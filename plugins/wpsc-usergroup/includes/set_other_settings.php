<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$user_group_term = get_term_by('slug','usergroup','wpsc_ticket_custom_fields');

$wpsc_usergroup_label = isset($_POST['wpsc_usergroup_label']) ? sanitize_text_field($_POST['wpsc_usergroup_label']) : '';
update_term_meta($user_group_term->term_id,'wpsc_tf_label',$wpsc_usergroup_label);

$wpsc_usergroup_change_category= isset($_POST['wpsc_usergroup_change_category']) ? sanitize_text_field($_POST['wpsc_usergroup_change_category']) : '1';
update_option('wpsc_allow_usergroup_change_category',$wpsc_usergroup_change_category);



echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','wpsc-usergroup').'" }';
