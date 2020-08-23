<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}

//
// Register taxonomy if it hasn't been registered yet
//

// Register Box Status Taxonomy
if( !taxonomy_exists('wpsc_box_statuses') ) {

	$args = array(
		'public' => false,
		'rewrite' => false
	);
	register_taxonomy( 'wpsc_box_statuses', 'wpsc_ticket', $args );

}

$term = wp_insert_term( 'Pending', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 0);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}

/*
$term = wp_insert_term( 'Assigned', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 1);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}
*/

$term = wp_insert_term( 'Scanning Preparation', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 2);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}

$term = wp_insert_term( 'Scanning/Digitization', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 3);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}

$term = wp_insert_term( 'QA/QC', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 4);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}

$term = wp_insert_term( 'Digitized - Not Validated', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 5);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}

$term = wp_insert_term( 'Ingestion', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 6);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}

$term = wp_insert_term( 'Validation', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 7);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}

$term = wp_insert_term( 'Re-Scan', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 8);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}

$term = wp_insert_term( 'Completed', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 9);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}

$term = wp_insert_term( 'Destruction Approval', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 10);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}

$term = wp_insert_term( 'Dispositioned', 'wpsc_box_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wpsc_box_status_load_order', 11);
  add_term_meta ($term['term_id'], 'wpsc_box_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wpsc_box_status_background_color', '#dd9933');
}


//
// END register taxonomy 
//


$statuses = get_terms([
	'taxonomy'   => 'wpsc_box_statuses',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wpsc_box_status_load_order')),
]);


?>
<h4>
<!-- 	<?php _e('Ticket Statuses','supportcandy');?> -->
	Box Statuses
<!-- 	<button style="margin-left:10px;" class="btn btn-success btn-sm" onclick="wpsc_get_add_status();"><?php _e('+Add New','supportcandy');?></button> -->
</h4>

<div class="wpsc_padding_space"></div>

<ul class="">
	<?php foreach ( $statuses as $status ) :
    $color = get_term_meta( $status->term_id, 'wpsc_box_status_color', true);
    $backgound_color = get_term_meta( $status->term_id, 'wpsc_box_status_background_color', true);
    ?>
		<li class="ui-state-default" data-id="<?php echo $status->term_id?>">
			<div class="wpsc-flex-container" style="background-color:<?php echo $backgound_color?>;color:<?php echo $color?>;">
				<div class=""><i class="fa fa-bars"></i></div>
				<div class="wpsc-sortable-label"><?php echo $status->name?></div>
				<div class="wpsc-sortable-edit" onclick="wpsc_get_edit_status(<?php echo $status->term_id?>);"><i class="fa fa-edit"></i></div>
<!-- 				<div class="wpsc-sortable-delete" onclick="wpsc_delete_status(<?php echo $status->term_id?>);"><i class="fa fa-trash"></i></div> -->
			</div>
		</li>
	<?php endforeach;?>
</ul>



<style>
	
</style>

<script>
	
	jQuery(function(){
    jQuery( ".wpsc-sortable" ).sortable({ handle: '.wpsc-sortable-handle' });
		jQuery( ".wpsc-sortable" ).on("sortupdate",function(event,ui){
			var ids = jQuery(this).sortable( "toArray", {attribute: 'data-id'} );
			var data = {
		    action: 'wpsc_settings',
		    setting_action : 'set_status_order',
				status_ids : ids
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
	
/*
	function wpsc_get_add_status(){
		wpsc_modal_open(wpsc_admin.add_new_status);
		var data = {
			action: 'wpsc_settings',
			setting_action : 'get_add_status'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_cat_name').focus();
		});
	}
*/
	
/*
	function wpsc_set_add_status(){
		var status_name = jQuery('#wpsc_status_name').val().trim();
		if (status_name.length == 0) {
			jQuery('#wpsc_status_name').val('').focus();
			return;
		}
		var status_color = jQuery('#wpsc_status_color').val().trim();
		if (status_color.length == 0) {
			status_color = '#ffffff';
		}
		var status_bg_color = jQuery('#wpsc_status_bg_color').val().trim();
		if (status_bg_color.length == 0) {
			status_bg_color = '#1E90FF';
		}
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_settings',
			setting_action : 'set_add_status',
			status_name : status_name,
			status_color: status_color,
			status_bg_color: status_bg_color
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_status_settings();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
*/
	
	function wpsc_get_edit_status(status_id){
// 		wpsc_modal_open(wpsc_admin.edit_status);
		wpsc_modal_open('Edit Box Status');
		console.log(status_id);
		var data = {
			action: 'wppatt_get_edit_box_status',
			status_id : status_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_status_name').focus();
		});
	}
	
	function wpsc_set_edit_status(status_id){
		//var status_name = jQuery('#wpsc_status_name').val().trim();
/*
		if (status_name.length == 0) {
			jQuery('#wpsc_status_name').val('').focus();
			return;
		}
*/
		var status_color = jQuery('#wpsc_status_color').val().trim();
		if (status_color.length == 0) {
			status_color = '#ffffff';
		}
		var status_bg_color = jQuery('#wpsc_status_bg_color').val().trim();
		if (status_bg_color.length == 0) {
			status_bg_color = '#1E90FF';
		}
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		console.log('status id: '+status_id);
		console.log('status color: '+status_color);
		console.log('status background: '+status_bg_color);
		var data = {
			action: 'wppatt_set_box_status',
// 			setting_action : 'set_edit_status',
			status_id : status_id,
			//status_name : status_name,
			status_color: status_color,
			status_bg_color: status_bg_color
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				wppatt_get_box_status_settings();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
	
	/*
	 * Status Settings
	 */
	function wppatt_get_box_status_settings(){
	  
	  jQuery('.wpsc_setting_pills li').removeClass('active');
	  jQuery('#wppatt_settings_box').addClass('active');
	  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
	  
	  var data = {
	    action: 'wppatt_get_box_settings'
// 	    setting_action : 'get_status_settings'
	  };
	
	  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
	    jQuery('.wpsc_setting_col2').html(response);
	  });
	  
	}
	
/*
	function wpsc_delete_reason(status_id){
		var flag = confirm(wpsc_admin.are_you_sure);
		if (flag) {
			var data = {
				action: 'wpsc_settings',
// 				setting_action : 'delete_status',
				setting_action : 'delete_reason',				
				status_id : status_id
			};
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				var response = JSON.parse(response_str);
				if (response.sucess_status=='1') {
					jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_success').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
					wpsc_get_status_settings();
				} else {
					jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_error').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
				}
			});
		}
	}
*/
	
</script>
