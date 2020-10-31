<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'wppatt_Functions' ) ) :
  
  final class wppatt_Functions {
      
    // Shortcode Query Component
    public function get_id_details(){    
    include WPPATT_ABSPATH . 'includes/admin/get_id_details.php';
    }
    
    // CRON for shipping
    public function wpatt_shipping_cron_schedule(){    
    include WPPATT_ABSPATH . 'includes/admin/shipping_cron.php';
    }
    
    // CRON for Recall Status Change from Shipping status
    public function wppatt_recall_shipping_status_schedule(){    
    include WPPATT_ABSPATH . 'includes/admin/recall_shipping_status_cron.php';
    }
    
    // CRON for Return Status Change from Shipping status
    public function wppatt_return_shipping_status_schedule(){    
    include WPPATT_ABSPATH . 'includes/admin/return_shipping_status_cron.php';
    }   
    
    // CRON for ecms
    public function wpatt_ecms_cron_schedule(){    
    include WPPATT_ABSPATH . 'includes/admin/ecms_cron.php';
    }

    // CRON for eidw
    public function wpatt_eidw_cron_schedule(){    
    include WPPATT_ABSPATH . 'includes/admin/eidw_cron.php';
    }   

    // CRON for message cleanup
    public function wppatt_private_message_cleanup(){    
    include WPPATT_ABSPATH . 'includes/admin/private_message_cleanup_cron.php';
    }   
    
    public function addStyles(){    
        wp_register_style('wpsc-bootstrap-css', WPSC_PLUGIN_URL.'asset/css/bootstrap-iso.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-fa-css', WPSC_PLUGIN_URL.'asset/lib/font-awesome/css/all.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-jquery-ui', WPSC_PLUGIN_URL.'asset/css/jquery-ui.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-public-css', WPSC_PLUGIN_URL . 'asset/css/public.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-admin-css', WPSC_PLUGIN_URL . 'asset/css/admin.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-modal-css', WPSC_PLUGIN_URL . 'asset/css/modal.css?version='.WPSC_VERSION );

        wp_enqueue_style('wpsc-bootstrap-css');
        wp_enqueue_style('wpsc-fa-css');
        wp_enqueue_style('wpsc-jquery-ui');
        wp_enqueue_style('wpsc-public-css');
        wp_enqueue_style('wpsc-admin-css');
        wp_enqueue_style('wpsc-modal-css');
    }
    
    // Add settings pill for recall statuses 
    public function recall_settings_pill(){
	    include WPPATT_ABSPATH . 'includes/admin/pages/recall_settings_pill.php';    
    }  
    
    // Add settings pill for return statuses 
    public function return_settings_pill(){
	    include WPPATT_ABSPATH . 'includes/admin/pages/return_settings_pill.php';    
    }  
    
    // Add settings pill for box statuses 
    public function box_settings_pill(){
	    include WPPATT_ABSPATH . 'includes/admin/pages/box_settings_pill.php';    
    }      
    
}  
endif;

$GLOBALS['wppattfunction'] =  new wppatt_Functions();
