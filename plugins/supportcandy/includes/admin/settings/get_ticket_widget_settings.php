<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
   exit;
}

$ticket_widgets = get_terms([
	'taxonomy'   => 'wpsc_ticket_widget',
	'hide_empty' => false,
  'orderby'    => 'meta_value_num',
  'order'    	 => 'ASC',
  'meta_query' => array('order_clause' => array('key' => 'wpsc_ticket_widget_load_order')),
]);
$wpsc_custom_widget_localize = get_option('wpsc_custom_widget_localize');
?>

<h4><?php _e('Ticket Widgets','supportcandy');?></h4>

<div class="wpsc_padding_space"></div>

<ul class="wpsc-sortable">
	<?php foreach ( $ticket_widgets as $ticket_widget) {
		$ticket_widget_name = $wpsc_custom_widget_localize['custom_widget_'.$ticket_widget->term_id];
		$wpsc_ticket_widget_type = get_term_meta( $ticket_widget->term_id, 'wpsc_ticket_widget_type', true);
		$bg_color = $wpsc_ticket_widget_type == '1' ? '#1E90FF' : '#ff0000';
    ?>
		<li class="ui-state-default" data-id="<?php echo $ticket_widget->term_id?>">
			<div class="wpsc-flex-container" style="background-color:<?php echo $bg_color?>;color:#fff;">
				<div class="wpsc-sortable-handle"><i class="fa fa-bars"></i></div>
				<div class="wpsc-sortable-label"><?php echo htmlentities($ticket_widget_name)?></div>
					<div class="wpsc-sortable-edit" style="margin-left:30px;" onclick="wpsc_get_edit_ticket_widget(<?php echo $ticket_widget->term_id?>);"><i class="fa fa-edit"></i></div>
			</div>
		</li>
	<?php }?>
</ul>
<script>
	
jQuery(function(){
    jQuery( ".wpsc-sortable" ).sortable({ handle: '.wpsc-sortable-handle' });
		jQuery( ".wpsc-sortable" ).on("sortupdate",function(event,ui){
			var ids = jQuery(this).sortable( "toArray", {attribute: 'data-id'} );
			var data = {
		    action: 'wpsc_settings',
		    setting_action : 'set_ticket_widgets',
				ticket_widget_ids : ids
		  };
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				var response = JSON.parse(response_str);
		    if (response.sucess_status=='1') {
		      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
		    }
		    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
		    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
		  });
		});
});

function wpsc_get_edit_ticket_widget(ticket_widget_id) {
	  jQuery('.wpsc_setting_pills li').removeClass('active');
	  jQuery('#wpsc_settings_ticket_widget').addClass('active');
	  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
	  
	  var data={
	    action: 'wpsc_settings',
	   	setting_action:'get_edit_ticket_widget',
			ticket_widget_id : ticket_widget_id
	  };
	  
	  jQuery.post(wpsc_admin.ajax_url,data,function(response) {
	    jQuery('.wpsc_setting_col2').html(response);
	  });
}


function wpsc_set_edit_ticket_widget(ticket_widget_id,event) {
		event.preventDefault();
		jQuery('.wpsc_submit_wait').show();
		var selectedrole = new Array();
		var ticket_widget_name = jQuery('#wpsc_ticket_widget_name').val();
		var ticket_widget_type = jQuery('#wpsc_ticket_widget_type').val();
		jQuery("input[name='ticket_widget_role']:checked").each(function() {
			selectedrole.push(this.value);
		});
		
	 	var data = {
	 		action: 'wpsc_settings',
	 		setting_action : 'set_edit_ticket_widgets',
	 		ticket_widget_id : ticket_widget_id,
	 		ticket_widget_name : ticket_widget_name,
	 		ticket_widget_type: ticket_widget_type,
	 		ticket_widget_role: selectedrole,
	 	};
	  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
	 		wpsc_modal_close();
	 		wpsc_get_ticket_widget_settings();
	 });
	 
	}	
</script>