<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<li id="wpsc_report_settings" role="presentation"><a href="javascript:wpsc_get_reports_settings();"><?php _e('Reports','wpsc-rp');?></a></li>

<script>
  function wpsc_get_reports_settings(){
    jQuery('.wpsc_setting_pills li').removeClass('active');
    jQuery('#wpsc_report_settings').addClass('active');
    jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
    var data = {
      action: 'wpsc_get_reports_settings',
    };
    jQuery.post(wpsc_admin.ajax_url, data, function(response) {
      jQuery('.wpsc_setting_col2').html(response);
    });
  }
</script>