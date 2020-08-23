<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}
$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

?>
<div id="wpsc_appearance_settings">
	<form id="wpsc_frm_appearance_individual_ticket_settings" method="post" action="javascript:wpsc_set_appearance_individual_ticket_settings();">
	 
	 <div class="form-group">
      <label for="wpsc_ticket_widgets_color"><?php _e('Ticket Widgets','supportcandy');?></label></br>
      <div class="row" >
         <div class="col-sm-4" style="">
           <p class="help-block"><?php _e('Background color','supportcandy');?></p>
     			 <input id="wpsc_ticket_widgets_bg_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_ticket_widgets_bg_color]?>" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color'] ?>"/>
         </div>
         <div class="col-sm-4" style="">
     			 <p class="help-block"><?php _e('Color','supportcandy');?></p>
     			 <input id="wpsc_ticket_widgets_text_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_ticket_widgets_text_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color'] ?>" />
         </div>
         <div class="col-sm-4" style="">
     			 <p class="help-block"><?php _e('Border Color','supportcandy');?></p>
     			 <input id="wpsc_ticket_widgets_border_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_ticket_widgets_border_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color'] ?>" />
         </div>
     </div>
   </div>
	 
	 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Report Thread','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_report_thread_bg_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_report_thread_bg_color]?>" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_report_thread_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_report_thread_text_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_report_thread_text_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_report_thread_text_color'] ?>" />
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_report_thread_border_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_report_thread_border_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_report_thread_border_color'] ?>" />
			 </div>
	 </div>
 </div>
	 
 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Reply Thread (Customer)','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_reply_thread_customer_bg_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_reply_thread_customer_bg_color]?>" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_reply_thread_customer_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_reply_thread_customer_text_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_reply_thread_customer_text_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_reply_thread_customer_text_color'] ?>" />
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_reply_thread_customer_border_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_reply_thread_customer_border_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_reply_thread_customer_border_color'] ?>" />
			 </div>
	 </div>
 </div>
 
 <div class="form-group">
	 <label for="wpsc_ticket_widgets_color"><?php _e('Reply Thread (Agent)','supportcandy');?></label></br>
	 <div class="row" >
			<div class="col-sm-4" style="">
				<p class="help-block"><?php _e('Background color','supportcandy');?></p>
				 <input id="wpsc_reply_thread_bg_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_reply_thread_bg_color]?>" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_reply_thread_bg_color'] ?>"/>
			</div>
			<div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Color','supportcandy');?></p>
				 <input id="wpsc_reply_thread_text_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_reply_thread_text_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_reply_thread_text_color'] ?>" />
			</div>
			<div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Border Color','supportcandy');?></p>
				 <input id="wpsc_reply_thread_border_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_reply_thread_border_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_reply_thread_border_color'] ?>" />
			</div>
	</div>
 </div>
 
 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Private Note','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_private_note_bg_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_private_note_bg_color]?>" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_private_note_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_private_note_text_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_private_note_text_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_private_note_text_color'] ?>" />
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_private_note_border_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_private_note_border_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_private_note_border_color'] ?>" />
			 </div>
	 </div>
 </div>
 
 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Ticket Logs','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_ticket_logs_bg_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_ticket_logs_bg_color]?>" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_logs_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_ticket_logs_text_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_ticket_logs_text_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_logs_text_color'] ?>" />
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_ticket_logs_border_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_ticket_logs_border_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_logs_border_color'] ?>" />
			 </div>
	 </div>
 </div>
 
 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Submit Reply Button','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_submit_reply_btn_bg_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_submit_reply_btn_bg_color]?>" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_submit_reply_btn_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_submit_reply_btn_text_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_submit_reply_btn_text_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_submit_reply_btn_text_color'] ?>" />
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_submit_reply_btn_border_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_submit_reply_btn_border_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_submit_reply_btn_border_color'] ?>" />
			 </div>
	 </div>
 </div>
 
 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Other Reply Form Buttons','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_other_reply_form_btn_bg_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_other_reply_form_btn_bg_color]?>" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_other_reply_form_btn_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_other_reply_form_btn_text_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_other_reply_form_btn_text_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_other_reply_form_btn_text_color'] ?>" />
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_other_reply_form_btn_border_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_other_reply_form_btn_border_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_other_reply_form_btn_border_color'] ?>" />
			 </div>
	 </div>
 </div>
 
 <div class="form-group">
		<label for="wpsc_ticket_widgets_color"><?php _e('Edit Buttons','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_edit_btn_bg_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_edit_btn_bg_color]?>" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_edit_btn_text_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_edit_btn_text_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'] ?>" />
			 </div>
			 <div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_edit_btn_border_color" class="wpsc_color_picker" name="individual_ticket_page[wpsc_edit_btn_border_color]" value="<?php echo $wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'] ?>" />
			 </div>
	 </div>
 </div>
	 
 	<button type="submit" id="wpsc_submit_app_individual_btn" class="btn btn-success" style="margin-bottom:10px;"><?php _e('Save Changes','supportcandy');?></button>
  <button type="button" id="wpsc_reset_defult_app_individual_btn" onclick="wpsc_reset_default_individual_ticket_settings()" class="btn btn-sm btn-default" style="margin-bottom:10px; font-size:15px;"><?php _e('Reset Default','supportcandy');?></button>
	<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
	<input type="hidden" name="action" value="wpsc_appearance_settings" />
	<input type="hidden" name="setting_action" value="set_appearance_individual_ticket_settings" />
	 
	</form>

</div>

<script>
  jQuery(document).ready(function(){
      jQuery('.wpsc_color_picker').wpColorPicker();
  });
</script>