<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'Usergroup_Functions' ) ) :
  
  final class Usergroup_Functions {
    
    function add_usergroups($ticket_id, $groups){
      global $wpscfunction,$wpdb;
      $prevs_groups = $wpscfunction->get_ticket_meta($ticket_id,'usergroups');
      
      $wpscfunction->delete_ticket_meta($ticket_id,'usergroups');
      if($groups){
       foreach($groups as $group){
         $wpscfunction->add_ticket_meta($ticket_id,'usergroups',$group);
       }
      }
      do_action('wpsc_set_add_usergroups', $ticket_id, $groups, $prevs_groups);
    }
  }
endif;

$GLOBALS['wpscugfunction'] =  new Usergroup_Functions();