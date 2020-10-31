<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_ATC' ) ) :

  final class WPSC_ATC {

    // Add new menu WPSC settings.
    function atc_setting_pill(){
      include WPSC_ATC_ABSPATH . 'includes/atc_setting_pill.php';
    }

    // Setting UI
    function get_atc_settings(){
      include WPSC_ATC_ABSPATH . 'includes/get_atc_settings.php';
			die();
    }

		// Save Settings
		function save_settings(){
			include WPSC_ATC_ABSPATH . 'includes/save_settings.php';
      die();
		}

		// Cron
		function atc_cron(){
			include WPSC_ATC_ABSPATH . 'includes/atc_cron.php';
		}
		
		function wpsc_after_submit_reply($thread_id, $ticket_id){
			include WPSC_ATC_ABSPATH . 'includes/wpsc_after_submit_reply.php';
		}
		
		//Add-on installed or not for licensing
		function is_add_on_installed($flag){
			return true;
		}
		
		// Print license functionlity for this add-on
		function addon_license_area(){
			include WPSC_ATC_ABSPATH . 'includes/addon_license_area.php';
		}
		
		// Activate SLA license
		function license_activate(){
			include WPSC_ATC_ABSPATH . 'includes/license_activate.php';
      die();
		}
		
		// Deactivate SLA license
		function license_deactivate(){
			include WPSC_ATC_ABSPATH . 'includes/license_deactivate.php';
      die();
		}

		// after 3 failed attemp of mail sent or after succesfully mail sent
		function after_mail_sent($email){
			global $wpscfunction;
			if($email->email_type == 'atc'){
				$wpscfunction->delete_ticket_meta($email->ticket_id,'wpsp_warning_email_send');
				$wpscfunction->add_ticket_meta($email->ticket_id,'wpsp_warning_email_send',1);
			}
		}

		function wpsc_after_en_setting_pills(){
			include WPSC_ATC_ABSPATH . 'includes/after_en_setting_pills.php';
		}

		function get_atc_ticket_notifications(){
			include WPSC_ATC_ABSPATH . 'includes/get_atc_ticket_notifications.php';	
			die();
		}

		function set_atc_ticket_notifications(){
			include WPSC_ATC_ABSPATH . 'includes/set_atc_ticket_notifications.php';	
			die();
		}

		function add_external_en_atc_setting_scripts(){
			?>
			<script>
				function wpsc_get_atc_ticket_notifications(){
  					jQuery('.wpsc_setting_pills li').removeClass('active');
  					jQuery('#wpsc_atc_ticket_notifications').addClass('active');
  					jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
 					var data = {
    					action: 'wpsc_get_atc_ticket_notifications'
  					};

  					jQuery.post(wpsc_admin.ajax_url, data, function(response) {
    					jQuery('.wpsc_setting_col2').html(response);
  					});
				}
			</script>
			<?php
		  }
		
	}

endif;
