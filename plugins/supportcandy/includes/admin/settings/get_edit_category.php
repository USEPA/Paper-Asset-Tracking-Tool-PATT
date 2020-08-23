<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$cat_id = isset($_POST) && isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
if (!$cat_id) {exit;}

$category = get_term_by('id', $cat_id, 'wpsc_categories');

ob_start();
?>
<div class="form-group">
  <label for="wpsc_cat_name"><?php _e('Category Name','supportcandy');?></label>
  <p class="help-block"><?php _e('Insert category name.','supportcandy');?></p>
  <input id="wpsc_cat_name" class="form-control" name="wpsc_cat_name" value="<?php echo $category->name;?>" />
</div>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_edit_category(<?php echo htmlentities($cat_id)?>);"><?php _e('Save Changes','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
