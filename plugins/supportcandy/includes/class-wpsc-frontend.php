<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Frontend' ) ) :
  
  final class WPSC_Frontend {
    
    // constructor
    public function __construct() {
      add_action( 'wp_enqueue_scripts', array( $this, 'loadScripts') );
      add_shortcode( 'supportcandy', array( $this, 'supportcandy' ) );
      add_shortcode( 'wpsc_create_ticket', array( $this, 'wpsc_create_ticket' ) );
      add_shortcode( 'wpsc_unresolved_ticket_count', array( $this, 'wpsc_unresolved_ticket_count' ) );
      add_action( 'wp_footer', array( $this, 'responsive_style'), 99999999 );
    }
    
    // Load admin scripts
    public function loadScripts(){
      
        //bootstrap
        wp_register_style('wpsc-bootstrap-css', WPSC_PLUGIN_URL.'asset/css/bootstrap-iso.css?version='.WPSC_VERSION );
        //Font-Awesom
        wp_register_style('wpsc-fa-css', WPSC_PLUGIN_URL.'asset/lib/font-awesome/css/all.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-jquery-ui', WPSC_PLUGIN_URL.'asset/css/jquery-ui.css?version='.WPSC_VERSION );
        //admin scripts
        wp_register_script('wpsc-admin', WPSC_PLUGIN_URL.'asset/js/admin.js?version='.WPSC_VERSION, array('jquery'));
        wp_register_script('wpsc-public', WPSC_PLUGIN_URL.'asset/js/public.js?version='.WPSC_VERSION, array('jquery'));
        wp_register_script('wpsc-modal', WPSC_PLUGIN_URL.'asset/js/modal.js?version='.WPSC_VERSION, array('jquery'));
        wp_register_style('wpsc-public-css', WPSC_PLUGIN_URL . 'asset/css/public.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-admin-css', WPSC_PLUGIN_URL . 'asset/css/admin.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-modal-css', WPSC_PLUGIN_URL . 'asset/css/modal.css?version='.WPSC_VERSION );
        
        //Datetime picker
        $wpsc_allow_datepickerjs_files = get_option('wpsc_allow_datepickerjs_files');

        if(!$wpsc_allow_datepickerjs_files){
          wp_register_script('wpsc-dtp-js', WPSC_PLUGIN_URL.'asset/lib/datetime-picker/jquery-ui-timepicker-addon.js?version='.WPSC_VERSION, array('jquery'), null, true);
          wp_register_style('wpsc-dtp-css', WPSC_PLUGIN_URL . 'asset/lib/datetime-picker/jquery-ui-timepicker-addon.css?version='.WPSC_VERSION );
        }

        //localize script
        $loading_html = '<div class="wpsc_loading_icon"><img src="'.WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif"></div>';
        $localize_script_data = apply_filters( 'wpsc_admin_localize_script', array(
            'ajax_url'             => admin_url( 'admin-ajax.php' ),
            'loading_html'         => $loading_html,
            'agent_setting'        => __('Agent Setting','supportcandy'),
            'change_ticket_status' => __('Change Ticket Status','supportcandy'),
            'delete_tickets'       => __('Delete Tickets','supportcandy'),
            'assign_agent'         => __('Assign Agent','supportcandy'),
            'change_ticket_status' => __('Change Ticket Status','supportcandy'),
            'edit_this_thread'     => __('Edit This Thread','supportcandy'),
            'close_ticket'         => __('Close Ticket','supportcandy'),
            'clone_ticket'         => __('Clone Ticket','supportcandy'),
            'delete_this_thread'   => __('Delete This Thread','supportcandy'),
            'edit_subject'         => __('Edit Subject','supportcandy'),
            'are_you_sure'         => __('Are you sure?','supportcandy'),
            'templates'            => __('Templates','supportcandy'),
            'asc'                  =>   'ASC',
            'desc'                 =>   'DESC',
            'change_agent_fields'  => __('Change Agent Fields','supportcandy'),
            'restore_ticket'       => __('Restore Ticket','supportcandy'),
            'change_raised_by'     => __('Change Raised By','supportcandy'),
            'view_more'            => __('View More','supportcandy'),
            'view_less'            => __('View Less','supportcandy'), 
            'create_thread_ticket' => __('Create Ticket From Thread','supportcandy'),
            'view_less'            => __('View Less','supportcandy'),
            'extra_thread_info'    => __('Thread Info','supportcandy'),
            'extra_ticket_info'    => __('Ticket Info ','supportcandy'),
            'users_all_tickets'    => __('All Tickets','supportcandy'),
            'create_thread_ticket' => __('Create Ticket From Thread','supportcandy'),
            'warning_message'      => __('There is unposted text in the reply area. Are you sure you want to discard and proceed?','supportcandy'),
            'additional_recipients'  => __('Recipients','supportcandy'),
            'customer_name'        => __('Please insert customer name','supportcandy'),
            'customer_email'       => __('Please insert customer email','supportcandy'),
            'validate_email'       => __('Incorrect Email Address','supportcandy'),
            'edit_custom_filter'   => __('Edit Filter','supportcandy'),
            'filter_title'         => __('Please insert filter title','supportcandy'),
            'popup_wait'           => __('Please wait...','supportcandy'),
            'change_ticket_fields' => __('Change Ticket Fields','supportcandy'),
            'assign_agent'         => __('Assign Agent','supportcandy'),
            'delete_ticket'        => __('Delete Ticket','supportcandy')
        ));
        wp_localize_script( 'wpsc-public', 'wpsc_admin', $localize_script_data );
        
        do_action('wpsc_after_enqueue_script',$localize_script_data);
        
    }
    
    /**
     * Main shortcode
     */
    function supportcandy($attr){
      ob_start();
			$installed_db_version = get_option( 'wpsc_db_version', 1 );
      if( !($installed_db_version < WPSC_DB_VERSION) ){
        include WPSC_ABSPATH.'includes/frontend/shortcode.php';
      }
			return ob_get_clean();
    }
    
    /**
     * Adds styles based on container width
     */
    function responsive_style(){
      include WPSC_ABSPATH.'includes/responsive_style.php';
    }
    
    /**
     * Create ticket shortcode
     */
    function wpsc_create_ticket() {
      ob_start();
      $installed_db_version = get_option( 'wpsc_db_version', 1 );
      if( !($installed_db_version < WPSC_DB_VERSION) ){
        include WPSC_ABSPATH.'includes/frontend/create_ticket_shortcode.php';
      }
			return ob_get_clean();
    }
    
    function wpsc_unresolved_ticket_count(){
      ob_start();
      $installed_db_version = get_option( 'wpsc_db_version', 1 );
      if( !($installed_db_version < WPSC_DB_VERSION) ){
        include WPSC_ABSPATH.'includes/frontend/unresolved_ticket_count_shortcode.php';
      }
			return ob_get_clean();
    }
    
  }
  
endif;

new WPSC_Frontend();