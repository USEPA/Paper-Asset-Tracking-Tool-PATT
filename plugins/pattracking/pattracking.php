<?php 
/**
 * Plugin Name: pattracking
 * Description: add-on to the support candy plugin specifically for the EPA Paper Asset Tracking Tool
 * Version: 0.2.1
 * Requires at least: 4.4
 * Tested up to: 5.3
 * Text Domain: pattracking
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'Patt_Tracking' ) ) :
 
  final class Patt_Tracking {
  
      
    public $version    = '0.2.1';
    public $db_version = '2.0';
    
    public function __construct() {
        
        // define global constants
        $this->define_constants();
        
        // Include required files and classes
        $this->includes();

        add_action( 'init', array($this,'load_textdomain') );
        
        /*
         * Cron setup
         */
        $cron_job_schedule = get_option('wppatt_cron_job_schedule_setup');
        if($cron_job_schedule) {
          add_filter('cron_schedules',array( $this, 'wppatt_cron_schedule'));
          if (!wp_next_scheduled('wppatt_cron_job_schedules')) {
              wp_schedule_event(time(), 'wppatt5min', 'wppatt_cron_job_schedules');
          }
          
          include( wppatt_ABSPATH.'includes/class-wp-cron.php' );
          $cron=new wppattWPCron();
          add_action( 'wppatt_cron_job_schedules', array( $cron, 'wppatt_cron_job'));
        }   
        
        /**
         * Attachment restructure
         */
        $restuct_attach = get_option('wppatt_restructured_attach_completed', 0);
        if( !$restuct_attach ){
          if (! wp_next_scheduled ( 'wppatt_attachment_restructure')) {
            wp_schedule_event( time(), 'hourly', 'wppatt_attachment_restructure');
          }
        }
    }
    
    function define_constants() {
        $this->define('WPPATT_STORE_URL', 'https://supportcandy.net');
        $this->define('WPPATT_PLUGIN_FILE', __FILE__);
        $this->define('WPPATT_ABSPATH', dirname(__FILE__) . '/');
        $this->define('WPPATT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        $this->define('WPPATT_PLUGIN_BASENAME', plugin_basename(__FILE__));
        $this->define('WPPATT_VERSION', $this->version);
        $this->define('WPPATT_DB_VERSION', $this->db_version);
    }
    
    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'pattracking' );
        load_textdomain( 'pattracking', WP_LANG_DIR . '/supportcandy/supportcandy-' . $locale . '.mo' );
        load_plugin_textdomain( 'pattracking', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }
    
    public function includes() {
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-abstraction.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-custom-function.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-hooks-filters.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-install.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-ajax.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-functions.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-actions.php' );
        include_once( WPPATT_ABSPATH . 'includes/rest_api/class-rest-child.php' );
        include_once( WPPATT_ABSPATH . 'includes/admin/tickets/ticket_list/filter_get_ticket_list.php' ); 
        $frontend  = new wppatt_Functions();
        // Add PATT Query Shortcode
        add_shortcode('wppattquery', array($frontend, 'get_id_details'));
        // Add Shipping CRON
        add_action( 'wppatt_shipping_cron', array($frontend, 'wpatt_shipping_cron_schedule'));
        // Add Recall Shipping CRON
        add_action( 'wppatt_shipping_cron_recall', array($frontend, 'wppatt_recall_shipping_status_schedule'));
        // Add Return Shipping CRON
        add_action( 'wppatt_shipping_cron_return', array($frontend, 'wppatt_return_shipping_status_schedule'));
        // Add ECMS CRON
        add_action( 'wppatt_ecms_cron', array($frontend, 'wpatt_ecms_cron_schedule')); 
        
        if ($this->is_request('admin')) {
          include_once( WPPATT_ABSPATH . 'includes/class-wppatt-admin.php' );
          
          update_option('wpsc_tl_agent_unresolve_statuses',array(3,4,670,5,63,64,672,671,65));
          update_option('wpsc_tl_customer_unresolve_statuses',array(3,4,670,5,63,64,672,671,65));

          update_option('wpsc_close_ticket_group',array(673,674,743,66,67,68,69));
          
          // PDF Label Add Button
          $backend  = new wppatt_Admin();
          add_action('wpsc_after_indidual_ticket_action_btn', array($backend, 'box_status_assignment_btnAfterClone'));
          add_action('wpsc_after_indidual_ticket_action_btn', array($backend, 'pdflabel_btnAfterClone'));
          add_action('wp_ajax_wpsc_get_pdf_label_field', array($backend, 'get_pdf_label_field'));
          
          // Add Box Details to Request page
          add_action('wpsc_before_request_id', array($backend, 'request_boxes_BeforeRequestID'));
 
          // Hide long logs on Request page
          add_action('wpsc_after_individual_ticket', array($backend, 'request_hide_logs'));
          
          // Add Shipping Widget
          add_action( 'wpsc_after_ticket_widget', array($backend, 'shipping_widget'));
          add_action('wp_ajax_wpsc_get_shipping_details', array($backend, 'get_shipping_details'));
          
          // Add Inventory Modal
          add_action('wp_ajax_wpsc_get_inventory_editor', array($backend, 'get_inventory_editor'));

          // Add Digitization Switch Modal
          add_action('wp_ajax_wpsc_get_digitization_editor_final', array($backend, 'get_digitization_editor'));

          // Add Folder/File Editor Modal
          add_action('wp_ajax_wpsc_get_folderfile_editor', array($backend, 'get_folder_file_editor'));
          
          // Add Box Editor Modal
          add_action('wp_ajax_wpsc_get_box_editor', array($backend, 'get_box_editor'));

          // Add RFID Reader Modal
          add_action('wp_ajax_wpsc_get_clear_rfid', array($backend, 'get_clear_rfid'));
          add_action('wp_ajax_wpsc_get_rfid_box_editor', array($backend, 'get_rfid_box_editor'));
          
          // Add Shipping Modal to Shipping Status Editor
          add_action('wp_ajax_wpsc_get_shipping_sse', array($backend, 'get_alert_replacement'));	

          // Add Destruction Completed Modal to Box Dashboard	
          add_action('wp_ajax_wpsc_get_destruction_completed_b', array($backend, 'get_alert_replacement'));	
          	
          // Add Unathorized Destruction Modal to Box Details	
          add_action('wp_ajax_wpsc_get_unauthorized_destruction_bd', array($backend, 'get_alert_replacement'));	
          	
          // Add Freeze Modal to Box Details	
          add_action('wp_ajax_wpsc_get_freeze_bd', array($backend, 'get_alert_replacement'));	
          	
          // Add Validate Modal to Box Details	
          add_action('wp_ajax_wpsc_get_validate_bd', array($backend, 'get_alert_replacement'));	
            	
          // Add Validate Modal on Folder File Dashboard	
          add_action('wp_ajax_wpsc_get_validate_ff', array($backend, 'get_alert_replacement'));	
    	  
    	  // Add Re-Scan Modal on Folder File Dashboard	
          add_action('wp_ajax_wpsc_get_rescan_ff', array($backend, 'get_alert_replacement'));	
    	
          // Add Freeze Modal on Folder File Dashboard	
          add_action('wp_ajax_wpsc_get_freeze_ff', array($backend, 'get_alert_replacement'));	
          	
          // Add Unauthorized Destruction Modal on Folder File Dashboard	
          add_action('wp_ajax_wpsc_unauthorized_destruction_ff', array($backend, 'get_alert_replacement'));	
          	
          // Add Validation Rescan to Folder File Details	
          add_action('wp_ajax_wpsc_get_rescan_ffd', array($backend, 'get_alert_replacement'));
          
          // Add Validation Modal to Folder File Details	
          add_action('wp_ajax_wpsc_get_validate_ffd', array($backend, 'get_alert_replacement'));	
          	         	
          // Add Unauthorized Destruction to Folder File Details	
          add_action('wp_ajax_wpsc_unauthorized_destruction_ffd', array($backend, 'get_alert_replacement'));	
          	
          // Add Freeze to Folder File Details	
          add_action('wp_ajax_wpsc_get_freeze_ffd', array($backend, 'get_alert_replacement'));
          
          // Add Validate Modal on Folder File Dashboard	
          add_action('wp_ajax_wpsc_delete_request', array($backend, 'get_alert_replacement'));	
          
          // Disable Show Agent Settings Button
          add_action('wpsc_show_agent_setting_button',false);
          
          // Add Recall Search ID functionality  
          add_action('wp_ajax_wppatt_recall_search_id', array($backend, 'recall_search_for_id'));
          
          // Add Recall Submit  
          add_action('wp_ajax_wppatt_recall_submit', array($backend, 'recall_submit'));
          
          // Add Recall Edit Shipping Modal  
          add_action('wp_ajax_wppatt_recall_get_shipping', array($backend, 'recall_get_shipping'));
          
          // Add Recall Edit Requestor Modal 
          add_action('wp_ajax_wppatt_recall_get_requestor', array($backend, 'recall_get_requestor'));
          
          // Add Recall Edit Request Date Modal 
          add_action('wp_ajax_wppatt_recall_get_date', array($backend, 'recall_get_date'));
          
          // Add Recall Edit Status Change Modal - Feature removed as state machine is automatic, not manual. 
          //add_action('wp_ajax_wppatt_recall_status_change', array($backend, 'recall_status_change'));
          
          // Add Recall Edit Shipping Multiple Items Modal 
          add_action('wp_ajax_wppatt_recall_shipping_change', array($backend, 'recall_edit_multi_shipping'));
          
          // Add Recall Setting Pill 
          add_action('wpsc_after_setting_pills', array($frontend, 'recall_settings_pill'));
          
          // Add Recall Get Recall Settings Pill 
          add_action('wp_ajax_wppatt_get_recall_settings', array($backend, 'get_recall_settings'));
          
          // Add Recall Set Recall Settings Pill 
          //add_action('wp_ajax_wppatt_set_recall_settings', array($backend, 'set_recall_settings'));
          
          // Add Recall Status Settings Edit Modal
          add_action('wp_ajax_wppatt_get_edit_recall_status', array($backend, 'get_edit_recall_status')); 
          
          // Add Set Recall Status Settings via Modal
          add_action('wp_ajax_wppatt_set_recall_status', array($backend, 'set_recall_status')); 
                    
          // Add Return Edit Returned 
          //add_action('wp_ajax_wppatt_initiate_return', array($backend, 'ticket_initiate_return'));
          
          // Add Recall Cancel Modal 
          add_action('wp_ajax_wppatt_recall_cancel', array($backend, 'recall_cancel')); 
          
          // Add Return Submit
          add_action('wp_ajax_wppatt_return_submit', array($backend, 'return_submit')); 
          
		  // Add Return Setting Pill 
          add_action('wpsc_after_setting_pills', array($frontend, 'return_settings_pill'));
          
          // Add Return Get Recall Settings Pill 
          add_action('wp_ajax_wppatt_get_return_settings', array($backend, 'get_return_settings'));
          
          // Add Return Status Settings Edit Modal
          add_action('wp_ajax_wppatt_get_edit_return_status', array($backend, 'get_edit_return_status')); 
          
          // Add Set Return Status Settings via Modal
          add_action('wp_ajax_wppatt_set_return_status', array($backend, 'set_return_status'));   
          
          // Add Return Cancel Modal 
          add_action('wp_ajax_wppatt_return_cancel', array($backend, 'return_cancel')); 
          
		      // Add Box Status Setting Pill 
          add_action('wpsc_after_setting_pills', array($frontend, 'box_settings_pill'));
          
          // Add Box Status Get Settings Panel 
          add_action('wp_ajax_wppatt_get_box_settings', array($backend, 'get_box_settings'));
          
          // Add Box Status Settings Edit Modal
          add_action('wp_ajax_wppatt_get_edit_box_status', array($backend, 'get_edit_box_status')); 
          
          // Add Set Box Status Settings via Modal
          add_action('wp_ajax_wppatt_set_box_status', array($backend, 'set_box_status')); 
		  
		  // Add Edit Shipping Modal 
          add_action('wp_ajax_wppatt_change_shipping', array($backend, 'change_shipping'));
          
		      // Add Assign Agents Modal 
          add_action('wp_ajax_wppatt_assign_agents', array($backend, 'edit_assign_agents'));
          
		      // Add Change Box Status Modal 
          add_action('wp_ajax_wppatt_change_box_status', array($backend, 'change_box_status'));          
		  
          // Set Barcode Scanning Page
          add_action( 'wpsc_add_admin_page', 'epa_admin_menu_items');
          
          function epa_admin_menu_items() {
            add_submenu_page( 'wpsc-tickets', 'Barcode Scanning', 'Barcode Scanning', 'wpsc_agent', 'scanning', 'scanning_page' );
            add_submenu_page( 'wpsc-tickets', 'RFID Dashboard', 'RFID Dashboard', 'wpsc_agent', 'rfid', 'rfid_page' );
            }
            
          function scanning_page(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/scanning.php' );
            }
            
          // Set Box and File Dashboard and Details Pages
          add_action( 'wpsc_add_submenu_page', 'main_menu_items');

          function main_menu_items() {
            add_submenu_page( '', '', '', 'wpsc_agent', 'request_delete', 'request_delete_page' );
            add_submenu_page( 'wpsc-tickets', 'Box Dashboard', 'Box Dashboard', 'wpsc_agent', 'boxes', 'boxes_page' );
            add_submenu_page( 'wpsc-tickets', 'Folder/File Dashboard', 'Folder/File Dashboard', 'wpsc_agent', 'folderfile', 'folderfile_page' );
            add_submenu_page( '', '', '', 'wpsc_agent', 'boxdetails', 'box_details' );
            add_submenu_page( '', '', '', 'wpsc_agent', 'filedetails', 'file_details' );
            add_submenu_page( 'wpsc-tickets', 'Recall Dashboard', 'Recall Dashboard', 'wpsc_agent', 'recall', 'recall_page' ); 
            add_submenu_page( '', '', '', 'wpsc_agent', 'recalldetails', 'recall_details' ); 
            add_submenu_page( '', '', '', 'wpsc_agent', 'recallcreate', 'recall_create' ); 
            add_submenu_page( 'wpsc-tickets', 'Return Dashboard', 'Return Dashboard', 'wpsc_agent', 'return', 'return_page' ); 
            add_submenu_page( '', '', '', 'wpsc_agent', 'returndetails', 'return_details' ); 
            add_submenu_page( '', '', '', 'wpsc_agent', 'returncreate', 'return_create' ); 
            add_submenu_page( 'wpsc-tickets', 'Shipping Status Editor', 'Shipping Status Editor', 'edit_posts', 'shipping', 'shipping_page' ); 
            }

            function shipping_page(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/shipping.php'
            );
            }
            
            function request_delete_page(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/request_delete.php'
            );
            }
            
            function boxes_page(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/boxes.php'
            );
            }
            
            function folderfile_page(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/folderfile.php'
            );
            }
            
            function rfid_page(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/rfid.php'
            );
            }
            
            function box_details(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/box-details.php'
            );
            }
            
            function file_details(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/folder-file-details.php'
            );
            }
            
            function inventory_test(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/test_inventory.php'
            );
            }
            

            function recall_page(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/recall.php'
            );
            }
            
            function recall_details(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/recall-details.php'
            );
            }
            
            function recall_create(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/recall-create.php'
            );
            }
            
            function return_page(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/return.php'
            );
            }
            
            function return_details(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/return-details.php'
            );
            }
            
            function return_create(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/return-create.php'
            );
            }

            include_once( WPPATT_ABSPATH . 'includes/class-wppatt-request-approval-widget.php' );
            include_once( WPPATT_ABSPATH . 'includes/class-wppatt-new-request-litigation-letter.php' );

    
        }
        if ($this->is_request('frontend')) {
          include_once( WPPATT_ABSPATH . 'includes/class-wppatt-frontend.php' );
        }
        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
          include_once( WPPATT_ABSPATH . 'includes/EDD_SL_Plugin_Updater.php' );
        }
        
    }
    
    private function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }
    
    private function is_request($type) {
        switch ($type) {
            case 'admin' :
                return is_admin();
            case 'frontend' :
                return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
        }
    }
    
    function wppatt_cron_schedule($schedules){
        if(!isset($schedules["wppattsc5min"])){
            $schedules["wppatt5min"] = array(
                'interval' => 5*60,
                'display'  => 'Once every 5 minute',
            );
        }
        return $schedules;
    }
    
  }
  
endif;

new Patt_Tracking();
