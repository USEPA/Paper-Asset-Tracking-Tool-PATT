<?php

final class WPSCannedreplyBackend {
  
  
  function wpsc_add_addon_tab_after_macro(){
    include WPSC_CANNED_DIR . 'includes/admin/canned_reply.php';    
  }
  
  function wpsc_canned_reply_post(){
    include WPSC_CANNED_DIR . 'includes/admin/wpsc_submit_canned_reply_post.php';    
  }
  

  function get_canned_reply() {
    include WPSC_CANNED_DIR . 'includes/ajax/get_canned_reply.php';
    die();
  }
  
  function delete_canned_reply() {
    include WPSC_CANNED_DIR . 'includes/ajax/delete_canned_reply.php';
    die();
  }
  
  function submit_canned_reply_post(){
    
    include WPSC_CANNED_DIR . 'includes/ajax/submit_canned_reply_post.php';
    die();
  }
  
  function set_canned_reply(){     
    include WPSC_CANNED_DIR . 'includes/ajax/set_canned_reply_title.php';
    die();
  }
  
  function insert_canned_reply(){    
    include WPSC_CANNED_DIR . 'includes/ajax/insert_canned_reply.php';
    die();
  }
  
  // Add-on installed or not for licensing
  function is_add_on_installed($flag){
    return true;
  }
  
  // Print license functionlity for this add-on
  function addon_license_area(){
    include WPSC_CANNED_DIR . 'includes/addon_license_area.php';
  }
  
  // Activate Canned Reply license
  function license_activate(){
    include WPSC_CANNED_DIR . 'includes/license_activate.php';
    die();
  }
  
  // Deactivate Canned Reply license
  function license_deactivate(){
    include WPSC_CANNED_DIR . 'includes/license_deactivate.php';
    die();
  }
}
  