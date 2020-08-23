<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction;
?>



<li id="wppatt_settings_box" role="presentation"><a href="javascript:wppatt_get_box_settings();">Box Statuses</a></li>

<script>
  function wppatt_get_box_settings(){
    jQuery('.wpsc_setting_pills li').removeClass('active');
    jQuery('#wppatt_settings_box').addClass('active');
    jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
    var data = {
      action: 'wppatt_get_box_settings',
    };
    jQuery.post(wpsc_admin.ajax_url, data, function(response) {
      jQuery('.wpsc_setting_col2').html(response);
    });
  }
</script>
