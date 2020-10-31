<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction;
?>



<li id="wppatt_settings_return" role="presentation"><a href="javascript:wppatt_get_return_settings();">Decline Settings</a></li>

<script>
  function wppatt_get_return_settings(){
    jQuery('.wpsc_setting_pills li').removeClass('active');
    jQuery('#wppatt_settings_return').addClass('active');
    jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
    var data = {
      action: 'wppatt_get_return_settings',
    };
    jQuery.post(wpsc_admin.ajax_url, data, function(response) {
      jQuery('.wpsc_setting_col2').html(response);
    });
  }
</script>
