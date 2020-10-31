<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

ob_start();
?>
<div class="form-group">
  <label for="wpsc_cat_name"><?php _e('Category Name','supportcandy');?></label>
  <p class="help-block"><?php _e('Insert category name. Please make sure category name you are entering should not already exist.','supportcandy');?></p>
  <input id="wpsc_cat_name" class="form-control" name="wpsc_cat_name" value="" />
</div>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_add_category();"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
