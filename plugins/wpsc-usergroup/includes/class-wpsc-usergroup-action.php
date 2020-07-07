<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'Usergroup_Actions' ) ) :
  
  final class Usergroup_Actions {
    // constructor
    public function __construct() {
      add_action( 'init', array( $this, 'load_actions') );
    }
    
    // Load actions
    function load_actions() {
      
      add_action('wpsc_set_add_usergroups',array($this,'set_add_usergroups'), 10 , 3);
    }
    function set_add_usergroups($ticket_id, $groups , $prevs_groups){
      global $wpscfunction, $current_user;
      $groups_obj = get_term_by('id',$groups,'wpsc_usergroup_data');
      $prev_groups_obj = get_term_by('id',$prevs_groups,'wpsc_usergroup_data'); 
      
      $prev_group_names = array();
      foreach ($prevs_groups as $prev_group_id) {
        $prev_groups_obj    = get_term_by('id',$prev_group_id,'wpsc_usergroup_data'); 
        $prev_group_names[] = $prev_groups_obj->name;
      }
      $prev_groups_names = implode(', ', $prev_group_names);
      
      $new_group_names = array();
      foreach ($groups as $group_id) {
        $groups_obj    = get_term_by('id',$group_id,'wpsc_usergroup_data'); 
        $new_group_names[] = $groups_obj->name;
      }
      $new_groups_names = implode(', ', $new_group_names);  
      if($current_user && $prev_groups_names && (!empty($new_group_names))){
        $log_str = sprintf( __('%1$s changed usergroups from %2$s to %3$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'.$prev_groups_names.'</strong>', '<strong>'.$new_groups_names.'</strong>');
      }else if($current_user && (!empty($new_group_names))){
        $log_str = sprintf( __('%1$s changed usergroups to %2$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>', '<strong>'.$new_groups_names.'</strong>');
      } elseif ($current_user && $prev_group_names && empty($new_group_names)) {
        $none = sprintf(__('None','wpsc-usergroup'));
      	$log_str = sprintf( __('%1$s changed usergroups from  %2$s to %3$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'.$prev_groups_names.'</strong>', '<strong>'.$none.'</strong>' );
      }else {
        $log_str = sprintf( __('added extra usergroups %1$s','supportcandy'), '<strong>'.$new_groups_names.'</strong>' );
      }
      
      $meta_value = array(
         'field_slug' => 'ticket_usergroups',
         'old_value'  => $prev_groups_names,
         'new_value'  => $new_groups_names,
         'updated_by' => $current_user->ID,
       );
       
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log',
        'log_meta'       => $meta_value 
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }
  }

endif;  

new Usergroup_Actions;