<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_ticket_list = get_option('wpsc_appearance_ticket_list');
?>
<div id="wpsc_appearance_settings">
	<form id="wpsc_frm_appearance_ticket_list_settings" method="post" action="javascript:wpsc_set_appearance_ticket_list_settings();">
	 
	 <div class="form-group">
      <label for="wpsc_filter_widgets_color"><?php _e('Filter Widgets','supportcandy');?></label></br>
      <div class="row" >
         <div class="col-sm-4" style="">
           <p class="help-block"><?php _e('Background color','supportcandy');?></p>
     			 <input id="wpsc_filter_widgets_bg_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_filter_widgets_bg_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_filter_widgets_bg_color']?>"/>
         </div>
         <div class="col-sm-4" style="">
     			 <p class="help-block"><?php _e('Color','supportcandy');?></p>
     			 <input id="wpsc_filter_widgets_text_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_filter_widgets_text_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_filter_widgets_text_color']?>" />
         </div>
         <div class="col-sm-4" style="">
     			 <p class="help-block"><?php _e('Border Color','supportcandy');?></p>
     			 <input id="wpsc_filter_widgets_border_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_filter_widgets_border_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_filter_widgets_border_color']?>" />
         </div>
     </div>
   </div>
     
	 <div class="form-group">
		 <label for="wpsc_default_buttons_action_bar_color"><?php _e('Ticket List Header ','supportcandy');?></label></br>
     <div class="row">
        <div class="col-sm-4" style="" id="wpsc_ticket_list_header_bg_color">
         <p class="help-block"><?php _e('Background color','supportcandy');?></p>
  			 <input id="wpsc_ticket_list_header_bg_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_ticket_list_header_bg_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_header_bg_color']?>" />
  		  </div>
        <div class="col-sm-4" style="" id="wpsc_ticket_list_header_text_color">
         <p class="help-block"><?php _e('Color','supportcandy');?></p>
         <input id="wpsc_ticket_list_header_text_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_ticket_list_header_text_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_header_text_color']?>" />
       </div>
    </div>
  </div>
  
	 <div class="form-group">
      <label for="wpsc_default_buttons_action_bar_color"><?php _e('Ticket List Item Mouseover','supportcandy');?></label></br>
      <div class="row">
        <div class="col-sm-4" id="wpsc_ticket_list_item_mo_bg_color">
          <p class="help-block"><?php _e('Background color','supportcandy');?></p>
          <input id="wpsc_ticket_list_item_mo_bg_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_ticket_list_item_mo_bg_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_item_mo_bg_color']?>" />
        </div>
        <div class="col-sm-4" id="wpsc_ticket_list_item_mo_text_color">
          <p class="help-block"><?php _e('Color','supportcandy');?></p>
          <input id="wpsc_ticket_list_item_mo_text_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_ticket_list_item_mo_text_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_item_mo_text_color']?>" />
        </div>
      </div>
  </div>
	
	<div class="form-group">
		 <label for="wpsc_search_box_custom_filter"><?php _e('Advance Filter Apply Button','supportcandy');?></label></br>
		 <div class="row" >
				<div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_apply_filter_btn_bg_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_apply_filter_btn_bg_color]?>" value="<?php echo $wpsc_appearance_ticket_list['wpsc_apply_filter_btn_bg_color'] ?>"/>
				</div>
				<div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_apply_filter_btn_text_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_apply_filter_btn_text_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_apply_filter_btn_text_color'] ?>" />
				</div>
				<div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_apply_filter_btn_border_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_apply_filter_btn_border_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_apply_filter_btn_border_color'] ?>" />
				</div>
		</div>
	</div>
	
	<div class="form-group">
		 <label for="wpsc_search_box_custom_filter"><?php _e('Advance Filter Save Button','supportcandy');?></label></br>
		 <div class="row" >
				<div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Background color','supportcandy');?></p>
					<input id="wpsc_save_filter_btn_bg_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_save_filter_btn_bg_color]?>" value="<?php echo $wpsc_appearance_ticket_list['wpsc_save_filter_btn_bg_color'] ?>"/>
				</div>
				<div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Color','supportcandy');?></p>
					<input id="wpsc_save_filter_btn_text_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_save_filter_btn_text_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_save_filter_btn_text_color'] ?>" />
				</div>
				<div class="col-sm-4" style="">
					<p class="help-block"><?php _e('Border Color','supportcandy');?></p>
					<input id="wpsc_save_filter_btn_border_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_save_filter_btn_border_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_save_filter_btn_border_color'] ?>" />
				</div>
		</div>
	</div>
	 
	<div class="form-group">
		<label for="wpsc_search_box_custom_filter"><?php _e('Advance Filter Close Button','supportcandy');?></label></br>
		<div class="row" >
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Background color','supportcandy');?></p>
				 <input id="wpsc_close_filter_btn_bg_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_close_filter_btn_bg_color]?>" value="<?php echo $wpsc_appearance_ticket_list['wpsc_close_filter_btn_bg_color'] ?>"/>
			 </div>
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Color','supportcandy');?></p>
				 <input id="wpsc_close_filter_btn_text_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_close_filter_btn_text_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_close_filter_btn_text_color'] ?>" />
			 </div>
			 <div class="col-sm-4" style="">
				 <p class="help-block"><?php _e('Border Color','supportcandy');?></p>
				 <input id="wpsc_close_filter_btn_border_color" class="wpsc_color_picker" name="appearance_ticket_list[wpsc_close_filter_btn_border_color]" value="<?php echo $wpsc_appearance_ticket_list['wpsc_close_filter_btn_border_color'] ?>" />
			 </div>
	 </div>
 </div>

		<button type="submit" id="wpsc_submit_app_ticket_list_btn" class="btn btn-success" style="margin-bottom:10px;"><?php _e('Save Changes','supportcandy');?></button>
	  <button type="button" id="wpsc_reset_defult_app_ticket_list_btn" onclick="wpsc_reset_default_ticket_list_settings()" class="btn btn-sm btn-default" style="margin-bottom:10px; font-size:15px;"><?php _e('Reset Default','supportcandy');?></button>
		<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
		<input type="hidden" name="action" value="wpsc_appearance_settings" />
		<input type="hidden" name="setting_action" value="set_appearance_ticket_list_settings" />
	 
	</form>

</div>

<script>
  jQuery(document).ready(function(){
      jQuery('.wpsc_color_picker').wpColorPicker();
  });
</script>