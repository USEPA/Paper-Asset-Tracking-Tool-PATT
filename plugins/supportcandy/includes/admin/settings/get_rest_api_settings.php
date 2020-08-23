<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}
?>

<h4 style="margin-bottom: 25px;">
	<?php _e('REST API','supportcandy');?>
</h4>
<form id="wpsc_frm_rest_api_settings" method="post" action="javascript:wpsc_set_rest_api_settings();">
	
  <div class="form-group">
    <label for="wpsc_captcha"><?php _e('Allow REST API','supportcandy');?></label>
    <p class="help-block"><?php _e("Enable or disable REST API for SupportCandy.","supportcandy");?></p>
    <select class="form-control" name="wpsc_rest_api" id="wpsc_rest_api">
      <?php
      $wpsc_rest = get_option('wpsc_rest_api');
      $selected = $wpsc_rest == '0' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
      $selected = $wpsc_rest == '1' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
      ?>
    </select>
  </div>
  
  <div class="form-group" id="wpsc_rest_api_secret_key">
		<label for="wpsc_rest_api_secret_key"><?php _e('Secret Key','supportcandy');?></label>
		<p class="help-block"><?php _e("This will be required to authorize your api requests. Store this at safe place in your application so that it should not be accessible to public.","supportcandy");?></p>
    <input type="text" class="form-control" name="wpsc_rest_api_secret_key" id="wpsc_rest_api_secret_key" value="<?php echo get_option('wpsc_rest_api_secret_key'); ?>">
	</div>
 
	<button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
	<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
	<input type="hidden" name="action" value="wpsc_settings" />
	<input type="hidden" name="setting_action" value="set_rest_api_settings" />
</form>
