<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$wpsc_appearance_login_form=get_option( 'wpsc_appearance_login_form' );
 ?>
 	<form id="wpsc_frm_appearance_login_form" method="post" action="javascript:wpsc_set_appearance_login_form();">
 	 
   <div class="form-group">
 		 <label for="wpsc_login_form_bg_color"><?php _e('Sign In Button','supportcandy');?></label>
 		 <div class="row">
        <div class="col-sm-4">
          <p class="help-block"><?php _e('Background Color','supportCandy') ?></p>
          <input id="wpsc_signin_button_bg_color" class="wpsc_color_picker" name="get_login_form[wpsc_signin_button_bg_color]" value="<?php echo ($wpsc_appearance_login_form['wpsc_signin_button_bg_color']);?>" />
        </div>
        <div class="col-sm-4">
          <p class="help-block"><?php _e('Color','supportCandy') ?></p>
          <input id="wpsc_signin_button_text_color" class="wpsc_color_picker" name="get_login_form[wpsc_signin_button_text_color]" value="<?php echo ($wpsc_appearance_login_form['wpsc_signin_button_text_color']);?>" />
        </div>
				<div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_signin_button_border_color" class="wpsc_color_picker" name="get_login_form[wpsc_signin_button_border_color]" value="<?php echo $wpsc_appearance_login_form['wpsc_signin_button_border_color'] ?>" />
				</div>
     </div>
   </div>
	 
   <div class="form-group">
     <label for="wpsc_register_now"><?php _e('Register Button','supportcandy');?></label>
     <div class="row">
        <div class="col-sm-4">
          <p class="help-block"><?php _e('Background color','supportcandy');?></p>
          <input id="wpsc_register_now_button_bg_color" class="wpsc_color_picker" name="get_login_form[wpsc_register_now_button_bg_color]" value="<?php echo ($wpsc_appearance_login_form['wpsc_register_now_button_bg_color']);?>" />
        </div>
        <div class="col-sm-4">
          <p class="help-block"><?php _e('Color','supportCandy') ?></p>
          <input id="wpsc_register_now_button_text_color" class="wpsc_color_picker" name="get_login_form[wpsc_register_now_text_color]" value="<?php echo ($wpsc_appearance_login_form['wpsc_register_now_text_color']);?>" />
        </div>
				<div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_register_now_button_border_color" class="wpsc_color_picker" name="get_login_form[wpsc_register_now_button_border_color]" value="<?php echo $wpsc_appearance_login_form['wpsc_register_now_button_border_color'] ?>" />
				</div>
     </div>
  </div>
	 
   <div class="form-group">
      <label for="wpsc_continue_as_guest_button"><?php _e('Continue as Guest Button','supportcandy');?></label>
      <div class="row">
	      <div class="col-sm-4">
	        <p class="help-block"><?php _e('Background Color','supportCandy') ?></p>
	        <input id="wpsc_continue_as_guest_button_bg_color" class="wpsc_color_picker" name="get_login_form[wpsc_continue_as_guest_button_bg_color]" value="<?php echo ($wpsc_appearance_login_form['wpsc_continue_as_guest_button_bg_color']);?>" />
	      </div>
	      <div class="col-sm-4">
	        <p class="help-block"><?php _e('Color','supportCandy') ?></p>
	        <input id="wpsc_continue_as_guest_button_text_color" class="wpsc_color_picker" name="get_login_form[wpsc_continue_as_guest_button_text_color]" value="<?php echo ($wpsc_appearance_login_form['wpsc_continue_as_guest_button_text_color']);?>" />
	      </div>
				<div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_continue_as_guest_button_border_color" class="wpsc_color_picker" name="get_login_form[wpsc_continue_as_guest_button_border_color]" value="<?php echo $wpsc_appearance_login_form['wpsc_continue_as_guest_button_border_color'] ?>" />
				</div>
     </div> 
  </div>
 

 
	 <button type="submit" class="btn btn-success" style="margin-bottom:10px;"><?php _e('Save Changes','supportcandy');?></button>
	 <button type="button" onclick="wpsc_reset_default_appperance_login_form_settings();" class="btn btn-sm btn-default" style="margin-bottom:10px; font-size:15px;"><?php _e('Reset Default','supportcandy');?></button>
	 <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
 	<input type="hidden" name="action" value="wpsc_appearance_settings" />
 	<input type="hidden" name="setting_action" value="set_appearance_login_form" /> 
 	 </form>
 <script>
   jQuery(document).ready(function(){
       jQuery('.wpsc_color_picker').wpColorPicker();
   });
 </script>
 <script>
 	function wpsc_reset_default_appperance_login_form_settings()
		{
		//	jQuery('.wpsc_submit_wait').show();

			var data = {
				action: 'wpsc_appearance_settings',
				setting_action : 'get_reset_default_apperance_login_form'
			};

			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				var response = JSON.parse(response_str);
				if (response.sucess_status=='1') {
					jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				}
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_appearance_login_form();
			});
		}
	
 </script>