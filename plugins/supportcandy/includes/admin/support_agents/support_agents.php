<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

?>

<div class="bootstrap-iso">
  
  <h3>
    <?php _e('Support Agents','supportcandy');?>
    <a href="https://supportcandy.net/support/" class="btn btn-info wpsc-help-site-link" style="float:right;margin-right:1% !important;margin-top:-9px !important;"><?php _e('Need Help? Click Here!','supportcandy');?></a>
  </h3>
  
  <div class="wpsc_padding_space"></div>
  <div class="row" style="margin-bottom:20px;">
    <ul class="nav nav-pills wpsc_setting_pills">
      <li id="wpsc_support_agents" role="presentation" class="active"><a href="javascript:wpsc_get_support_agents();"><?php _e('Support Agents','supportcandy');?></a></li>
      <li id="wpsc_settings_agent_roles" role="presentation"><a href="javascript:wpsc_get_agent_roles();"><?php _e('Agent Roles','supportcandy');?></a></li>
      <?php do_action('wpsc_after_support_agent_pills');?>
    </ul>
  </div>
  <div style="width:95% !important;" class="row wpsc_setting_col2"></div>
  
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
      wpsc_get_support_agents();
    });
  </script>
  <?php
}
?>