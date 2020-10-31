<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb, $current_user, $wpscfunction;
$agent_permissions = $wpscfunction->get_current_agent_permissions();

if( !$current_user->ID || !$current_user->has_cap('wpsc_agent') ) return;

?>
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
?>
<span onclick="wpsc_get_canned_reply()" >Canned Reply</span>
<?php } ?>
<script>
function wpsc_get_canned_reply(){  
  wpsc_modal_open('Canned Reply');
  var data = {
    action: 'get_canned_reply'  
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    jQuery('#wpsc_popup_body').html(response.body);
    jQuery('#wpsc_popup_footer').html(response.footer);
    jQuery('#wpsc_cat_name').focus();
  });  
}
</script>
