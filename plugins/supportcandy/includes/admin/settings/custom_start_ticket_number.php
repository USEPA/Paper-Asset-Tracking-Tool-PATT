<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction,$wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

if(!(isset($_POST) && isset($_POST['new_count']) && is_numeric($_POST['new_count']) && $_POST['new_count'] > 0)){
	exit;
}

$new_ticket_number = sanitize_text_field($_POST['new_count']);
$old_ticket_number = $wpdb->get_var("SELECT MAX(id) FROM  ".$wpdb->prefix."wpsc_ticket t ");
$msg=array();

if($new_ticket_number > $old_ticket_number) {
	update_option('wpsc_custom_ticket_count',$new_ticket_number);
	$wpdb->query("ALTER TABLE ".$wpdb->prefix."wpsc_ticket AUTO_INCREMENT = ".$new_ticket_number." ");
	do_action('wpsc_after_custom_ticket_number_update');
	
	echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';
	
}else if( $new_ticket_number < $old_ticket_number ){
	$msg['msg']= __('Ticket number must be greater than current ticket number', 'supportcandy');
	echo json_encode($msg);
}
