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


// Register Return (Decline) Status Taxonomy
if( !taxonomy_exists('wppatt_return_statuses') ) {

	$args = array(
		'public' => false,
		'rewrite' => false
	);
	register_taxonomy( 'wppatt_return_statuses', 'wpsc_ticket', $args );

}

// $term = wp_insert_term( 'Return Initiated', 'wppatt_return_statuses' );
$term = wp_insert_term( 'Decline Initiated', 'wppatt_return_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wppatt_return_status_load_order', 0);
  add_term_meta ($term['term_id'], 'wppatt_return_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wppatt_return_status_background_color', '#dd9933');
}

// $term = wp_insert_term( 'Return Shipped', 'wppatt_return_statuses' );
$term = wp_insert_term( 'Decline Shipped', 'wppatt_return_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  //$load_order = $wpdb->get_var("select max(meta_value) as load_order from {$wpdb->prefix}termmeta WHERE meta_key='wppatt_recall_status_load_order'");
  add_term_meta ($term['term_id'], 'wppatt_return_status_load_order', 1);
  add_term_meta ($term['term_id'], 'wppatt_return_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wppatt_return_status_background_color', '#30d1c9');
}

// $term = wp_insert_term( 'Return Complete', 'wppatt_return_statuses' );
$term = wp_insert_term( 'Decline Complete', 'wppatt_return_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  //$load_order = $wpdb->get_var("select max(meta_value) as load_order from {$wpdb->prefix}termmeta WHERE meta_key='wppatt_recall_status_load_order'");
  add_term_meta ($term['term_id'], 'wppatt_return_status_load_order', 2);
  add_term_meta ($term['term_id'], 'wppatt_return_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wppatt_return_status_background_color', '#81d742');
}

// $term = wp_insert_term( 'Return Cancelled', 'wppatt_return_statuses' );
$term = wp_insert_term( 'Decline Cancelled', 'wppatt_return_statuses' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  //$load_order = $wpdb->get_var("select max(meta_value) as load_order from {$wpdb->prefix}termmeta WHERE meta_key='wppatt_recall_status_load_order'");
  add_term_meta ($term['term_id'], 'wppatt_return_status_load_order', 3);
  add_term_meta ($term['term_id'], 'wppatt_return_status_color', '#ffffff');
  add_term_meta ($term['term_id'], 'wppatt_return_status_background_color', '#000000');
}


// Register Return Reason Taxonomy
if( !taxonomy_exists('wppatt_return_reason') ) {

	$args = array(
		'public' => false,
		'rewrite' => false
	);
	register_taxonomy( 'wppatt_return_reason', 'wpsc_ticket', $args );

}

/*
$term = wp_insert_term( 'Damaged', 'wppatt_return_reason' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wppatt_return_reason_load_order', 0);
  add_term_meta ($term['term_id'], 'wppatt_return_reason_description', 'Record(s) are damaged to a state where they cannot be repaired and scanned by the NDC.');
}

$term = wp_insert_term( 'Non-record', 'wppatt_return_reason' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wppatt_return_reason_load_order', 1);
  add_term_meta ($term['term_id'], 'wppatt_return_reason_description', 'Record(s) do not meet do not meet the criteria for digitization.');
}

$term = wp_insert_term( 'Duplicate', 'wppatt_return_reason' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wppatt_return_reason_load_order', 2);
  add_term_meta ($term['term_id'], 'wppatt_return_reason_description', 'Record(s) are duplicates of submitted or scanned records.');
}

$term = wp_insert_term( 'Unscannable', 'wppatt_return_reason' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wppatt_return_reason_load_order', 3);
  add_term_meta ($term['term_id'], 'wppatt_return_reason_description', 'I.e. an object is in the records box as a record - it could be photographed but the NDC will not have photographic equipment, or a format (i.e. punched tape, betamax, .vsd file format, etc.) is submitted that the NDC does not have equipment to process/digitize.');
}

$term = wp_insert_term( 'Copyright Material', 'wppatt_return_reason' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wppatt_return_reason_load_order', 4);
  add_term_meta ($term['term_id'], 'wppatt_return_reason_description', 'Record(s) are not government records and/or are copyright material. NDP should not accept copyrighted materials for scanning. “In general, all government records are in the public domain” https://www.archives.gov/faqs#copyright. OMB’s, NARA’s & EPA’s digitization goals do not grant the government authority to copy, store, share or reproduce copyrighted material. Ask OGC. NDP will save a great deal of confusion & useless effort by refusing to accept copyrighted material.');
}

$term = wp_insert_term( 'Request Cancelled before Arrival', 'wppatt_return_reason' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wppatt_return_reason_load_order', 5);
  add_term_meta ($term['term_id'], 'wppatt_return_reason_description', 'Sending location cancelled the digitization request after shipping but before the record(s) arrived/were in-processed at the NDC.');
}

$term = wp_insert_term( 'Contents Not Prepared to Standards', 'wppatt_return_reason' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wppatt_return_reason_load_order', 6);
  add_term_meta ($term['term_id'], 'wppatt_return_reason_description', 'Record(s) do not meet the preparation criteria by the sending location for digitization.');
}

$term = wp_insert_term( 'Box Listing Incomplete/Missing', 'wppatt_return_reason' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  add_term_meta ($term['term_id'], 'wppatt_return_reason_load_order', 7);
  add_term_meta ($term['term_id'], 'wppatt_return_reason_description', 'Box Listing is missing or missing information about records including in the transfer.');
}
*/

//
// END register taxonomy 
//


$statuses = get_terms([
	'taxonomy'   => 'wppatt_return_statuses',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wppatt_return_status_load_order')),
]);

$reasons = get_terms([
	'taxonomy'   => 'wppatt_return_reason',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wppatt_return_reason_load_order')),
]);

?>
<h4>
<!-- 	<?php _e('Ticket Statuses','supportcandy');?> -->
	Decline Settings
<!-- 	<button style="margin-left:10px;" class="btn btn-success btn-sm" onclick="wpsc_get_add_status();"><?php _e('+Add New','supportcandy');?></button> -->
</h4>

<hr>
<h4>Decline Statuses</h4>

<div class="wpsc_padding_space"></div>

<ul class="">
	<?php foreach ( $statuses as $status ) :
    $color = get_term_meta( $status->term_id, 'wppatt_return_status_color', true);
    $backgound_color = get_term_meta( $status->term_id, 'wppatt_return_status_background_color', true);
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

<hr>

<h4>Decline Reasons
	<button style="margin-left:10px;" class="btn btn-success btn-sm" onclick="wpsc_get_add_reason();"><?php _e('+Add New','supportcandy');?></button>
</h4>
<div class="wpsc_padding_space"></div>

<ul class="">
	<?php foreach ( $reasons as $reason ) :
    $description = get_term_meta( $reason->term_id, 'wppatt_return_reason_description', true);
    ?>
		<li class="ui-state-default" data-id="<?php echo $reason->term_id?>">
			<div class="wpsc-flex-container" style="">
				<div class=""><i class="fa fa-bars"></i></div>
				<div class=""><?php echo $reason->name?></div>
				<div class="wpsc-sortable-edit" onclick="wpsc_get_edit_reason(<?php echo $reason->term_id?>);"><i class="fa fa-edit"></i></div>
<!-- 				<div class="wpsc-sortable-delete" onclick="wpsc_delete_reason(<?php echo $reason->term_id?>);"><i class="fa fa-trash"></i></div> -->
			</div>
			<div class="">
				<div class="col-xl-8">
					<textarea readonly class="return-description" value="<?php echo $description; ?>" ><?php echo $description; ?> </textarea>
<!-- 					<div class="return-description-field" ><?php echo $description; ?></div> -->
				</div>
			</div>
		</li>
	<?php endforeach;?>
</ul>

<style>
.return-description {
	width: 100%;
/* 	height: 7em; */
	height: auto;
}

.return-description-field {
	padding-left: 7px;
	background-color: #eaeaea;
	border-radius: 3px;
}
	
</style>

<script>
	
	jQuery(document).ready(function(){
		
		jQuery('textarea').each(function () {
			this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
		}).on('input', function () {
			this.style.height = 'auto';
			this.style.height = (this.scrollHeight) + 'px';
		});
		
	});
	
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
	
	
	function wpsc_get_edit_status(status_id){
// 		wpsc_modal_open(wpsc_admin.edit_status);
// 		wpsc_modal_open('Edit Return Status');
		wpsc_modal_open('Edit Decline Status');
		console.log(status_id);
		var data = {
			action: 'wppatt_get_edit_return_status',
			status_id : status_id,
			type: 'edit-return-status-color'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_status_name').focus();
		});
	}
	
	
	function wpsc_get_add_reason(){
// 		wpsc_modal_open('Add New Return Reason');
		wpsc_modal_open('Add New Decline Reason');
		console.log('Add Reason');
		var data = {
			action: 'wppatt_get_edit_return_status',
			type: 'add-return-reason'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_status_name').focus();
		});
	}
	
	function wpsc_get_edit_reason(term_id){
// 		wpsc_modal_open('Edit Return Reason');
		wpsc_modal_open('Edit Decline Reason');
		console.log('Edit Reason '+ term_id);
		var data = {
			action: 'wppatt_get_edit_return_status',
			type: 'edit-return-reason',
			return_term_id: term_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_status_name').focus();
		});
	}
	
	function wpsc_delete_edit_reason(term_id){
// 		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
// 		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		
		let confirmed = confirm("Please confirm that you want to delete this Decline Reason. This cannot be undone.");
		
		if (confirmed) {
			var data = {
				action: 'wppatt_set_return_status',
				type: 'delete-return-reason',
				return_term_id: term_id
			};
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				wpsc_modal_close();
				var response = JSON.parse(response_str);
				if (response.sucess_status=='1') {
					wppatt_get_return_status_settings();
				} else {
					jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_error').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
				}
			});
		}
		
		
	}
	
	
	function wpsc_set_edit_status(status_id){

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
			action: 'wppatt_set_return_status',
			status_id : status_id,
			type : 'set-return-status-color',
			status_color: status_color,
			status_bg_color: status_bg_color
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				wppatt_get_return_status_settings();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
	
	function wpsc_set_add_reason(){
		let valid = true;
		
		let return_name = jQuery('#wpsc_return_reason_name').val().trim();
		if ( return_name == '' ) {
			valid = false;
		}
		
		let return_description = jQuery('#wpsc_return_reason_description').val().trim();
		if ( return_description == '' ) {
			valid = false;
		}
		
		if ( !valid ) {
			alert('Decline Name/Description blank.');
		} else {
			
		
		
			jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
			jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
			console.log('decline name: '+return_name);
			console.log('decline description: '+return_description);

			var data = {
				action: 'wppatt_set_return_status',
				type: 'add-new-return-reason',
				return_name: return_name,
				return_description: return_description
			};
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				wpsc_modal_close();
				var response = JSON.parse(response_str);
				if (response.sucess_status=='1') {
					wppatt_get_return_status_settings();
				} else {
					jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_error').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
				}
			});
		
		}
		
	}
	
	function wpsc_set_edit_reason(return_term_id){
		let valid = true;
		
		let return_name = jQuery('#wpsc_return_reason_name').val().trim();
		if ( return_name == '' ) {
			valid = false;
		}
		
		let return_description = jQuery('#wpsc_return_reason_description').val().trim();
		if ( return_description == '' ) {
			valid = false;
		}
		
		if ( !valid ) {
			alert('Decline Name/Description blank.');
		} else {
			
		
		
			jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
			jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
			console.log('decline name: '+return_name);
			console.log('decline description: '+return_description);

			var data = {
				action: 'wppatt_set_return_status',
				type: 'set-edit-return-reason',
				return_name: return_name,
				return_description: return_description,
				return_term_id: return_term_id
			};
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				wpsc_modal_close();
				var response = JSON.parse(response_str);
				if (response.sucess_status=='1') {
					wppatt_get_return_status_settings();
				} else {
					jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_error').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
				}
			});
		
		}
		
	}
	
	
	/*
	 * Status Settings
	 */
	function wppatt_get_return_status_settings(){
	  
	  jQuery('.wpsc_setting_pills li').removeClass('active');
	  jQuery('#wppatt_settings_return').addClass('active');
	  jQuery('.wpsc_setting_col2').html(wpsc_admin.loading_html);
	  
	  var data = {
	    action: 'wppatt_get_return_settings'
// 	    setting_action : 'get_status_settings'
	  };
	
	  jQuery.post(wpsc_admin.ajax_url, data, function(response) {
	    jQuery('.wpsc_setting_col2').html(response);
	  });
	  
	}
	
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
	
</script>
