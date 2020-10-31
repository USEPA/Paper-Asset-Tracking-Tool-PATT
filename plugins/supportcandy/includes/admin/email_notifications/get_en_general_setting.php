<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}
?>

<form id="wpsc_frm_general_settings" method="post" action="javascript:wpsc_set_en_general_settings();">

  <div class="form-group">
    <label for="wpsc_en_from_name"><?php _e('From Name','supportcandy');?></label>
    <p class="help-block"><?php _e('Emails to send by this name.','supportcandy');?></p>
    <input type="text" class="form-control" name="wpsc_en_from_name" id="wpsc_en_from_name" value="<?php echo get_option('wpsc_en_from_name','');?>" />
  </div>

  <div class="form-group">
    <label for="wpsc_en_from_email"><?php _e('From Email','supportcandy');?></label>
    <p class="help-block"><?php _e('Emails to send from this email.','supportcandy');?></p>
    <input type="text" class="form-control" name="wpsc_en_from_email" id="wpsc_en_from_email" value="<?php echo get_option('wpsc_en_from_email','');?>" />
  </div>

  <div class="form-group">
    <label for="wpsc_en_reply_to"><?php _e('Reply to','supportcandy');?></label>
    <p class="help-block"><?php _e('(Optional) When recipients reply to the notification from their inbox, reply will be sent to this email address.','supportcandy');?></p>
    <input type="text" class="form-control" name="wpsc_en_reply_to" id="wpsc_en_reply_to" value="<?php echo get_option('wpsc_en_reply_to','');?>" />
  </div>

  <div class="form-group">
    <label for="wpsc_en_ignore_emails"><?php _e('Ignore Emails','supportcandy');?></label>
    <p class="help-block"><?php _e('Emails will not be sent to these email addresses. New email should begin on new line.','supportcandy');?></p>
    <?php
    $ignore_emails = get_option('wpsc_en_ignore_emails',array());
    ?>
    <textarea class="form-control" style="height:100px !important;" name="wpsc_en_ignore_emails" id="wpsc_en_ignore_emails"><?php echo stripcslashes(implode('\n', $ignore_emails))?></textarea>
  </div>

  <div class="form-group">
    <label for="wpsc_en_send_mail_count"><?php _e('Email frequency','supportcandy');?></label>
    <p class="help-block"><?php _e('Number of emails sent for each cron job call.','supportcandy');?></p>
    <input type="number"  min="1"  class="form-control" name="wpsc_en_send_mail_count" id="wpsc_en_send_mail_count" value="<?php echo get_option('wpsc_en_send_mail_count');?>" />
  </div>

  <div class="form-group">
    <label for="wpsc_email_sending_method"><?php _e('Email sending preference','supportcandy');?></label>
    <p class="help-block"><?php _e("Select how an email notification will be sent.","supportcandy");?></p>
    <select class="form-control" name="wpsc_email_sending_method" id="wpsc_email_sending_method">
      <?php
      $wpsc_email_sending_method = get_option('wpsc_email_sending_method');
      $selected = $wpsc_email_sending_method == '1' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="1">'.__('Background (Increase screen load time)','supportcandy').'</option>';
      $selected = $wpsc_email_sending_method == '0' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="0">'.__('Instant','supportcandy').'</option>';
      ?>
    </select>
  </div>

  <?php do_action('wpsc_get_gerneral_settings');?>

  <button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
  <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
  <input type="hidden" name="action" value="wpsc_email_notifications" />
  <input type="hidden" name="setting_action" value="set_en_general_settings" />

</form>
