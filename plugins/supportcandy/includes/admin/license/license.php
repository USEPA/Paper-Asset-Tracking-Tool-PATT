<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$is_addons = apply_filters( 'wpsc_is_add_on_installed', false );
if($is_addons) {
  $license_messege = __('Enter your add-ons license keys here to receive updates for purchased add-ons. If your license key has expired, please renew your license.','supportcandy');
} else {
  $license_messege = '<h4>'.sprintf(__('No add-ons installed. See available add-ons - %1$s.','supportcandy'),'<a href="https://supportcandy.net/add-ons/" target="_blank">https://supportcandy.net/add-ons/</a>').'</h4>';
}


?>

<div class="bootstrap-iso">
  
  <h3>
    <?php _e('License','supportcandy');?>
    <a href="https://supportcandy.net/support/" class="btn btn-info wpsc-help-site-link" style="float:right;margin-right:1% !important;margin-top:-9px !important;"><?php _e('Need Help? Click Here!','supportcandy');?></a>
  </h3>
  
  <div class="wpsc_padding_space"></div>
  <div class="row" style="margin-bottom:20px;"><?php echo html_entity_decode($license_messege)?></div>
  <div class="row"><?php do_action('wpsc_addon_license_area')?></div>
  
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