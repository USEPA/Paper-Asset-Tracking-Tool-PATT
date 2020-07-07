<?php
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly
?>
<?php
$usergroup_name = isset($_POST['wpsp_usergroup_name']) ? sanitize_text_field($_POST['wpsp_usergroup_name']) : '';
$user_id        = isset($_POST['user_id']) ? $_POST['user_id'] : array();
$supervisor_id  = isset($_POST['supervisor_id']) ? $_POST['supervisor_id'] : array();
$category_id    = isset($_POST) && isset($_POST['wpsc_usergroup_category']) ? intval($_POST['wpsc_usergroup_category']) : 0;
$term_id        = isset($_POST) && isset($_POST['usergroup_id']) ? intval($_POST['usergroup_id']) : 0;

wp_update_term($_POST['usergroup_id'], 'wpsc_usergroup_data', array('name' => $usergroup_name));
if (isset($_POST['usergroup_id'])) {
      delete_term_meta($_POST['usergroup_id'], 'wpsc_usergroup_userid');
      delete_term_meta($_POST['usergroup_id'], 'wpsc_usergroup_supervisor_id');
      foreach($user_id as $id){
        add_term_meta ($_POST['usergroup_id'], 'wpsc_usergroup_userid', $id);
      }
      
      foreach($supervisor_id as $s_id){
        add_term_meta ($_POST['usergroup_id'], 'wpsc_usergroup_supervisor_id', $s_id);
      }
      
      update_term_meta ($term_id, 'wpsc_usergroup_category', $category_id);
}