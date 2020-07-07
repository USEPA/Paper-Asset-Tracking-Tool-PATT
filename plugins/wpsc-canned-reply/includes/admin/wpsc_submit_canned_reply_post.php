<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user, $wpscfunction;

if( !$current_user->ID || !$current_user->has_cap('wpsc_agent') ) return;

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');
$canned_reply_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_other_reply_form_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_other_reply_form_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_other_reply_form_btn_border_color'].'!important';

?>

<?php
$agent_permissions = $wpscfunction->get_current_agent_permissions();
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
?>
<button type="button" onclick="javascript:wpsc_submit_canned_reply_post();" class="btn" style="<?php echo $canned_reply_btn_css?>">
	<i class="fa fa-save"></i> <?php _e('Add Canned Reply','wpsc-canned-reply')?> 
</button>
<script>
function wpsc_submit_canned_reply_post(){  
  wpsc_modal_open('Create New Canned Reply');
	   var data = {
	     action: 'submit_canned_reply_post',	     
	   };
	   jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {  
	     var response = JSON.parse(response_str);
	     jQuery('#wpsc_popup_body').html(response.body);
	     jQuery('#wpsc_popup_footer').html(response.footer);        
	   }); 
}
</script>
<?php
}
?>