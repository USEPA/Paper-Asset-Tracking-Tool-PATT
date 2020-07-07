<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_SLA' ) ) :
  
  final class WPSC_SLA {
		
		public function __construct() {
	    add_action( 'admin_enqueue_scripts', array( $this, 'loadScripts') );
	   }

	   public function loadScripts(){
	      wp_enqueue_script('jquery');
	      wp_enqueue_script('jquery-ui-core');
	      wp_enqueue_script('wpsc-sla-admin', WPSC_SLA_PLUGIN_URL.'asset/js/admin.js?version='.WPSC_SLA_VERSION, array('jquery'), null, true);
				wp_enqueue_style('wpsc-sla-css', WPSC_SLA_PLUGIN_URL . 'asset/css/public.css?version='.WPSC_SLA_VERSION );
	  } 
    
    // Add new menu WPSC settings.
    function sla_setting_pill(){
      include WPSC_SLA_ABSPATH . 'includes/sla_setting_pill.php';
    }
    
    // Get SLA sett
    function get_sla_settings(){
      include WPSC_SLA_ABSPATH . 'includes/get_sla_settings.php';
      die();
    }
		
		// Set SLA sett
    function set_sla_settings(){
      include WPSC_SLA_ABSPATH . 'includes/set_sla_settings.php';
      die();
    }
		
		// Get add SLA policy
    function get_add_sla_policy(){
      include WPSC_SLA_ABSPATH . 'includes/get_add_sla_policy.php';
      die();
    }
		
		// set add SLA policy
    function set_add_sla_policy(){
      include WPSC_SLA_ABSPATH . 'includes/set_add_sla_policy.php';
      die();
    }
		
		// Get edit SLA policy
    function get_edit_sla_policy(){
      include WPSC_SLA_ABSPATH . 'includes/get_edit_sla_policy.php';
      die();
    }
		
		// Set edit SLA policy
    function set_edit_sla_policy(){
      include WPSC_SLA_ABSPATH . 'includes/set_edit_sla_policy.php';
      die();
    }
		
		// set SLA order
    function set_sla_order(){
      include WPSC_SLA_ABSPATH . 'includes/set_sla_order.php';
      die();
    }
		
		// delete SLA policy
    function delete_sla_policy(){
      include WPSC_SLA_ABSPATH . 'includes/delete_sla_policy.php';
      die();
    }
		
		// Print SLA in Ticket List
		function print_tl_sla($ticket_list){
      include WPSC_SLA_ABSPATH . 'includes/print_tl_sla.php';
    }
		
		// SLA Checkout for create ticket
		function sla_checkout_create_ticket($ticket_id){
      include WPSC_SLA_ABSPATH . 'includes/sla_checkout.php';
    }
		
		// SLA Checkout for submit reply
		function sla_checkout_submit_reply($thread_id, $ticket_id){
      include WPSC_SLA_ABSPATH . 'includes/sla_checkout.php';
    }
		
		// SLA Checkout for change status
		function sla_checkout_change_status($ticket_id, $texonomy_id){
      include WPSC_SLA_ABSPATH . 'includes/sla_checkout.php';
    }
		
		// SLA Checkout for change field
		function sla_checkout_change_field($ticket_id, $fields_slug, $fields_value){
      include WPSC_SLA_ABSPATH . 'includes/sla_checkout.php';
    }
		
		// Add-on installed or not for licensing
		function is_add_on_installed($flag){
			return true;
		}
		
		// Print license functionlity for this add-on
		function addon_license_area(){
			include WPSC_SLA_ABSPATH . 'includes/addon_license_area.php';
		}
		
		// Activate SLA license
		function license_activate(){
			include WPSC_SLA_ABSPATH . 'includes/license_activate.php';
      die();
		}
		
		// Deactivate SLA license
		function license_deactivate(){
			include WPSC_SLA_ABSPATH . 'includes/license_deactivate.php';
      die();
		}
		
		//Upgrade SLA
		function upgrade_sla() {
			include WPSC_SLA_ABSPATH . 'includes/get_upgrade_sla.php';
      die();
		}
    
		function notification_types($notification_types) {
			$notification_types['out_of_sla']   = __('Out of SLA','wpsc-sla');
			return $notification_types;
		}
		
		function out_of_sla_email() {
			include WPSC_SLA_ABSPATH . 'includes/out_of_sla_email.php';
		}
		
		/**
		 *  Add meta key
		 */
		
		function wpsc_get_all_meta_keys($meta_key){
			$meta_key[] = 'sla';
			$meta_key[] = 'sla_term';
			return $meta_key;
		}
		
		/*
			Filter Autocomplete
		*/
		
		function filter_autocomplete( $output,$term,$field_slug ){
			include WPSC_SLA_ABSPATH . 'includes/filter_autocomplete.php';
			return $output;
		}
		/*
			Get Filter val label
		 */
		function filter_val_label($val,$field_slug) {
			include WPSC_SLA_ABSPATH . 'includes/filter_val_label.php';
			return $val;
		}
		/*
		Add ticket meta
		 */
		function add_ticket_meta($flag,$field_slug){
			if($field_slug=='sla') {
				return false;
			}else {
				return true;
			}
		}
		/*
		Get ticket meta Query
 		*/
		function get_tickets_meta($meta_query,$field_slug,$custom_filter){
		
			include WPSC_SLA_ABSPATH . 'includes/get_tickets_meta.php';
			return $meta_query;  
		}

		/**
		 *  Replace macro
		 */
		
		function wpsc_replace_macro($str,$ticket_id){

			if(strpos($str,'{sla}') !== false){
				
				global $wpscfunction;
				$sla = $wpscfunction->get_ticket_meta($ticket_id,'sla',true);
				if($sla == '3099-01-01 00:00:00'){
					$sla_status = '';
				}else{
					$now  = new DateTime;
					$ago  = new DateTime($sla);
					$diff = $now->diff($ago);
				
					if($diff->invert){
						$sla_status = __('Out OF SLA','wpsc-sla');
					}else{
						$sla_status = __('IN SLA','wpsc-sla');
					}
				}
			
				$str = preg_replace('/{sla}/',$sla_status, $str);
			}
			
			return $str;
		}
		
		// add submenu in report 
		function sla_overdue_ticket_graph(){
			?>
 		 <li id="wpsc_rp_sla_reports" role="presentation"><a href="javascript:get_sla_overdue_report();"><?php _e('SLA','wpsc-sla');?></a></li>
 		 <?php
		}
		
		// sla graph 
		function get_sla_overdue_report(){
			include WPSC_SLA_ABSPATH . 'includes/sla_graph/get_sla_overdue_report.php';
			die();
		}
		
		// sla graph by filter
		function sla_reports_by_filter(){
			include WPSC_SLA_ABSPATH . 'includes/sla_graph/sla_reports_by_filter.php';
			die();
		}
		
		// add in sla report table after out of sla 
		function after_ticket_out_of_sla(){
			include WPSC_SLA_ABSPATH . 'includes/after_ticket_out_of_sla.php';
		}
		
		function sla_dashboard_report(){
			include WPSC_SLA_ABSPATH . 'includes/dashboard_graph/sla_dashboard_report.php';
		} 

		// after 3 failed attemp of mail sent or after succesfully mail sent
		function after_mail_sent($email){
			global $wpscfunction;
			if($email->email_type == 'out_of_sla'){
				$wpscfunction->delete_ticket_meta($email->ticket_id,'wpsp_out_of_sla_email_send');
			    $wpscfunction->add_ticket_meta($email->ticket_id,'wpsp_out_of_sla_email_send',1);
			}
		}
  }
  
endif;