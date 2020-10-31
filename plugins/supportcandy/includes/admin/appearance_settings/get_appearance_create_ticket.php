<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_create_ticket = get_option('wpsc_create_ticket');
?>
<div id="wpsc_appearance_settings">
	<form id="wpsc_frm_appearance_create_ticket_settings" method="post" action="javascript:wpsc_set_appearance_create_ticket_settings();">
	 
	 <div class="form-group">
      <label for="wpsc_ticket_widgets_color"><?php _e('Submit Button','supportcandy');?></label></br>
      <div class="row" >
         <div class="col-sm-4" style="">
           <p class="help-block"><?php _e('Background color','supportcandy');?></p>
     			 <input id="wpsc_submit_button_bg_color" class="wpsc_color_picker" name="appearance_create_ticket[wpsc_submit_button_bg_color]?>" value="<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_bg_color'] ?>"/>
         </div>
         <div class="col-sm-4" style="">
     			 <p class="help-block"><?php _e('Color','supportcandy');?></p>
     			 <input id="wpsc_submit_button_text_color" class="wpsc_color_picker" name="appearance_create_ticket[wpsc_submit_button_text_color]" value="<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_text_color'] ?>" />
         </div>
         <div class="col-sm-4" style="">
     			 <p class="help-block"><?php _e('Border Color','supportcandy');?></p>
     			 <input id="wpsc_submit_button_border_color" class="wpsc_color_picker" name="appearance_create_ticket[wpsc_submit_button_border_color]" value="<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_border_color'] ?>" />
         </div>
     </div>
   </div>
	 
	 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Reset Button','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_reset_button_bg_color" class="wpsc_color_picker" name="appearance_create_ticket[wpsc_reset_button_bg_color]?>" value="<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_reset_button_text_color" class="wpsc_color_picker" name="appearance_create_ticket[wpsc_reset_button_text_color]" value="<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_text_color'] ?>" />
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_reset_button_border_color" class="wpsc_color_picker" name="appearance_create_ticket[wpsc_reset_button_border_color]" value="<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_border_color'] ?>" />
			 </div>
	 </div>
 </div>
	 
 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Captcha','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_captcha_bg_color" class="wpsc_color_picker" name="appearance_create_ticket[wpsc_captcha_bg_color]?>" value="<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_captcha_text_color" class="wpsc_color_picker" name="appearance_create_ticket[wpsc_captcha_text_color]" value="<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_text_color'] ?>" />
			 </div>
	 </div>
 </div>
 
 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Extra Information','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-12" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_extra_info_text_color" class="wpsc_color_picker" name="appearance_create_ticket[wpsc_extra_info_text_color]?>" value="<?php echo $wpsc_appearance_create_ticket['wpsc_extra_info_text_color'] ?>"/>
			 </div>
	 </div>
 </div>
 
  <button type="submit" id="wpsc_submit_app_tic_page_btn" class="btn btn-success" style="margin-bottom:10px;"><?php _e('Save Changes','supportcandy');?></button>
  <button type="button" id="wpsc_reset_defult_app_tic_page_btn" onclick="wpsc_reset_default_create_ticket_settings()" class="btn btn-sm btn-default" style="margin-bottom:10px; font-size:15px;"><?php _e('Reset Default','supportcandy');?></button>
	<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
	<input type="hidden" name="action" value="wpsc_appearance_settings" />
	<input type="hidden" name="setting_action" value="set_appearance_create_ticket_settings" />
	 
	</form>

</div>

<script>
  jQuery(document).ready(function(){
      jQuery('.wpsc_color_picker').wpColorPicker();
  });
</script>