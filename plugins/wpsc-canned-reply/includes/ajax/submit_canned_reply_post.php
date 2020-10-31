<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

ob_start();
?>
<form id="frm_canned_reply_create_title">
	<div id="canned_reply_title">
	  <div class="form-group">
			<label><?php _e('Canned Reply Title', 'wpsp-canned-reply') ?></label>
		    <input class="form-control form-control" name="canned_reply_title"  id="canned_reply_title" type="text" autocomplete="off" placeholder="<?php _e('canned reply title','wpsp-canned-reply')?>" />				
		</div>
		<div>
			<label><?php _e('Category', 'wpsp-canned-reply') ?></label>
			<select class="form-control" name="category" >
				<option value="0" selected="selected"><?php _e('All', 'wpsp-canned-reply') ?></option>
					<?php
						$categories = get_terms([
							'taxonomy'   => 'wpsc_canned_reply_categories',
							'hide_empty' => false
						]);												
						foreach ($categories as $category):																											
							echo '<option value="'.$category->term_id.'">'.$category->name.'</option>';							
						endforeach;
					?>
			</select>
		</div>
	</div> 
	<input type="hidden" name="action" value="wpsc_tickets" />
	<input type="hidden" name="setting_action" value="set_canned_reply_title" />  			
</form>
<script>

function wpsc_set_canned_reply_title(){         
  var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
  if(is_tinymce){
	var description = tinyMCE.activeEditor.getContent().trim();
  } else {
	var description = jQuery('#wpsc_reply_box').val().trim();
  }
  if(description.length==0) return; 
  var ticket_id = jQuery('input[name="ticket_id"]').val().trim();  
  var dataform = new FormData(jQuery('#frm_canned_reply_create_title')[0]);
  dataform.append('action','wpsc_set_canned_reply');
  dataform.append('description',description);
  wpsc_modal_close();
  jQuery('.wpsc_reply_widget').html(wpsc_admin.loading_html);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,  
    processData: false,
    contentType: false
  })
  .done(function (response_str) { 
    wpsc_open_ticket(ticket_id);
  });
}
</script>
<?php 

$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsp-canned-reply');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_canned_reply_title();"><?php _e('Submit','wpsp-canned-reply');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);