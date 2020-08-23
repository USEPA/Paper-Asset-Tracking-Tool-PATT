<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Admin' ) ) :
  
  final class WPSC_Admin {
    
    // constructor
    public function __construct() {
      add_action( 'admin_enqueue_scripts', array( $this, 'loadScripts') );
      add_action( 'admin_menu', array($this,'register_dashboard_menu') );
      add_action( 'admin_footer', array( $this, 'responsive_style'), 99999999 );
    }
    
    // Load admin scripts
    public function loadScripts(){
      if(isset($_REQUEST['page']) && preg_match('/wpsc-/',$_REQUEST['page'])) :
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'), '1.8.6');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_editor();
        //bootstrap
        wp_enqueue_style('wpsc-bootstrap-css', WPSC_PLUGIN_URL.'asset/css/bootstrap-iso.css?version='.WPSC_VERSION );
        //Font-Awesom
        wp_enqueue_style('wpsc-fa-css', WPSC_PLUGIN_URL.'asset/lib/font-awesome/css/all.css?version='.WPSC_VERSION );
        wp_enqueue_style('wpsc-jquery-ui', WPSC_PLUGIN_URL.'asset/css/jquery-ui.css?version='.WPSC_VERSION );
        //admin scripts
        wp_enqueue_script('wpsc-admin', WPSC_PLUGIN_URL.'asset/js/admin.js?version='.WPSC_VERSION, array('jquery'), null, true);
        wp_enqueue_script('wpsc-public', WPSC_PLUGIN_URL.'asset/js/public.js?version='.WPSC_VERSION, array('jquery'), null, true);
        wp_enqueue_script('wpsc-modal', WPSC_PLUGIN_URL.'asset/js/modal.js?version='.WPSC_VERSION, array('jquery'), null, true);
        wp_enqueue_style('wpsc-public-css', WPSC_PLUGIN_URL . 'asset/css/public.css?version='.WPSC_VERSION );
        wp_enqueue_style('wpsc-admin-css', WPSC_PLUGIN_URL . 'asset/css/admin.css?version='.WPSC_VERSION );
        wp_enqueue_style('wpsc-modal-css', WPSC_PLUGIN_URL . 'asset/css/modal.css?version='.WPSC_VERSION );
        //Datetime picker
        wp_enqueue_script('wpsc-dtp-js', WPSC_PLUGIN_URL.'asset/lib/datetime-picker/jquery-ui-timepicker-addon.js?version='.WPSC_VERSION, array('jquery'), null, true);
        wp_enqueue_style('wpsc-dtp-css', WPSC_PLUGIN_URL . 'asset/lib/datetime-picker/jquery-ui-timepicker-addon.css?version='.WPSC_VERSION );
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
            'are_you_sure'        => __('Are you sure?','supportcandy'), 
            'templates'            => __('Templates','supportcandy'),
            'asc'                  => 'ASC',
            'desc'                 => 'DESC',
            'add_new_agent_only_field' => __('Add new agent only field','supportcandy'),
            'edit_agent_only_field' => __('Edit agent only field','supportcandy'),
            'add_new_form_field'   => __('Add New Form Field','supportcandy'),
            'edit_form_field'      => __('Edit Form Field','supportcandy'),
            'add_new_role'         => __('Add New Role','supportcandy'),
            'edit_agent_role'      => __('Edit Agent Role','supportcandy'),
            'add_new_category'     => __('Add New Category','supportcandy'),
            'edit_category'        => __('Edit Category','supportcandy'),
            'add_new_priority'     => __('Add New Priority','supportcandy'),
            'edit_priority'        => __('Edit Priority','supportcandy'),
            'add_new_status'       => __('Add New Status','supportcandy'),
            'edit_status'          => __('Edit Status','supportcandy'),
            'add_new_agent'        => __('Add New Agent','supportcandy'),
            'edit_agent'           => __('Edit Agent','supportcandy'),
            'add_filter_item'      => __('Add filter item','supportcandy'),
            'add_list_item'        => __('Add list item','supportcandy'),
            'change_agent_fields'  => __('Change Agent Fields','supportcandy'),
            'restore_ticket'       => __('Restore Ticket','supportcandy'),  
            'restore_deleted_tickets' => __('Restore Deleted Tickets','supportcandy'),
            'change_raised_by'     => __('Change Raised By','supportcandy'),
            'view_more' => __('View More','supportcandy'),
            'view_less' => __('View Less','supportcandy'),
            'delete_tickets_permanently' => __('Delete Tickets Permanently','supportcandy'),
            'delete_ticket_permanently'  => __('Delete Ticket Permanently','supportcandy'),
            'create_thread_ticket'       => __('Create Ticket From Thread','supportcandy'),
            'additional_recipients'      => __('Recipients','supportcandy'),
            'customer_name'              => __('Please insert customer name','supportcandy'),
            'customer_email'             => __('Please insert customer email','supportcandy'),
            'extra_thread_info'          => __('Thread Info','supportcandy'),
            'extra_ticket_info'          => __('Ticket Info ','supportcandy'),
            'users_all_tickets'          => __('All Tickets','supportcandy'),
            'warning_message'            => __('There is unposted text in the reply area. Are you sure you want to discard and proceed?','supportcandy'),
            'validate_email'             => __('Incorrect Email Address','supportcandy'),
            'save_filter'                => __('Save Filter','supportcandy'),
            'edit_custom_filter'         => __('Edit Filter','supportcandy'),
            'filter_title'               => __('Please insert filter title','supportcandy'),
            'popup_wait'                 => __('Please wait...','supportcandy'),
            'unresolve_agent'            => __('Please select at least one status for agent unresolved filter','supportcandy'),
            'unresolve_customer'         => __('Please select at least one status for customer unresolved filter','supportcandy'),
            'agent_close_statuses'       => __('Please select at least one close status','supportcandy'),
            'change_ticket_fields'       => __('Change Ticket Fields','supportcandy'),
            'assign_agent'               => __('Assign Agent','supportcandy'),
            'delete_ticket'              => __('Delete Ticket','supportcandy')
          ));
        wp_localize_script( 'wpsc-admin', 'wpsc_admin', $localize_script_data );
      endif;
    }
    
    // Dashboard Menus
    public function register_dashboard_menu(){
      
      global $current_user,$wpscfunction;
      
      $unresolved_count = 0;
      $label_counts  = get_user_meta( $current_user->ID, 'wpsc_'.get_current_blog_id().'_label_counts', true );
      if( $label_counts && is_array($label_counts) &&  isset($label_counts['unresolved_agent']) ){
        $unresolved_count = intval($label_counts['unresolved_agent']);
      }
      
      add_menu_page( 
        __( 'Support', 'supportcandy' ),
        __( 'Support','supportcandy'),
        'wpsc_agent',
        'wpsc-tickets',
        array($this,'tickets'),
        WPSC_PLUGIN_URL.'asset/images/admin_icon.png',
        25
      );

      add_submenu_page(
        'wpsc-tickets',
        __( 'Ticket List', 'supportcandy' ),
        __( 'Tickets', 'supportcandy' ),
        'wpsc_agent',
        'wpsc-tickets',
        array($this,'tickets')
      );
      
// PATT Menu Items
do_action('wpsc_add_submenu_page');
$agent_permissions = $wpscfunction->get_current_agent_permissions();
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
do_action('wpsc_add_admin_page');
}
// END PATT Menu Items

      add_submenu_page(
        'wpsc-tickets',
        __( 'Support Agents', 'supportcandy' ),
        __( 'Support Agents', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-support-agents',
        array($this,'support_agents')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'Custom Fields', 'supportcandy' ),
        __( 'Custom Fields', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-custom-fields',
        array($this,'custom_fields')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'Ticket List', 'supportcandy' ),
        __( 'Ticket List', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-ticket-list',
        array($this,'ticket_list')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'Email Notifications', 'supportcandy' ),
        __( 'Email Notifications', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-email-notifications',
        array($this,'email_notifications')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'Appearance', 'supportcandy' ),
        __( 'Appearance', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-appearance',
        array($this,'appearance_settings')
      );
      
      //PATT do_action('wpsc_add_submenu_page');
      
      add_submenu_page(
        'wpsc-tickets',
        __( 'Settings', 'supportcandy' ),
        __( 'Settings', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-settings',
        array($this,'settings')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'License', 'supportcandy' ),
        __( 'License', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-license',
        array($this,'licenses')
      );
      add_submenu_page(
        'wpsc-tickets',
        __( 'Addons', 'supportcandy' ),
        __( 'Addons', 'supportcandy' ),
        'edit_published_posts',
        'wpsc-add-ons',
        array($this,'addons')
      );
    }
    
    /**
     * Ticket list on backend
     */
    public function tickets(){
      global $wpscfunction, $current_user;
      $installed_db_version = get_option( 'wpsc_db_version', 1 );
      if( !($installed_db_version < WPSC_DB_VERSION) ){
        $wpscfunction->display_ad_banner();
        include WPSC_ABSPATH.'includes/admin/tickets/tickets.php';
      } else if($current_user->has_cap('edit_published_posts')) {
        include WPSC_ABSPATH.'includes/admin/db_upgrade/db_upgrade.php';
      }
    }
    
    // Support Agents
    public function support_agents(){
      global $wpscfunction;
      $wpscfunction->display_ad_banner();
      include WPSC_ABSPATH.'includes/admin/support_agents/support_agents.php';
    }
    
    // Ticket Form Page
    public function custom_fields(){
      global $wpscfunction;
      $wpscfunction->display_ad_banner();
      include WPSC_ABSPATH.'includes/admin/custom_fields/custom_fields.php';
    }
    
    // Ticket List settings
    public function ticket_list(){
      global $wpscfunction;
      $wpscfunction->display_ad_banner();
      include WPSC_ABSPATH.'includes/admin/ticket_list/ticket_list.php';
    }
    
    // Email Notification Settings
    public function email_notifications(){
      global $wpscfunction;
      $wpscfunction->display_ad_banner();
      include WPSC_ABSPATH.'includes/admin/email_notifications/email_notifications.php';
    }
    
    // Settings Page
    public function settings(){
      global $wpscfunction;
      $wpscfunction->display_ad_banner();
      include WPSC_ABSPATH.'includes/admin/settings/settings.php';
    }

    // Add-ons page
    public function licenses(){
      global $wpscfunction;
      $wpscfunction->display_ad_banner();
      include WPSC_ABSPATH.'includes/admin/license/license.php';
    }
    
    // Add-ons page
    public function addons(){
      global $wpscfunction;
      $wpscfunction->display_ad_banner();
      include WPSC_ABSPATH.'includes/admin/license/addons.php';
    }
    
    function responsive_style(){
      include WPSC_ABSPATH.'includes/responsive_style.php';
    }
    
    //Appearance Settings
    function appearance_settings() {
      global $wpscfunction;
      $wpscfunction->display_ad_banner();
      include WPSC_ABSPATH.'includes/admin/appearance_settings/appearance_settings.php';
    }
    
  }
  
endif;

new WPSC_Admin();