<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

?>

<div class="bootstrap-iso">
  
  <h3>
    <?php _e('Appearance Settings','supportcandy');?>
    <a href="https://supportcandy.net/support/" class="btn btn-info wpsc-help-site-link" style="float:right;margin-right:1% !important;margin-top:-9px !important;"><?php _e('Need Help? Click Here!','supportcandy');?></a>
  </h3>
  
  <div class="wpsc_padding_space"></div>
  
  <div class="row">
    <div class="col-sm-4 wpsc_setting_col1">
      <ul class="nav nav-pills nav-stacked wpsc_setting_pills">
        <li id="wpsc_appearance_general" role="presentation" class="active"><a href="javascript:wpsc_get_appearance_general_settings();"><?php _e('General','supportcandy');?></a></li>
        <li id="wpsc_appearance_ticket_list" role="presentation"><a href="javascript:wpsc_get_appearance_ticket_list();"><?php _e('Ticket List','supportcandy');?></a></li>
        <li id="wpsc_appearance_individual_ticket" role="presentation"><a href="javascript:wpsc_get_appearance_individual_ticket();"><?php _e(' Individual Ticket Page','supportcandy');?></a></li>
        <li id="wpsc_appearance_create_ticket" role="presentation"><a href="javascript:wpsc_get_appearance_create_ticket();"><?php _e('Create Ticket Page','supportcandy');?></a></li>
        <li id="wpsc_appearance_modal_window" role="presentation"><a href="javascript:wpsc_get_appearance_madal_window();"><?php _e('Modal Window (Popup Screen)','supportcandy');?></a></li>
        <li id="wpsc_appearance_login_form" role="presentation"><a href="javascript:wpsc_get_appearance_login_form();"><?php _e('Login Form','supportcandy');?></a></li>
        <li id="wpsc_appearance_signup_form" role="presentation"><a href="javascript:wpsc_get_appearance_signup();"><?php _e('User Registration','supportcandy');?></a></li>
        <?php do_action('wpsc_after_appearance_setting_pills');?>
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
      wpsc_get_appearance_general_settings();
    });
  </script>
  <?php
}
?>