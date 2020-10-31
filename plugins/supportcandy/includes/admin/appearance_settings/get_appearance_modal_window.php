<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

?>
<div id="wpsc_appearance_settings">
	<form id="wpsc_frm_appearance_modal_window_settings" method="post" action="javascript:wpsc_set_appearance_modal_window_settings();">
	 
	 <div class="form-group">
      <label for="wpsc_ticket_widgets_color"><?php _e('Header','supportcandy');?></label></br>
      <div class="row" >
         <div class="col-sm-4" style="">
           <p class="help-block"><?php _e('Background color','supportcandy');?></p>
     			 <input id="wpsc_header_bg_color" class="wpsc_color_picker" name="appearance_modal_window[wpsc_header_bg_color]?>" value="<?php echo $wpsc_appearance_modal_window['wpsc_header_bg_color'] ?>"/>
         </div>
         <div class="col-sm-4" style="">
     			 <p class="help-block"><?php _e('Color','supportcandy');?></p>
     			 <input id="wpsc_header_text_color" class="wpsc_color_picker" name="appearance_modal_window[wpsc_header_text_color]" value="<?php echo $wpsc_appearance_modal_window['wpsc_header_text_color'] ?>" />
         </div>
     </div>
   </div>
	 
	 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Footer','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_footer_bg_color" class="wpsc_color_picker" name="appearance_modal_window[wpsc_footer_bg_color]?>" value="<?php echo $wpsc_appearance_modal_window['wpsc_footer_bg_color'] ?>"/>
			 </div>
	 </div>
 </div>
	 
 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Close Button','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_close_button_bg_color" class="wpsc_color_picker" name="appearance_modal_window[wpsc_close_button_bg_color]?>" value="<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_close_button_text_color" class="wpsc_color_picker" name="appearance_modal_window[wpsc_close_button_text_color]" value="<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color'] ?>" />
			 </div>
	 </div>
 </div>
 
 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Action Button','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_action_button_bg_color" class="wpsc_color_picker" name="appearance_modal_window[wpsc_action_button_bg_color]?>" value="<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_action_button_text_color" class="wpsc_color_picker" name="appearance_modal_window[wpsc_action_button_text_color]" value="<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color'] ?>" />
			 </div>
	 </div>
 </div>
 
  <button type="submit" class="btn btn-success" id="wpsc_submit_app_modal_window_btn" style="margin-bottom:10px;"><?php _e('Save Changes','supportcandy');?></button>
  <button type="button" id="wpsc_reset_defult_app_modal_window_btn" onclick="wpsc_reset_default_modal_window_settings()" class="btn btn-sm btn-default" style="margin-bottom:10px; font-size:15px;"><?php _e('Reset Default','supportcandy');?></button>
	<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
	<input type="hidden" name="action" value="wpsc_appearance_settings" />
	<input type="hidden" name="setting_action" value="set_appearance_modal_window_settings" />
	 
	</form>

</div>

<script>
  jQuery(document).ready(function(){
      jQuery('.wpsc_color_picker').wpColorPicker();
  });
</script>