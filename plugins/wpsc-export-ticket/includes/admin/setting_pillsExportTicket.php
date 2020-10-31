<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user;
?>
<li id="wpsc_settings_export_ticket" role="presentation"><a href="javascript:wpsc_get_export_ticket_settings();"><?php _e('Export Ticket','wpsc-export-ticket');?></a></li>
<script>
function wpsc_get_export_ticket_settings(){   
  jQuery('.wpsc_setting_pills li').removeClass('active');
  jQuery('#wpsc_settings_export_ticket').addClass('active');
  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
  
  var data = {
    action: 'wpsc_get_export_ticket_settings',    
  };

  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {     
    jQuery('.wpsc_setting_col2').html(response_str);
  });
  
}
</script>