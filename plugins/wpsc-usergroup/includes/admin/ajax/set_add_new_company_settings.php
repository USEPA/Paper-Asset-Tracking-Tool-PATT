<?php
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly
?>
<?php
$usergroup_name = isset($_POST['wpsp_usergroup_name']) ? sanitize_text_field($_POST['wpsp_usergroup_name']) : '';
$user_id        = isset($_POST['user_id']) ? $_POST['user_id'] : array();
$supervisor_id  = isset($_POST['supervisor_id']) ? $_POST['supervisor_id'] : array();
$category_id    = isset($_POST) && isset($_POST['wpsc_usergroup_category']) ? intval($_POST['wpsc_usergroup_category']) : 0;

$term = wp_insert_term( $usergroup_name, 'wpsc_usergroup_data' );
if (!is_wp_error($term) && isset($term['term_id'])) {
      foreach($user_id as $id){
        add_term_meta ($term['term_id'], 'wpsc_usergroup_userid', $id);
      }
      
      foreach($supervisor_id as $s_id){
        add_term_meta ($term['term_id'], 'wpsc_usergroup_supervisor_id', $s_id);
      }
      
      add_term_meta ($term['term_id'], 'wpsc_usergroup_category', $category_id);
}

$response = array(
  'messege' => __('Setting saved!','wpsc-usergroup'),
);

echo json_encode( $response );