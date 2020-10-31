<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}

$ticket_id    = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : 0 ;
$thread_id  = isset($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : 0 ;

$thread_body = get_post($thread_id);
$thread_body = $thread_body->post_content;

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

$rich_editing = $wpscfunction->rich_editing_status($current_user);

ob_start();
?>
<form id="frm_edit_thread">
    
    <textarea id="wpsc_therad_edit" name="thread_body" class="wpsc_textarea"><?php echo stripslashes(addslashes(htmlspecialchars_decode($thread_body, ENT_QUOTES)))?></textarea>
    
		<input type="hidden" name="action" value="wpsc_tickets" />
		<input type="hidden" name="setting_action" value="set_edit_thread" />
		<input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id)?>" />
    <input type="hidden" name="thread_id" value="<?php echo htmlentities($thread_id)?>" />
    
</form> 
<?php
$tinymce_toolbox = array();
if( $rich_editing ){
  $wpsc_tinymce_toolbar = get_option('wpsc_tinymce_toolbar');
  $toolbar_active = get_option('wpsc_tinymce_toolbar_active');
  foreach ($toolbar_active as $key => $value) {
    $tinymce_toolbox[] = $wpsc_tinymce_toolbar[$value]['value'];
    if($value == 'blockquote' || $value == 'align' || $value == 'numbered_list' || $value == 'right_to_left'){
      $tinymce_toolbox[] = ' | ';
    }
  }
  ?>
  <script>
  tinymce.remove();
  tinymce.init({ 
    selector:'#wpsc_therad_edit',
    body_id: 'thread_editor',
    menubar: false,
	  statusbar: false,
	  autoresize_min_height: 150,
	  wp_autoresize_on: true,
    plugins: [
      'lists link image directionality wpautoresize'
    ],
    image_advtab: true,
    toolbar: '<?php echo implode(' ', $tinymce_toolbox) ?> | wpsc_templates ',
    branding: false,
    autoresize_bottom_margin: 20,
    browser_spellcheck : true,
    relative_urls : false,
    remove_script_host : false,
    convert_urls : true,
	  setup: function (editor) {
    }
  });
  </script>
  <?php } ?>
<?php 
$body = ob_get_clean();

ob_start();
$tinymce_array = htmlspecialchars(json_encode($tinymce_toolbox), ENT_QUOTES, 'UTF-8');
?>


<div class="row">
    <div class="col-md-12" style="text-align: right;">
			<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"    onclick="wpsc_modal_close_thread(<?php echo $tinymce_array ?>);"><?php _e('Close','supportcandy');?></button>
			<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;"  onclick="wpsc_set_edit_thread(<?php echo htmlentities($ticket_id) ?>);"><?php _e('Save Changes','supportcandy');?></button>
  </div>
</div>

<?php
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
?>