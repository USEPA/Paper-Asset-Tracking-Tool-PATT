<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_custom_ticket_count = get_option('wpsc_custom_ticket_count');
$wpsc_thread_limit = get_option('wpsc_thread_limit');
?>

<form id="wpsc_frm_advanced_settings" method="post" action="javascript:wpsc_set_advanced_settings();">
  <div class="form-group">
    <label for="wpsc_ticket_url_permission"><?php _e('Ticket URL Permissions','supportcandy');?></label>
    <select class="form-control" name="wpsc_ticket_url_permission" id="wpsc_ticket_url_permission">
      <?php
      $wpsc_ticket_url_permission = get_option('wpsc_ticket_url_permission');
      $selected = $wpsc_ticket_url_permission == '1' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="1">'.__('Anyone can see','supportcandy').'</option>';
      $selected = $wpsc_ticket_url_permission == '0' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="0">'.__('Login required','supportcandy').'</option>';
      ?>
    </select>
  </div>

	<div class="form-group">
		<label for="wpsc_sign_out"><?php _e('Logout button','supportcandy');?></label>
		<p class="help-block"><?php _e("Enable/Disable logout button on ticket list action bar.","supportcandy");?></p>
		<select class="form-control" name="wpsc_sign_out" id="wpsc_sign_out">
			<?php
			$wpsc_allow_sign_out = get_option('wpsc_sign_out');
			$selected = $wpsc_allow_sign_out == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_allow_sign_out == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="wpsc_ticket_public_mode"><?php _e('Public Mode','supportcandy');?></label>
		<p class="help-block"><?php _e("If you enable this setting then all tickets will be visible to all users and they can reply to each others tickets.","supportcandy");?></p>
		<select class="form-control" name="wpsc_ticket_public_mode" id="wpsc_ticket_public_mode">
			<?php
			$wpsc_ticket_public_mode = get_option('wpsc_ticket_public_mode');
			$selected = $wpsc_ticket_public_mode == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_ticket_public_mode == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select >
	</div>

    <div class="form-group" id="wpsc_allow_reply_to_public_tickets" style="<?php echo !$wpsc_ticket_public_mode?'display:none;':'';?>">
		<label for="wpsc_allow_reply_to_public_tickets"><?php _e('Allow others to reply to public tickets','supportcandy');?></label>
		<p class="help-block"><?php _e("Set whether or not users other than raised by can reply to public tickets.","supportcandy");?></p>
		<select class="form-control" name="wpsc_allow_reply_to_public_tickets" id="wpsc_allow_reply_to_public_tickets">
			<?php
			$wpsc_allow_reply_to_public_tickets = get_option('wpsc_allow_reply_to_public_tickets');
			$selected = $wpsc_allow_reply_to_public_tickets == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_allow_reply_to_public_tickets == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select>
	</div>
	<div class="form-group">
		<label for="wpsc_show_and_hide_filters"><?php _e('Ticket list filters default visibility','supportcandy');?></label>
		<p class="help-block"><?php _e("Default show/hide filters on ticket list on page load.","supportcandy");?></p>
		<select class="form-control" name="wpsc_show_and_hide_filters" id="wpsc_show_and_hide_filters">
			<?php
			$wpsc_show_and_hide_filters = get_option('wpsc_show_and_hide_filters');
			$selected = $wpsc_show_and_hide_filters == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Show','supportcandy').'</option>';
			$selected = $wpsc_show_and_hide_filters == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Hide','supportcandy').'</option>';
			?>
		</select>
	</div>

	<div class="form-group">
		<label for="wpsc_allow_reply_confirmation"><?php _e('Allow Reply Confirmation','supportcandy');?></label>
		<p class="help-block"><?php _e("If you enable this setting, it will ask confirmation before submitting reply.","supportcandy");?></p>
		<select class="form-control" name="wpsc_allow_reply_confirmation" id="wpsc_allow_reply_confirmation">
			<?php
			$wpsc_allow_reply_confirmation = get_option('wpsc_allow_reply_confirmation');
			$selected = $wpsc_allow_reply_confirmation == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_allow_reply_confirmation == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="wpsc_thread_date_format"><?php _e('Thread Date Format','supportcandy');?></label>
		<p class="help-block"><?php _e("This format will be applicable for thread log time.","supportcandy");?></p>
		<select class="form-control" name="wpsc_thread_date_format" id="wpsc_thread_date_format">
			<?php
			$wpsc_thread_date_format = get_option('wpsc_thread_date_format');
			$selected = $wpsc_thread_date_format == 'string' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="string">'.__('In words (eg. 1 min ago)','supportcandy').'</option>';
			$selected = $wpsc_thread_date_format == 'timestamp' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="timestamp">'.__('Timestamp','supportcandy').'</option>';
			?>
		</select>
		<?php $wpsc_thread_dt_format = get_option('wpsc_thread_date_time_format'); ?>
		<div id="date_time_format" style="<?php echo $wpsc_thread_date_format=='timestamp'?'display:block;':'display:none;' ?>">
			<label for="wpsc_thread_date_time_format" style="margin-top:10px"><?php _e('Timestamp Format','supportcandy');?></label>
			 <?php $wpsc_thread_date_time_format = get_option('wpsc_thread_date_time_format');?>
			<input type="text" class="form-control" id="wpsc_thread_date_time_format" name="wpsc_thread_date_time_format" value="<?php echo $wpsc_thread_date_time_format ?>">
		</div>
	</div>
	
	<div class="form-group">
		<label for="wpsc_notify"><?php _e('Do Not Notify Owner','supportcandy');?></label>
		<p class="help-block"><?php _e("Enable or Disable Do Not Notify Owner checkbox in create ticket.","supportcandy");?></p>
		<select class="form-control" name="wpsc_notify" id="wpsc_notify">
			<?php
			$wpsc_notify = get_option('wpsc_do_not_notify_setting');
			$selected = $wpsc_notify == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_notify == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select>
	</div>
	<?php $wpsc_notify = get_option('wpsc_do_not_notify_setting'); ?>
	<div class="form-group" id="wpsc_do_not_notify_option" style= <?php echo $wpsc_notify=='1'?'display:block;':'display:none;'?>>
		<label for="wpsc_notify_checkbox"><?php _e("Don't Notify Owner Checkbox",'supportcandy');?></label>
		<p class="help-block"><?php _e("Checked or Unchecked Don't Notify Owner checkbox in create ticket.","supportcandy");?></p>
		<select class="form-control" name="wpsc_notify_checkbox" id="wpsc_notify_checkbox">
			<?php
			$wpsc_notify_checkbox = get_option('wpsc_default_do_not_notify_option');
			$selected = $wpsc_notify_checkbox == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Checked','supportcandy').'</option>';
			$selected = $wpsc_notify_checkbox == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Unchecked','supportcandy').'</option>';
			?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="wpsc_hide_show_priority"><?php _e('Priority Visibility','supportcandy');?></label>
		<p class="help-block"><?php _e("Show/Hide priority in individual ticket for customers.","supportcandy");?></p>
		<select class="form-control" name="wpsc_hide_show_priority" id="wpsc_hide_show_priority">
			<?php
			$wpsc_hide_show_priority = get_option('wpsc_hide_show_priority');
			$selected = $wpsc_hide_show_priority == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Show','supportcandy').'</option>';
			$selected = $wpsc_hide_show_priority == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Hide','supportcandy').'</option>';
			?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="wpsc_view_more"><?php _e('Ticket Thread View More','supportcandy');?></label>
		<p class="help-block"><?php _e("Enable/Disable View More button in individual ticket thread. This will show full content of the thread if disabled.","supportcandy");?></p>
		<select class="form-control" name="wpsc_view_more" id="wpsc_view_more">
			<?php
			$wpsc_view_more = get_option('wpsc_view_more');
			$selected = $wpsc_view_more == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_view_more == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select>
	</div>

	<div class="form-group">
		<label for="wpsc_on_and_off_auto_refresh"><?php _e('Auto Refresh Default Status','supportcandy');?></label>
		<p class="help-block"><?php _e("Default status of auto-refresh on ticket list on page load.","supportcandy");?></p>
		<select class="form-control" name="wpsc_on_and_off_auto_refresh" id="wpsc_on_and_off_auto_refresh">
			<?php
			$wpsc_on_and_off_auto_refresh = get_option('wpsc_on_and_off_auto_refresh');
			$selected = $wpsc_on_and_off_auto_refresh == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('On','supportcandy').'</option>';
			$selected = $wpsc_on_and_off_auto_refresh == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Off','supportcandy').'</option>';
			?>
		</select>
	</div>


	<div class="form-group">
		<label for="wpsc_sign_out"><?php _e('Ticket ID','supportcandy');?></label>
		<p class="help-block"><?php _e("This is applicable for Ticket id being created for new ticket.","supportcandy");?></p>
		<select class="form-control" name="wpsc_ticket_id_type" id="wpsc_ticket_id_type">
			<?php
			$wpsc_ticket_id_type  = get_option('wpsc_ticket_id_type');
			$selected = $wpsc_ticket_id_type == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Sequential','supportcandy').'</option>';
			$selected = $wpsc_ticket_id_type == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Random','supportcandy').'</option>';
			?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="wpsc_custom_ticket_number"><?php _e('Starting Ticket ID','supportcandy');?></label>
		<p class="help-block"><?php _e("This is applicable only for Sequential ticket id type.","supportcandy");?></p>
		<div class="row">
				<div class="col-sm-6" style="padding-left:0px;" id="wpsc_custom_ticket_start_number">
					<input type="number" class="form-control" name="wpsc_custom_ticket_count" id="wpsc_custom_ticket_count" value="<?php echo htmlentities($wpsc_custom_ticket_count)?>" />
				</div>
				<div class="col-sm-6">
				 <button  id="wpsc_ticket_count_btn" type="button" class="btn btn-success" onclick="wpsc_custom_ticket_number();"><?php _e('Save','supportcandy');?></button>
				 <img class="wpsc_submit_wait_1" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
				 <input type="hidden" name="action" value="wpsc_settings" />
				 <input type="hidden" name="setting_action" value="custom_start_ticket_number" />
			 </div>
	 </div>
	</div>
	
	<div class="form-group">
		<label for="wpsc_thread_limit"><?php _e('Ticket History Limit','supportcandy');?></label>
		<p class="help-block"><?php _e("Number of threads for ticket history macro.","supportcandy");?></p>
		<div class="row">
			<input type="number"  min="1" class="form-control" name="wpsc_thread_limit" id="wpsc_thread_limit" value = "<?php echo htmlentities($wpsc_thread_limit) ?>"/>
	 </div>
	</div>
	
		<div class="form-group">
		<label for="wpsc_redirect_to_ticket_list"><?php _e('Ticket reply redirect','supportcandy');?></label>
		<p class="help-block"><?php _e("Set screen to which user should be redirected after successful ticket reply.","supportcandy");?></p>
		<select class="form-control" name="wpsc_redirect_to_ticket_list" id="wpsc_redirect_to_ticket_list">
			<?php
			$wpsc_redirect_to_ticket_list = get_option('wpsc_redirect_to_ticket_list');
			$selected = $wpsc_redirect_to_ticket_list == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Ticket List','supportcandy').'</option>';
			$selected = $wpsc_redirect_to_ticket_list == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Ticket Itself','supportcandy').'</option>';
			?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="wpsc_reg_guest_user_after_create_ticket"><?php _e('Register user on creating ticket','supportcandy');?></label>
		<p class="help-block"><?php _e("Register user upon create ticket if not exists (Guest User) ","supportcandy");?></p>
		<select class="form-control" name="wpsc_reg_guest_user_after_create_ticket" id="wpsc_reg_guest_user_after_create_ticket">
			<?php
			$wpsc_reg_guest_user_after_create_ticket = get_option('wpsc_reg_guest_user_after_create_ticket');
			$selected = $wpsc_reg_guest_user_after_create_ticket == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_reg_guest_user_after_create_ticket == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select>
  </div>
	
	<div class="form-group">
		<label for="wpsc_auto_delete"><?php _e('Auto delete tickets','supportcandy');?></label>
		<p class="help-block"><?php _e("If enabled, ticket will get deleted after given time interval.","supportcandy");?></p>
		<select class="form-control" name="wpsc_auto_delete_ticket" id="wpsc_auto_delete_ticket">
			<?php
			$wpsc_auto_delete_ticket  = get_option('wpsc_auto_delete_ticket');
			$selected = $wpsc_auto_delete_ticket == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_auto_delete_ticket == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select>
		
		<div id="auto_delete_time" style="<?php echo $wpsc_auto_delete_ticket=='1'?'display:block;':'display:none;' ?>">
			<label for="wpsc_auto_delete_ticket_time" style="margin-top:10px"><?php _e('Age','supportcandy');?></label>
				<p class="help-block"><?php _e("Set time interval.","supportcandy");?></p>
			 	<div class="row">
			 		<div class="col-sm-6" style="padding-left:0px !important;">
						<?php $wpsc_auto_delete_ticket_time = get_option('wpsc_auto_delete_ticket_time');?>
		 			<input type="number"  min="1" class="form-control" id="wpsc_auto_delete_ticket_time" name="wpsc_auto_delete_ticket_time" value="<?php echo $wpsc_auto_delete_ticket_time ?>">	
			 		</div>
					
					<div class="col-sm-6" style="padding-left:0px !important;">
						<select class="form-control" name="wpsc_auto_delete_ticket_time_period_unit">
	 					 <?php
	 					 $wpsc_auto_delete_ticket_time_period_unit = get_option('wpsc_auto_delete_ticket_time_period_unit');
	 					 $selected = $wpsc_auto_delete_ticket_time_period_unit == 'days' ? 'selected="selected"' : '';
	 					 echo '<option '.$selected.' value="days">Days</option>';
	 					 $selected = $wpsc_auto_delete_ticket_time_period_unit == 'months' ? 'selected="selected"' : '';
	 					 echo '<option '.$selected.' value="months">Months</option>';
	 					 $selected = $wpsc_auto_delete_ticket_time_period_unit == 'years' ? 'selected="selected"' : '';
	 					 echo '<option '.$selected.' value="years">Years</option>';
	 					 ?>
	 				 </select>	
					</div>
			 	</div>
		</div>
	</div>

	    <div class="form-group">
		<label for="wpsc_reply_bcc"><?php _e('BCC','supportcandy');?></label>
		<p class="help-block"><?php _e("Enable/Disable Bcc field in reply ticket form.","supportcandy");?></p>
		<select class="form-control" name="wpsc_reply_bcc" id="wpsc_reply_bcc">
			<?php
			$wpsc_reply_bcc = get_option('wpsc_reply_bcc_visibility');
			$selected = $wpsc_reply_bcc == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_reply_bcc== '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select>
	</div>

	<div class = "form-group">
		<label for="wpsc_allow_datepickerjs_files"><?php _e('Exclude Scripts','supportcandy');?></label>
		<p class="help-block"><?php _e("It will get excluded from frontend.","supportcandy");?></p>
		<div class = "row">
			<?php 
				$wpsc_allow_datepickerjs_files = get_option('wpsc_allow_datepickerjs_files');
				$checked = $wpsc_allow_datepickerjs_files == '1' ? 'checked="checked"' : '';
			?>
			<div class="col-sm-12" style="margin-bottom:10px; display:flex;padding-left:0;">
				<div style="width:25px;"><input <?php  echo $checked ?> type="checkbox" name="wpsc_allow_datepickerjs_files" value="1" /></div>
				<div style="padding-top:3px;"><?php _e('DateTimePicker','supportcandy') ?></div>
			</div>
		</div>
	</div>


	<button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
  <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
  <input type="hidden" name="action" value="wpsc_settings" />
  <input type="hidden" name="setting_action" value="set_advanced_settings" />
</form>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#wpsc_thread_date_format').change(function() {
 		 if(this.value=='timestamp'){			 
 			  jQuery('#date_time_format').show();
 		 }else {
 		 		jQuery('#date_time_format').hide(); 
 		 }
 	 });
	 
	 jQuery('#wpsc_auto_delete_ticket').change(function() {
		if(this.value=='1'){			 
			 jQuery('#auto_delete_time').show();
		}else {
			 jQuery('#auto_delete_time').hide(); 
		}
	});
	jQuery('#wpsc_notify').change(function() {
		if(this.value=='1'){			 
			 jQuery('#wpsc_do_not_notify_option').show();
		}else {
			 jQuery('#wpsc_do_not_notify_option').hide(); 
		}
	});
 });
</script>

<script>
 jQuery(document).ready(function() {	   
   jQuery('#wpsc_ticket_public_mode').change(function() {
		 if(this.value=='1'){			 
			  jQuery('#wpsc_allow_reply_to_public_tickets').show();
		 }else {
		 		jQuery('#wpsc_allow_reply_to_public_tickets').hide(); 
		 }
	 });
	});
</script>