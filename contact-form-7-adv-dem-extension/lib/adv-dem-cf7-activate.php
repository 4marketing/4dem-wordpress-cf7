<?php
function adv_dem_cf7_error() {
	if( !file_exists(WP_PLUGIN_DIR.'/contact-form-7/wp-contact-form-7.php') ) {
		$adv_dem_error_out = '<div id="message" class="error is-dismissible"><p>';
		$adv_dem_error_out .= __('The Contact Form 7 plugin must be installed for the <b>4Dem.it Extension</b> to work.' , ADV_DEM_CF7_TEXTDOMAIN) . ' <b><a href="'.admin_url('plugin-install.php?tab=plugin-information&plugin=contact-form-7&from=plugins&TB_iframe=true&width=600&height=550').'" class="thickbox" title="Contact Form 7">' . __('Install Contact Form 7 Now.', ADV_DEM_CF7_TEXTDOMAIN) . '</a></b>';
		$adv_dem_error_out .= '</p></div>';
		echo $adv_dem_error_out;
	}
	else if ( !class_exists( 'WPCF7') ) {
		$adv_dem_error_out = '<div id="message" class="error is-dismissible"><p>';
		$adv_dem_error_out .= __('The Contact Form 7 is installed, but <b>you must activate Contact Form 7</b> below for the <b>4Dem.it Extension</b> to work.', ADV_DEM_CF7_TEXTDOMAIN);
		$adv_dem_error_out .= '</p></div>';
		echo $adv_dem_error_out;
	}
	
}
add_action('admin_notices', 'adv_dem_cf7_error');

function adv_dem_cf7_act_redirect( $plugin ) {
	if( $plugin == ADV_DEM_CF7_PLUGIN_BASENAME ) {
		exit( wp_redirect( admin_url( 'admin.php?page=wpcf7&post='.adv_dem_cf7_get_latest_item().'&active-tab=4' ) ) );
	}
	delete_option('adv_dem_cf7_show_notice', 0);
	update_site_option('adv_dem_cf7_show_notice', 1);
}
add_action( 'activated_plugin', 'adv_dem_cf7_act_redirect' );

if (get_site_option('adv_dem_cf7_show_notice') == 1){
	function adv_dem_cf7_show_update_notice() {
		if(!current_user_can( 'manage_options')) return;
		$class = 'notice is-dismissible vc-notice welcome-panel';
		$message = '<h2>'.ADV_DEM_CF7_AGENCY_NAME.' '.esc_html__('Extension has been improved!', ADV_DEM_CF7_TEXTDOMAIN).'</h2>';
		global $wp_version;
		if( version_compare($wp_version, '4.2') < 0 ){
			$message .= ' | <a id="adv-dem-dismiss-notice" href="javascript:adv_dem_dismiss_notice();">'.__('Dismiss this notice.', ADV_DEM_CF7_TEXTDOMAIN).'</a>';
		}
		echo '<div id="adv-dem-notice" class="'.$class.'"><div class="welcome-panel-content">'.$message. '</div></div>';
		echo "<script>
        function adv_dem_dismiss_notice(){
          var data = {
          'action': 'adv_dem_dismiss_notice',
          };
          jQuery.post(ajaxurl, data, function(response) {
            jQuery('#adv-dem-notice').hide();
          });
        }
        jQuery(document).ready(function(){
          jQuery('body').on('click', '.notice-dismiss', function(){
            adv_dem_dismiss_notice();
          });
        });
        </script>";
	}
	
	if(is_multisite()){
		add_action( 'network_admin_notices', 'adv_dem_cf7_show_update_notice' );
	}
	else {
		add_action( 'admin_notices', 'adv_dem_cf7_show_update_notice' );
	}
	add_action( 'wp_ajax_adv_dem_cf7_dismiss_notice', 'adv_dem_cf7_dismiss_notice' );
	
	function adv_dem_cf7_dismiss_notice() {
		$result = update_site_option('adv_dem_cf7_show_notice', 0);
		return $result;
		wp_die();
	}
	
}

