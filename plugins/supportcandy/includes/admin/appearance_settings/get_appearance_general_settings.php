<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_general_settings = get_option('wpsc_appearance_general_settings');
?>
<div id="wpsc_appearance_settings">
	<form id="wpsc_frm_appearance_general_settings" method="post" action="javascript:wpsc_set_appearance_general_settings();">
	 
	 <div class="form-group">
		 <label for="wpsc_status_bg_color"><?php _e('Background Color','supportcandy');?></label>
		 <div class="row">
			 <div class="col-sm-12" style="">
				 <p class="help-block"><?php _e('Background color of container for all screens including ticket list, open ticket, create ticket, etc.','supportcandy');?></p>
				 <input id="wpsc_bg_color" class="wpsc_color_picker" name="general_settings[wpsc_bg_color]" value="<?php echo $wpsc_appearance_general_settings['wpsc_bg_color']?>" />
			 </div>
		 </div>
	 </div>
	 
	 <div class="form-group">
		 <label for="wpsc_status_color"><?php _e('Color','supportcandy');?></label>
		 <div class="row">
			  <div class="col-sm-12" style="">
					<p class="help-block"><?php _e('Text color of container for all screens including ticket list, open ticket, create ticket, etc.','supportcandy');?></p>
		 		  <input id="wpsc_text_color" class="wpsc_color_picker" name="general_settings[wpsc_text_color]" value="<?php echo $wpsc_appearance_general_settings['wpsc_text_color']?>" />
		 	 </div>
		</div>
	</div>
	
	<div class="form-group">
		 <label for="wpsc_action_bar_color"><?php _e('Action Bar Color','supportcandy');?></label>
		 <div class="row">
			  <div class="col-sm-12">
					<p class="help-block"><?php _e('Color of action bar for all screens. This color also gets applied to container border','supportcandy');?></p>
		 		  <input id="wpsc_action_bar_color" class="wpsc_color_picker" name="general_settings[wpsc_action_bar_color]" value="<?php echo $wpsc_appearance_general_settings['wpsc_action_bar_color']?>" />
				</div>
		</div>
	</div>
	 
	 <div class="form-group">
		 <label for="wpsc_crt_ticket_action_bar_color"><?php _e('Create Ticket Button (Action Bar)','supportcandy');?></label></br>
		 <div class="row">
			  <div class="col-sm-4" id="wpsc_crt_ticket_btn_action_bar_bg_color">
				  <p class="help-block"><?php _e('Background color','supportcandy');?></p>
				  <input id="wpsc_crt_ticket_btn_action_bar_bg_color" class="wpsc_color_picker" name="general_settings[wpsc_crt_ticket_btn_action_bar_bg_color]" value="<?php echo $wpsc_appearance_general_settings['wpsc_crt_ticket_btn_action_bar_bg_color']?>"/>
			  </div>
			 <div class="col-sm-4" id="wpsc_crt_ticket_btn_action_bar_text_color">
				 <p class="help-block"><?php _e('Color','supportcandy');?></p>
				 <input id="wpsc_crt_ticket_btn_action_bar_text_color" class="wpsc_color_picker" name="general_settings[wpsc_crt_ticket_btn_action_bar_text_color]" value="<?php echo $wpsc_appearance_general_settings['wpsc_crt_ticket_btn_action_bar_text_color']?>" />
			 </div>
		 </div>
	</div>
	 
	 <div class="form-group">
		 <label for="wpsc_default_buttons_action_bar_color"><?php _e('Default Buttons (Action Bar)','supportcandy');?></label></br>
		 <div class="row">
			  <div class="col-sm-4" id="wpsc_default_btn_action_bar_bg_color">
					<p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_default_btn_action_bar_bg_color" class="wpsc_color_picker" name="general_settings[wpsc_default_btn_action_bar_bg_color]" value="<?php echo $wpsc_appearance_general_settings['wpsc_default_btn_action_bar_bg_color']?>" />
				</div>
				
				<div class="col-sm-4" id="wpsc_crt_ticket_btn_action_bar_text_color">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_default_btn_action_bar_text_color" class="wpsc_color_picker" name="general_settings[wpsc_default_btn_action_bar_text_color]" value="<?php echo $wpsc_appearance_general_settings['wpsc_default_btn_action_bar_text_color']?>" />
				</div>
		</div>
	</div>
	
	<div class="form-group">
		<label for="wpsc_default_buttons_action_bar_color"><?php _e('Sign Out Button','supportcandy');?></label></br>
		<div class="row">
			 <div class="col-sm-4" id="wpsc_sign_out_bg_color">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
				 <input id="wpsc_default_btn_action_bar_bg_color" class="wpsc_color_picker" name="general_settings[wpsc_sign_out_bg_color]" value="<?php echo $wpsc_appearance_general_settings['wpsc_sign_out_bg_color']?>" />
			 </div>
			 
			 <div class="col-sm-4" id="wpsc_crt_ticket_btn_action_bar_text_color">
				 <p class="help-block"><?php _e('Color','supportcandy');?></p>
				 <input id="wpsc_sign_out_text_color" class="wpsc_color_picker" name="general_settings[wpsc_sign_out_text_color]" value="<?php echo $wpsc_appearance_general_settings['wpsc_sign_out_text_color']?>" />
			 </div>
	 </div>
 </div>
 	 
	<button type="submit" id="wpsc_submit_app_gen_btn" class="btn btn-success" style="margin-bottom:10px;"><?php _e('Save Changes','supportcandy');?></button>
  <button type="button" id="wpsc_reset_defult_app_general_btn" onclick="wpsc_reset_default_general_settings()" class="btn btn-sm btn-default" style="margin-bottom:10px; font-size:15px;"><?php _e('Reset Default','supportcandy');?></button>
	<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
	<input type="hidden" name="action" value="wpsc_appearance_settings" />
	<input type="hidden" name="setting_action" value="set_appearance_general_settings" />
</form>

</div>

<script>
  jQuery(document).ready(function(){
      jQuery('.wpsc_color_picker').wpColorPicker();
  });
</script>