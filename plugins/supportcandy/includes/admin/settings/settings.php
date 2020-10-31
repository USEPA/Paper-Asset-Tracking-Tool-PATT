<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

?>

<div class="bootstrap-iso">
  
  <h3>
    <?php _e('Settings','supportcandy');?>
    <a href="https://supportcandy.net/support/" class="btn btn-info wpsc-help-site-link" style="float:right;margin-right:1% !important;margin-top:-9px !important;"><?php _e('Need Help? Click Here!','supportcandy');?></a>
  </h3>
  
  <div class="wpsc_padding_space"></div>
  <div class="row">
    <div class="col-sm-4 wpsc_setting_col1">
      <ul class="nav nav-pills nav-stacked wpsc_setting_pills">
        <li id="wpsc_settings_general" role="presentation" class="active"><a href="javascript:wpsc_get_general_settings();"><?php _e('General','supportcandy');?></a></li>
        <li id="wpsc_settings_category" role="presentation"><a href="javascript:wpsc_get_category_settings();"><?php _e('Ticket Categories','supportcandy');?></a></li>
        <li id="wpsc_settings_status" role="presentation"><a href="javascript:wpsc_get_status_settings();"><?php _e('Ticket Statuses','supportcandy');?></a></li>
        <li id="wpsc_settings_priority" role="presentation"><a href="javascript:wpsc_get_priority_settings();"><?php _e('Ticket Priorities','supportcandy');?></a></li>
        <li id="wpsc_settings_ticket_widget" role="presentation"><a href="javascript:wpsc_get_ticket_widget_settings();"><?php _e('Open Ticket Widget','supportcandy');?></a></li>
        <li id="wpsc_settings_thank_you" role="presentation"><a href="javascript:wpsc_get_thank_you_settings();"><?php _e('Thank you page','supportcandy');?></a></li>
        <li id="wpsc_settings_cron_setup" role="presentation"><a href="javascript:wpsc_get_cron_setup_settings();"><?php _e('Cron Setting','supportcandy');?></a></li>
        <li id="wpsc_settings_attachment" role="presentation"><a href="javascript:wpsc_get_attachment_settings();"><?php _e('Attachments','supportcandy');?></a></li>
        <li id="wpsc_settings_term_and_conditions" role="presentation"><a href="javascript:wpsc_get_terms_and_condition_settings();"><?php _e('Terms & Conditions & GDPR','supportcandy');?></a></li>
        <li id="wpsc_advanced_settings" role="presentation"><a href="javascript:wpsc_get_advanced_settings();"><?php _e('Advanced Settings','supportcandy');?></a></li>
        <li id="wpsc_captcha_settings" role="presentation"><a href="javascript:wpsc_get_captcha_settings();"><?php _e('Captcha','supportcandy');?></a></li>
        <li id="wpsc_rest_settings" role="presentation"><a href="javascript:wpsc_get_rest_api_settings();"><?php _e('REST API','supportcandy');?></a></li>
        <li id="wpsc_tinymce_settings" role="presentation"><a href="javascript:wpsc_get_tinymce_settings();"><?php _e('TinyMCE','supportcandy');?></a></li>
        
        <?php do_action('wpsc_after_setting_pills');?>
      </ul>
    </div>
    <div class="col-sm-8 wpsc_setting_col2"></div>
  </div>
  
  <div id="wpsc_alert_success" class="alert alert-success wpsc_alert" style="display:none;" role="alert">
    <i class="fa fa-check-circle"></i> <span class="wpsc_alert_text"></span>
  </div>
  
  <div id="wpsc_alert_error" class="alert alert-danger wpsc_alert" style="display:none;" role="alert">
    <i class="fa fa-exclamation-triangle"></i> <span class="wpsc_alert_text"></span>
  </div>
  
</div>

<!-- Pop-up snippet start -->
<div id="wpsc_popup_background" style="display:none;"></div>
<div id="wpsc_popup_container" style="display:none;">
  <div class="bootstrap-iso">
    <div class="row">
      <div id="wpsc_popup" class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
        <div id="wpsc_popup_title" class="row"><h3>Modal Title</h3></div>
        <div id="wpsc_popup_body" class="row">I am body!</div>
        <div id="wpsc_popup_footer" class="row">
          <button type="button" class="btn wpsc_popup_close"><?php _e('Close','supportcandy');?></button>
          <button type="button" class="btn wpsc_popup_action"><?php _e('Save Changes','supportcandy');?></button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Pop-up snippet end -->

<?php
add_action('admin_footer', 'wpsc_page_inline_script');
function wpsc_page_inline_script(){
  ?>
  <script>
    jQuery(document).ready(function(){
      wpsc_get_general_settings();
    });
  
  </script>
  <?php
}
?>