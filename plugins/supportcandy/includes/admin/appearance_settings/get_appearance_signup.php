<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_signup = get_option('wpsc_appearance_signup');
?>

<div id="wpsc_appearance_settings">
	<form id="wpsc_frm_appearnce_signup_settings" method="post" action="javascript:wpsc_set_appearance_sign_up();">
	
	 <div class="form-group">
			 <label for="wpsc_appearance_signup_button"><?php _e('Register Button ','supportcandy');?></label></br>
	     <div class="row">
	        <div class="col-sm-4" style="" id="wpsc_appearance_signup_button_bg_color">
		         <p class="help-block"><?php _e('Background color','supportcandy');?></p>
		  			 <input id="wpsc_appearance_signup_button_bg_color" class="wpsc_color_picker" name="wpsc_appearance_signup[wpsc_appearance_signup_button_bg_color]" value="<?php echo $wpsc_appearance_signup['wpsc_appearance_signup_button_bg_color']?>" />
	  		  </div>
	        
	        <div class="col-sm-4" style="" id="wpsc_appearance_signup_button_text_color">
		         <p class="help-block"><?php _e('Color','supportcandy');?></p>
		         <input id="wpsc_appearance_signup_button_text_color" class="wpsc_color_picker" name="wpsc_appearance_signup[wpsc_appearance_signup_button_text_color]" value="<?php echo $wpsc_appearance_signup['wpsc_appearance_signup_button_text_color']?>" />
	        </div>
				 
				  <div class="col-sm-4" style="">
						 <p class="help-block"><?php _e('Border Color','supportcandy');?></p>
						 <input id="wpsc_appearance_signup_button_border_color" class="wpsc_color_picker" name="wpsc_appearance_signup[wpsc_appearance_signup_button_border_color]" value="<?php echo $wpsc_appearance_signup['wpsc_appearance_signup_button_border_color'] ?>" />
				  </div>
	    </div>
  </div>
  
	 <div class="form-group">
      <label for="wpsc_appearance_cancel_button"><?php _e('Cancel Button','supportcandy');?></label></br>
      <div class="row">
	        <div class="col-sm-4" id="wpsc_appearance_cancel_button_bg_color">
	          <p class="help-block"><?php _e('Background color','supportcandy');?></p>
	          <input id="wpsc_appearance_cancel_button_bg_color" class="wpsc_color_picker" name="wpsc_appearance_signup[wpsc_appearance_cancel_button_bg_color]" value="<?php echo $wpsc_appearance_signup['wpsc_appearance_cancel_button_bg_color']?>" />
	        </div>
	        
	        <div class="col-sm-4" id="wpsc_appearance_cancel_button_text_color">
	          <p class="help-block"><?php _e('Color','supportcandy');?></p>
	          <input id="wpsc_appearance_cancel_button_text_color" class="wpsc_color_picker" name="wpsc_appearance_signup[wpsc_appearance_cancel_button_text_color]" value="<?php echo $wpsc_appearance_signup['wpsc_appearance_cancel_button_text_color']?>" />
	        </div>
					
					<div class="col-sm-4" style="">
	 				 <p class="help-block"><?php _e('Border Color','supportcandy');?></p>
	 				 <input id="wpsc_appearance_cancel_button_border_color" class="wpsc_color_picker" name="wpsc_appearance_signup[wpsc_appearance_cancel_button_border_color]" value="<?php echo $wpsc_appearance_signup['wpsc_appearance_cancel_button_border_color'] ?>" />
	 			 </div>
     </div>
  </div>
	 
	<button type="submit" class="btn btn-success" style="margin-bottom:10px;"><?php _e('Save Changes','supportcandy');?></button>
  <button type="button" onclick="wpsc_reset_appearance_signup_form()" class="btn btn-sm btn-default" style="margin-bottom:10px; font-size:15px;"><?php _e('Reset Default','supportcandy');?></button>
	<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
	<input type="hidden" name="action" value="wpsc_appearance_settings" />
	<input type="hidden" name="setting_action" value="set_appearance_signup" />
</form>

</div>

<script>
  jQuery(document).ready(function(){
      jQuery('.wpsc_color_picker').wpColorPicker();
  });
  </script>
  