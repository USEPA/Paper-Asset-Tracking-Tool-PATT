<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Usergroup_Frontend' ) ) :
  
  final class WPSC_Usergroup_Frontend {
    
      public function __construct() {
          add_action( 'wp_enqueue_scripts', array( $this, 'loadScripts') );
      }
      
      // Load admin scripts
      public function loadScripts(){
           wp_enqueue_script('jquery');
           wp_enqueue_script('jquery-ui-core');
           wp_enqueue_script('wpsc_usergroup_public', WPSC_USERGROUP_URL.'asset/js/public.js?version='.WPSC_USERGROUP_VERSION, array('jquery'), null, true);
           wp_enqueue_style('wpsc_usergroup_public', WPSC_USERGROUP_URL . 'asset/css/public.css?version='.WPSC_USERGROUP_VERSION );
          
           $loading_html = '<div class="wpsc_loading_icon"><img src="'.WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif"></div>';
           $localize_script_data = apply_filters( 'wpsc_admin_localize_script', array(
              'ajax_url'       => admin_url( 'admin-ajax.php' ),
              'loading_html'   => $loading_html,
           ));
          wp_localize_script( 'wpsc_usergroup_public', 'wpsc_usergroup_data', $localize_script_data );
      }
      
      function wpsc_tl_customer_restrict_rules($restrict_rules){
          include_once( WPSC_USERGROUP_ABSPATH . 'includes/wpsc_tl_customer_restrict_rules.php' );
          return $restrict_rules;
          die();
      }
      
      function wpsc_has_permission($response,$ticket_id,$permission){
        global $current_user,$wpscfunction;
        $customer_email = $wpscfunction->get_ticket_fields($ticket_id,'customer_email');
        $user           = get_user_by('email',$customer_email);
        $usergroups     = array();
        if($user){
          $usergroups     = get_terms([
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
        }
        if($permission == 'view_ticket' || $permission == 'reply_ticket'){
            if($current_user->user_email == $customer_email) return 1;
            foreach($usergroups as $usergroup){
                $wpsc_usergroup_userid     = get_term_meta( $usergroup->term_id, 'wpsc_usergroup_userid');
                $wpsc_usergroup_supervisor = get_term_meta( $usergroup->term_id, 'wpsc_usergroup_supervisor_id');
                if(in_array($current_user->ID,$wpsc_usergroup_supervisor)){
                    $response = 1;
                    break;
                }
            }
        }
        return $response;
      }
      
      function wpsc_en_create_ticket_email_addresses($email_addresses,$email,$ticket_id){
          include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/wpsc_en_create_ticket_email_addresses.php' );
          return $email_addresses;
      }
      
      function wpsc_en_submit_reply_email_addresses($email_addresses,$email,$thread_id,$ticket_id){
          include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/wpsc_en_submit_reply_email_addresses.php' );
          return $email_addresses;
      }
      
      function wpsc_en_assign_agent_email_addresses($email_addresses,$email,$ticket_id){
          include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/wpsc_en_assign_agent_email_addresses.php' );
          return $email_addresses;
      }
      
      function wpsc_en_change_status_email_addresses($email_addresses,$email,$ticket_id){
          include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/wpsc_en_change_status_email_addresses.php' );
          return $email_addresses;
      }
      
      function wpsc_en_delete_ticket_email_addresses($email_addresses,$email,$ticket_id){
          include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/wpsc_en_delete_ticket_email_addresses.php' );
          return $email_addresses;
      }
      
      function wpsc_en_submit_note_email_addresses($email_addresses,$email,$thread_id,$ticket_id){
          include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/wpsc_en_submit_note_email_addresses.php' );
          return $email_addresses;
      }
      
      function wpsc_add_default_ticket_category($default_category, $args, $customer_email, $ticket_category){
        include_once( WPSC_USERGROUP_ABSPATH . 'includes/frontend/wpsc_add_default_ticket_category.php' );
        return $ticket_category;
        die();
      }
      
      function wpsc_selected_create_ticket_category($category_id){
        global $current_user;
        
        if( $current_user->ID ){
          $user = get_user_by('email',$current_user->user_email);
          $usergroups = get_terms([
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

          if($usergroups){
          	$cat_id = get_term_meta( $usergroups[0]->term_id, 'wpsc_usergroup_category', true);
            if($cat_id){
              $category_id = $cat_id;
            }
          }
        }
        
        return $category_id;
      }
      
      //Send email notification for change category 
      function wpsc_en_change_category_email_addresses($email_addresses,$email,$ticket_id){
          include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/wpsc_en_change_category_email_addresses.php' );
          return $email_addresses;
      }
      
      //Send email notification for change priority 
      function wpsc_en_change_priority_email_addresses($email_addresses,$email,$ticket_id){
          include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/wpsc_en_change_priority_email_addresses.php' );
          return $email_addresses;
      }
      
      function add_extra_ticket_usergroup($ticket_id){
        global $current_user;
        if($current_user->has_cap('wpsc_agent')){
          include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/add_extra_ticket_usergroup.php');  
        }
      }
      
      function filter_autocomplete_usergroups(){
        include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/filter_autocomplete_usergroups.php');
        die();
      }
      
      function set_add_extra_ticket_usergroup($ticket_id, $post_data){
        include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/set_add_extra_ticket_usergroup.php');
      }
      
      function add_usergroups_to_individual_ticket($ticket_id){
        global $wpscfunction;
        $groups = $wpscfunction->get_ticket_meta($ticket_id,'usergroups');
        if($groups){
          ?>
          <strong><small><?php _e('Usergroups:', 'supportcandy'); ?></small></strong>	
          <?php

          foreach ($groups as $group ) {
            $groups_obj =  get_term_by('id',$group,'wpsc_usergroup_data');
            ?>
            <td style="width:25px !important;">
              <div style="padding:2px 0;">
                  <img style="width:20px" src="<?php echo WPSC_USERGROUP_URL.'asset/images/usergroup.png'?>">
                <?php echo $groups_obj->name;?>
              </div>
            </td>
          <?php  
          }
  		  }else{
          $extra_email = true;
        }
      }

      function export_usergroup_ticket_fields( $export_colomn_value,$ticket_id,$value){
        include( WPSC_USERGROUP_ABSPATH . 'includes/frontend/export_usergroup_ticket_fields.php');
        return $export_colomn_value;
      }

  }
  
endif;