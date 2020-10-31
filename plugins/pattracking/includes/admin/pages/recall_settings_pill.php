<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction;
?>



<li id="wppatt_settings_recall" role="presentation"><a href="javascript:wppatt_get_recall_settings();">Recall Statuses</a></li>

<script>
  function wppatt_get_recall_settings(){
    jQuery('.wpsc_setting_pills li').removeClass('active');
    jQuery('#wppatt_settings_recall').addClass('active');
    jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
    var data = {
      action: 'wppatt_get_recall_settings',
    };
    jQuery.post(wpsc_admin.ajax_url, data, function(response) {
      jQuery('.wpsc_setting_col2').html(response);
    });
  }
</script>
