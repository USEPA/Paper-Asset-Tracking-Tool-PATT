<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

if (!isset($_SESSION)) {
    session_start();    
}

$post_name = $_POST["post_name"];

ob_start();

			$get_post_details = $wpdb->get_row("SELECT a.post_title, a.post_content 
			FROM wpqa_posts a 
			INNER JOIN wpqa_term_relationships b ON a.id = b.object_id 
			INNER JOIN wpqa_term_taxonomy c ON b.term_taxonomy_id = c.term_taxonomy_id 
			INNER JOIN wpqa_terms d ON c.term_id = d.term_id WHERE a.post_status = 'publish' AND
			d.slug = 'help-messages' AND a.post_name = '" . $post_name . "'");
			$post_details_subject = $get_post_details->post_title;
			$post_details_content = $get_post_details->post_content; 
			
echo '<strong>'.$post_details_subject.'</strong><br />';
echo '<p>'.$post_details_content.'</p>';
?>

<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();<?php if($response_page == 'filedetails') { ?>window.location.reload();<?php } ?>"><?php _e('Close','wpsc-export-ticket');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);