<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}

$ticket_id    = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : 0 ;
$thread_id    = isset($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : 0 ;

$source       = isset($_POST['source']) ? sanitize_text_field($_POST['source']) : 'thread' ;
$raised_email = $wpscfunction->get_ticket_fields($ticket_id,'customer_email');

$user         = get_user_by('email', $raised_email);
$thread_type  = get_post_meta( $thread_id, 'thread_type', true);

$ip_address = get_post_meta($thread_id, 'ip_address', true);
$ip_address = $ip_address ? $ip_address : __('Not Found','supportcandy');

$os         = get_post_meta($thread_id,'os',true);
$os         = $os ? $os : __('Not Found','supportcandy');
 
$browser    = get_post_meta($thread_id,'browser',true);
$browser    = $browser ? $browser : __('Not Found','supportcandy');

$reply_source = get_post_meta( $thread_id, 'reply_source',true);
$reply_type = '';
if ($reply_source == 'browser'){
	$reply_type = "Browser"; 
}
$reply_type = apply_filters( 'wpsc_ticket_thread_reply_source',$reply_type, $reply_source,$thread_id,$ticket_id );
$reply_type = $reply_type ? $reply_type : __('Not Found','supportcandy');

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

if(get_option('wpsc_support_page_id')){
	$ticket_auth_code = $wpscfunction->get_ticket_fields($ticket_id,'ticket_auth_code');
	$ticket_url = get_permalink(get_option('wpsc_support_page_id')).'?support_page=open_ticket&ticket_id='.$ticket_id.'&auth_code='.$ticket_auth_code;
	$ticket_url = '<a href="'.$ticket_url.'" target="_blank">'.__('Click here','supportcandy').'</a>';
}else{
	$ticket_url = __('Please select support page in settings.','supportcandy');
}



ob_start();
?>
<div id="wpsc_raised_extra_info">
	<table class="table table-hover">
		<tr>
			<td style="width:200px;"><strong><?php _e('IP Address','supportcandy'); ?></strong></td>
			<td><?php echo htmlentities($ip_address); ?></td>
		</tr>
		<tr>
			<?php if($source == 'raised_by'){ ?>
			<td><strong><?php _e('Ticket Source','supportcandy'); ?></strong></td>
		<?php } else { ?>
			<td><strong><?php _e('Thread Source','supportcandy'); ?></strong></td>
		<?php } ?>
			<td><?php echo htmlentities($reply_type); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e('Operating System','supportcandy'); ?></strong></td>
			<td><?php echo htmlentities($os); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e('Browser','supportcandy'); ?></strong></td>
			<td><?php echo htmlentities($browser); ?></td>
		</tr>
		<tr>
			<td><strong><?php _e('Ticket URL','supportcandy'); ?></strong></td>
			<td><?php echo ($ticket_url); ?></td>
		</tr>
	</table>
</div>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<?php
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);