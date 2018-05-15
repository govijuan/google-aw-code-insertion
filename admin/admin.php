<?php

			/* For The admin to work */

			add_action('admin_menu', 'rb_roi_adm_menu');

			function rb_roi_adm_menu(){
				$page_title = "Configurações para ROI da Rocha Branca";
				$menu_title = "RB ROI Settings";
				$capability = "administrator";
				$menu_slug = "rb-roi-settings";
				$function = "rb_roi_settings_menu";

				//add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url);
				add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function);
			}



			function rb_roi_settings_menu(){
				if ( !current_user_can( 'manage_options' ) )  {
					wp_die( __( 'Você não tem permisão para acessar esta página com esse usuário.' ) );
				}
?>
				<style type="text/css">
					.rb-roi-panel-wrap th{
						font-weight: normal;
					}
					.save-info-wrap{
						position: fixed;
						z-index: 100;
						background-color: rgba(255,255,255, 0.9);
						padding: 4px 15px;
						line-height: 30px;
				    top: 41%;
				    border: 1px solid green;
				    left: 34%;
				    display: none;
				    height: 50px;
						min-width: 208px;
						text-align: center;
					}
					.save-info-wrap .dashicons-yes{
						color: green;
						margin: 0 10px 0 0;
						font-family: dashicons;
						font-size: 30px;
						display: inline-block;
						top: 10px;
						position: relative;
					}
				</style>
				<div class="rb-roi-panel-wrap wrap">
					<div class="title-wrap">
						<h1 class="wp-heading-inline"><?php echo get_admin_page_title();?></h1>
					</div>
					<hr class="wp-header-end">
					<div class="save-info-wrap"></div>
					<div class="metabox-holder">
						<div id="all-fileds" class="postbox-container column-1 normal">
							<div id="normal-sortables" class="meta-box-sortables ui-sortable">
								<div class="postbox">
									<button type="button" class="handlediv">
										<span class="screen-reader-text">Alternar painel: Códigos para Conversion Tracking</span>
										<span class="toggle-indicator" ></span>
									</button>
									<h2 class="hndle ui-sortable-handle"><span>Códigos para Conversion Tracking</span></h2>
									<div class="inside">
										<table class="form-table">
											<tbody>
												<tr valign="top">
													<th scope="row">AdWords Conversion ID</th>
													<td>
														<input type="text" id="g-conv-t-code" name="g-conv-t-code" value="<?php if(get_option( 'g_conv_t_code' ) != null) {echo get_option( 'g_conv_t_code' );} ?>">
													</td>
												</tr>
												<tr valign="top">
													<th scope="row">AdWords Conversion Label</th>
													<td>
														<input type="text" id="g-conv-t-label" name="g-conv-t-label" value="<?php if(get_option( 'g_conv_t_label' ) != null){ echo get_option( 'g_conv_t_label' );} ?>">
													</td>
												</tr>
												<tr valign="top"></tr>
											</tbody>
										</table>
									</div>
								</div>

								
								<p class="submit"><input type="submit" class="button button-primary" name="submit" value="Salvar alterações"></p>
							</div>
						</div>
					</div>
				</div>

<?php }


add_action('admin_footer', 'rb_roi_admin_js');

function rb_roi_admin_js(){
?>
	<script type="text/javascript">
			jQuery(document).ready(function($){
					$('.rb-roi-panel-wrap .submit .button').live('click', function(e){
						e.preventDefault();
						var g_conv_t_code = $('#g-conv-t-code').val();
						var g_conv_t_label = $('#g-conv-t-label').val();

						var data = {
							action: 'roi_settings_action',
							g_conv_t_code: g_conv_t_code,
							g_conv_t_label: g_conv_t_label,
						};

						$.post(ajaxurl, data, function(response){
							$('.save-info-wrap').html('Carregando...');
							$('.save-info-wrap').show(500);
						})
						.success(function(){})
						.error(function(){ alert("Erro de evio de dados");})
						.complete(function(){ $('.save-info-wrap').html('<span class="dashicons-yes"></span><span>Configurações Salvas!</span>').delay(6000).hide(2000); });
					});
			});
	</script>
<?php 	
}

add_action('wp_ajax_roi_settings_action', 'rb_roi_settings_action');

function rb_roi_settings_action(){
	$g_conv_t_code = $_POST['g_conv_t_code'];
	$g_conv_t_label = $_POST['g_conv_t_label'];

	update_option('g_conv_t_code', $g_conv_t_code);
	update_option('g_conv_t_label',  $g_conv_t_label);

	echo "Configurações Salvas";

	die;
}

	
function google_conversion_tracking() {
	if ( is_order_received_page() ) :
		global $wp;
		$order_id = isset( $wp->query_vars['order-received'] ) ? intval( $wp->query_vars['order-received'] ) : 0;
		$order = new WC_Order( $order_id );
		if ( $order && ! $order->has_status( 'failed' ) ) :
				$order_total = round($order->get_subtotal(),2);
		?>
		<!-- Google Code for Order_placed_dev Conversion Page -->
			<script type="text/javascript">
			/* <![CDATA[ */
				var google_conversion_id = <?php echo get_option('g_conv_t_code'); ?>;
				var google_conversion_label = "<?php echo get_option('g_conv_t_label');?>";
				var google_conversion_value = <?php echo $order_total?>;
				var google_conversion_currency = "BRL";
				var google_remarketing_only = false;
			/* ]]> */
			</script>
			<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
			</script>
			<noscript>
				<div style="display:inline;">
					<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/<?php echo get_option('g_conv_t_code')?>/?value=<?php echo $order_total;?>&amp;currency_code=BRL&amp;label=<?php echo get_option('g_conv_t_label');?>&amp;guid=ON&amp;scopeript=0"/>
				</div>
			</noscript> 

	
	
<?php endif; endif;
}
add_action( 'wp_footer', 'google_conversion_tracking' );
?>