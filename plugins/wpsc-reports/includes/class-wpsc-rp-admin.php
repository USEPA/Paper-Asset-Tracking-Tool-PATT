<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Reports_Admin	' ) ) :

  final class WPSC_Reports_Admin	 {
    public function __construct() {
      add_action( 'admin_enqueue_scripts', array( $this, 'loadScripts') );
    }

    public function loadScripts(){
      wp_enqueue_script('jquery');
      wp_enqueue_script('jquery-ui-core');
      wp_enqueue_script('wpsc_reports_admin', WPSC_RP_PLUGIN_URL.'asset/js/admin.js?version='.WPSC_RP_VERSION, array('jquery'), null, true);
      wp_enqueue_style('wpsc-report-css', WPSC_RP_PLUGIN_URL . 'asset/css/public.css?version='.WPSC_RP_VERSION );
      
      if($_REQUEST && isset($_REQUEST['page']) && $_REQUEST['page']=='wpsc-reports'){
        wp_enqueue_script('wpsc_chart_reports_admin	', WPSC_RP_PLUGIN_URL . 'asset/lib/chart-js/Chart.bundle.min.js?version='.WPSC_RP_VERSION );
        wp_enqueue_script('wpsc_chart_color_admin	', WPSC_RP_PLUGIN_URL . 'asset/lib/chart-js/utils.js?version='.WPSC_RP_VERSION );   
      }
      
      $loading_html = '<div class="wpsc_loading_icon"><img src="'.WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif"></div>';
      $localize_script_data = apply_filters( 'wpsc_admin_localize_script', array(
          'ajax_url'          => admin_url( 'admin-ajax.php' ),
          'loading_html'      => $loading_html,
      ));
      wp_localize_script( 'wpsc_reports_admin', 'wpsc_reports_data', $localize_script_data );
    }


    function rp_setting_pill(){
      include_once( WPSC_RP_ABSPATH . 'includes/rp_setting_pill.php' );
    }

    function load_settings(){
      global $wpscfunction, $current_user;
      $installed_db_version = get_option( 'wpsc_rp_db_version', 1 );
      if( !($installed_db_version < WPSC_RP_DB_VERSION) ){
        include WPSC_RP_ABSPATH . 'includes/all_reports.php';
      } else {
        include WPSC_RP_ABSPATH.'includes/db_upgrade/db_upgrade.php';
      }
      
    }
    
      // Ticket Stats 
    function get_ticket_stats_report(){
      include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/ticket_stats_report.php' );
      die();
    }
      
      //First Response time reports
    function get_first_response_time_reports(){
      include_once(WPSC_RP_ABSPATH . 'includes/first_response_time/get_first_response_time_reports.php');
      die();
    }
    
      // Calculate Fitst Response Time 
    function get_first_response_time($thread_id, $ticket_id){
      include_once(WPSC_RP_ABSPATH . 'includes/first_response_time/get_first_response_time.php');
    }    
    
      // Add-on installed or not for licensing
    function is_add_on_installed($flag){
			return true;
		}
    
    // Print license functionlity for this add-on
    function addon_license_area(){
      include_once(WPSC_RP_ABSPATH . 'includes/license/addon_license_area.php');
    }
    
    // Activate REPORTS license
    function license_activate(){
      include_once(WPSC_RP_ABSPATH . 'includes/license/license_activate.php');
      die();
    }
    
    // Deactivate REPORTS license
    function license_deactivate(){
      include_once(WPSC_RP_ABSPATH . 'includes/license/license_deactivate.php');
      die();
    }
    
    //Upgrade FRT
		function upgrade_frt() {
			include_once(WPSC_RP_ABSPATH . 'includes/first_response_time/upgrade_frt/get_upgrade_frt.php');
      die();
    }
    
    //Checked FRT
		function set_checked_frt($ticket_id) {
      global $wpscfunction;
      $wpscfunction->delete_ticket_meta($ticket_id,'frt_checked');
      $wpscfunction->add_ticket_meta($ticket_id,'frt_checked',1);
      $wpscfunction->delete_ticket_meta($ticket_id, 'ticket_counted');
      $wpscfunction->add_ticket_meta($ticket_id, 'ticket_counted', 1);
    }
    
    // Cron job 
    function wpsc_report_cron_job(){
      include WPSC_RP_ABSPATH . 'wpsc-report-cron.php';
    }
    
    // Get category report
    function get_category_report(){
      include WPSC_RP_ABSPATH . 'includes/category/get_category_report.php';
      die();
    }
    
    // Get all dropdown reports
    function get_all_dropdown_report(){
      include WPSC_RP_ABSPATH . 'includes/dropdown/get_all_dropdown_report.php';
      die();
    }
    
    // Get all check boxes reports
    function get_all_checkbox_report(){
      include WPSC_RP_ABSPATH . 'includes/checkbox/get_all_checkbox_report.php';
      die();
    }
    
    // Get all radio button reports 
    function get_all_radio_button_report(){
      include WPSC_RP_ABSPATH . 'includes/radio_button/get_all_radio_button_report.php';
      die();
    }
    
    // Get all active customers report
    function get_active_customers_report(){
      include WPSC_RP_ABSPATH . 'includes/active_customers/get_active_customers_report.php';
      die();
    }
    
    // Get report settings 
    function reports_setting_pill(){
      include WPSC_RP_ABSPATH . 'includes/reports_setting_pill.php';
    }
    
    // Setting UI
    function get_reports_settings(){
      include WPSC_RP_ABSPATH . 'includes/settings/get_reports_settings.php';
			die();
    }

		// Save Settings
		function save_settings(){
			include WPSC_RP_ABSPATH . 'includes/settings/save_settings.php';
      die();
		}
    
    // get dashboard report 
    function get_dashboard_report(){
      include WPSC_RP_ABSPATH . 'includes/dashboard/get_dashboard_report.php';
      die();
    }
    
    // filter for active customers table
    function get_active_customers_filter(){
      include WPSC_RP_ABSPATH . 'includes/active_customers/get_active_customers_filter.php';
    }
    
    // set default filter for active customers
    function set_active_customers_default_filter(){
      include WPSC_RP_ABSPATH . 'includes/active_customers/set_active_customers_default_filter.php';
      die();
    }
    
    /**
     * Database v2 upgrade process
     */
    public function run_db_v2_upgrade(){
      include WPSC_RP_ABSPATH . 'includes/db_upgrade/run_db_v2_upgrade.php';
      die();
    }
    
    // Recalculate First Response Time 
    function get_recalculate_first_response_time(){
      include WPSC_RP_ABSPATH . 'includes/first_response_time/get_recalculate_first_response_time.php';
      die();
    }
    
    function get_active_customers_settings(){
      include WPSC_RP_ABSPATH . 'includes/active_customers/get_active_customers_settings.php';
      die();
    }

    function get_upgrade_ticket(){
     include WPSC_RP_ABSPATH .'includes/ticket_statistics/upgrade_ticket/get_upgrade_ticket.php';
     die();
    }

    // Recalculate ticket stats
    function get_recalculate_ts(){
      include WPSC_RP_ABSPATH . 'includes/ticket_statistics/get_recalculate_ts.php';
      die();
    }
  }

endif;
