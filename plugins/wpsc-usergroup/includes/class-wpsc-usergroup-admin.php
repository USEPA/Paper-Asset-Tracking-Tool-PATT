<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Usergroup_Admin' ) ) :

  final class WPSC_Usergroup_Admin {

    public function __construct() {
      add_action( 'admin_enqueue_scripts', array( $this, 'loadScripts') );
    }

    // Load admin scripts
    public function loadScripts(){
         wp_enqueue_script('jquery');
         wp_enqueue_script('jquery-ui-core');
         wp_enqueue_script('wpsc_usergroup_admin', WPSC_USERGROUP_URL.'asset/js/admin.js?version='.WPSC_USERGROUP_VERSION, array('jquery'), null, true);
         wp_enqueue_style('wpsc_usergroup_admin', WPSC_USERGROUP_URL . 'asset/css/admin.css?version='.WPSC_USERGROUP_VERSION );

        $loading_html = '<div class="wpsc_loading_icon"><img src="'.WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif"></div>';
        $localize_script_data = apply_filters( 'wpsc_admin_localize_script', array(
            'ajax_url'          => admin_url( 'admin-ajax.php' ),
            'loading_html'      => $loading_html,
            'at_least_one_user' => __('Please select atleast one user','wpsc-usergroup'),
            'insert_name'       => __('Please enter required Name','wpsp-company'),
        ));
        wp_localize_script( 'wpsc_usergroup_admin', 'wpsc_usergroup_data', $localize_script_data );
    }

    function wpsc_after_setting_pills(){
        ?>
        <li id="wpsc_settings_wpsc_usergroup" role="presentation"><a href="javascript:wpsc_get_wpsc_usergroup_settings();"><?php _e('Usergroup','wpsc-usergroup');?></a></li>
        <?php
    }

    function wpsp_en_after_edit_recipients($recipients){
        ?>
        <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
          <div style="width:25px;"><input type="checkbox" <?php echo in_array('usergroup_supervisors',$recipients)?'checked="checked"':''?> name="wpsc_en_recipients[]" value="usergroup_supervisors" /></div>
          <div style="padding-top:3px;"><?php _e('Usergroup Supervisors','wpsc-usergroup')?></div>
        </div>
        <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
          <div style="width:25px;"><input type="checkbox" <?php echo in_array('usergroup_members',$recipients)?'checked="checked"':''?> name="wpsc_en_recipients[]" value="usergroup_members" /></div>
          <div style="padding-top:3px;"><?php _e('Usergroup Members','wpsc-usergroup')?></div>
        </div>
        <?php
    }

    function wpsp_en_add_ticket_recipients(){
        ?>
        <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
          <div style="width:25px;"><input type="checkbox" name="wpsc_en_recipients[]" value="usergroup_supervisors" /></div>
          <div style="padding-top:3px;"><?php _e('Usergroup Supervisors','wpsc-usergroup')?></div>
        </div>
        <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
          <div style="width:25px;"><input type="checkbox" name="wpsc_en_recipients[]" value="usergroup_members" /></div>
          <div style="padding-top:3px;"><?php _e('Usergroup Members','wpsc-usergroup')?></div>
        </div>
        <?php
    }

    // Add-on installed or not for licensing
		function is_add_on_installed($flag){
			return true;
		}

    // Print license functionlity for this add-on
    function addon_license_area(){
      include WPSC_USERGROUP_ABSPATH . 'includes/addon_license_area.php';
    }

    // Activate USERGROUP license
    function license_activate(){
      include WPSC_USERGROUP_ABSPATH . 'includes/license_activate.php';
      die();
    }

    // Deactivate USERGROUP license
    function license_deactivate(){
      include WPSC_USERGROUP_ABSPATH . 'includes/license_deactivate.php';
      die();
    }

    // Print Ticket List
    function print_tl_usergroup($ticket_list){
      include WPSC_USERGROUP_ABSPATH . 'includes/print_tl_usergroup.php';
    }

    function wpsc_get_select_field($flag,$ticket_id,$select_field){
      if ($select_field == 'usergroup'){
         $flag = false;
      }
      return $flag;
    }

    //filters
    function wpsc_filter_autocomplete($output,$term,$field_slug){
      if( $field_slug == 'usergroup'){
        $usergroups = get_terms([
          'taxonomy'   => 'wpsc_usergroup_data',
          'hide_empty' => false,
          'orderby'    => 'meta_value_num',
          'order'    	 => 'ASC'
        ]);
        foreach($usergroups as $usergroup){
          $output[] = array(
            'label'    => $usergroup->name,
            'value'    => '',
            'flag_val' => $usergroup->term_id,
            'slug'     => $field_slug,
          );
        }
      }

      return $output;
    }

    function wpsc_filter_val_label($val,$field_slug){
      if($field_slug == 'usergroup'){
        $user_group = get_term_by('id',$val,'wpsc_usergroup_data');
        $val = $user_group->name;
      }
      return $val;
    }

    function wpsc_add_ticket_meta($flag,$field_slug){
      if( $field_slug == 'usergroup'){
        $flag = false;
      }
      return $flag;
    }

    function wpsc_tl_restrict_rules($restrict_rules){
      global $wpscfunction;
      $filter = $wpscfunction->get_current_filter();
      if(isset($filter['custom_filter']['usergroup'])){
        $restrict_rules = array(
          'relation' => 'OR',
        );
        $user_groups = $filter['custom_filter']['usergroup'];
        $user_emails = array();
        foreach ($user_groups as $group_id) {
          $user_group = get_term_by('id',$group_id,'wpsc_usergroup_data');
          $group_user_id = get_term_meta( $user_group->term_id, 'wpsc_usergroup_userid');
          foreach ($group_user_id as $user_id) {
            $user_obj = get_user_by('id',$user_id);
            $user_emails[] = $user_obj->user_email;

          }
        }
        $user_emails = array_unique($user_emails);
        $restrict_rules[] = array(
          'key'            => 'customer_email',
          'value'          => $user_emails,
          'compare'        => 'IN'
        );
      }

      return $restrict_rules;
    }

    function wpsc_set_other_settings(){
      include WPSC_USERGROUP_ABSPATH . 'includes/set_other_settings.php';
      die();
    }

    function create_ticket_category($ticket_category,$args){
      $customer_email = isset($args['customer_email']) ? sanitize_text_field($args['customer_email']) : '';

      $user = get_user_by('email',$customer_email);
      if( $user ){
        $usergroup = get_terms([
          'taxonomy'   => 'wpsc_usergroup_data',
          'hide_empty' => false,
          'meta_query' => array(
          'relation' => 'AND',
          array(
            'key'     => 'wpsc_usergroup_userid',
            'value'   => $user->ID,
            'compare' => '='
          ),
          ),
        ]);
        if( $usergroup ){
          $category_id = get_term_meta( $usergroup[0]->term_id, 'wpsc_usergroup_category', true);
          $user_group_term = get_term_by('slug','usergroup','wpsc_ticket_custom_fields');
          $allowed = get_option('wpsc_allow_usergroup_change_category');
          if($category_id && (!$user->has_cap('wpsc_agent') && !$allowed) && !empty($ticket_category) ){
          $ticket_category = $category_id;
          }
        }
      }
      
      return $ticket_category;
    }
    
    function add_condition_option( $condition_options ){
      
        $condition_options[] = array(
          'key'         => 'usergroup',
          'label'       => __( 'Usergroup', 'wpsc-usergroup' ),
          'has_options' => 1,
        );
        return $condition_options;
      
    }
    
    function wpsc_disable_ticket_category($disabled, $field, $default_ticket_category){
      global $current_user;
      
      $allowed = get_option('wpsc_allow_usergroup_change_category');
      if( (!$current_user->has_cap('wpsc_agent') && !$allowed) && !empty($default_ticket_category) ){ 
        $disabled = 'disabled';
      }
      return $disabled;
    }

    function wpsc_add_hidden_category_field($field,$default_ticket_category){
      if($field->slug == 'ticket_category'){
        global $current_user;
        $allowed = get_option('wpsc_allow_usergroup_change_category');
        if( (!$current_user->has_cap('wpsc_agent') && !$allowed) && !empty($default_ticket_category) ){
          echo "<input type='hidden' name='". $field->slug."' value='".$default_ticket_category."'>";
        }
      }
    }


    function wpsc_condition_dd_options( $options, $key ){
      if( $key == 'usergroup' ){
        $options = array();
        $usergroups = get_terms([
          'taxonomy'   => 'wpsc_usergroup_data',
          'hide_empty' => false,
          'orderby'    => 'meta_value_num',
          'order'    	 => 'ASC'
        ]);
        foreach($usergroups as $group){
          $data = array(
            'value' => $group->term_id,
            'label' => $group->name
          );
          $options[] = $data;
        }
      }
      
      return $options;
    }

    function wpsc_check_custom_ticket_condition ($inner_flag, $ticket_id, $unique_condition ){
      global $current_user,$wpdb;
      $user_email = $wpdb->get_var("SELECT customer_email FROM {$wpdb->prefix}wpsc_ticket WHERE id=".$ticket_id);
      if(email_exists($user_email)){
        $user = get_user_by('email', $user_email);
        foreach ( $unique_condition as $condition) {
        
          if($condition->field == 'usergroup'){
            $wpsc_usergroup_userid = get_term_meta( $condition->cond_val, 'wpsc_usergroup_userid');
            if(in_array($user->ID,$wpsc_usergroup_userid)){
              $inner_flag = true;    
            }else{
              $inner_flag = false;
            }
          }
          
          if( $inner_flag ) break;
        
        }
      }
      
      return $inner_flag;
    }
  
    function replace_macro($str,$ticket_id){
      include WPSC_USERGROUP_ABSPATH . 'includes/replace_macro.php';
      return $str;
    }
  }

endif;