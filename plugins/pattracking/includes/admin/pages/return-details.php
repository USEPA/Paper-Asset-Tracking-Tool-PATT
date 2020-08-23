<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

// $GLOBALS['id'] = $_GET['id'];
$GLOBALS['return_id'] = $_GET['id'];
$GLOBALS['pid'] = $_GET['pid'];
$subfolder_path = site_url( '', 'relative'); 

//TEST DATA 
//$GLOBALS['recall_id'] = 19;

$prefix = 'RTN-';
$str = $GLOBALS['return_id'];
if (substr($str, 0, strlen($prefix)) == $prefix) {
    $GLOBALS['return_id'] = substr($str, strlen($prefix));
    $clean_return_id = (int)$GLOBALS['return_id'];
} 

/*
echo 'Global: '.$GLOBALS['return_id'];
echo '<br>clean: '.$clean_return_id;
*/

$agent_permissions = $wpscfunction->get_current_agent_permissions();

$general_appearance = get_option('wpsc_appearance_general_settings');

$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$cancel_return_btn_css       = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';

?>


<div class="bootstrap-iso">
<?php

			/*
			* Get Data
			*/
		
			$where = [
				'return_id' => $GLOBALS['return_id']
// 				'return_id' => $clean_return_id
// 				'return_id' => '0000142'
			];
			$return_array = Patt_Custom_Func::get_return_data($where);
 			//echo 'The Object: ';
			//print_r($return_array);
			
			//Added for servers running < PHP 7.3
			if (!function_exists('array_key_first')) {
			    function array_key_first(array $arr) {
			        foreach($arr as $key => $unused) {
			            return $key;
			        }
			        return NULL;
			    }
			}
			
			$return_array_key = array_key_first($return_array);	
			$return_obj = $return_array[$return_array_key];
			
			

			
			//
			// REAL DATA
			//
			$db_null = -99999;
			$db_empty = '';
			$blank_date = '0000-00-00 00:00:00';
			
			$return_reason = $return_obj->reason;

			$comment = stripslashes($return_obj->comments);

			$tracking_url = Patt_Custom_Func::get_tracking_url($return_obj->tracking_number);
			$tracking_num = '<a href="' . $tracking_url.'" target="_blank">'.$return_obj->tracking_number.'</a>';

			$shipping_carrier = $return_obj->shipping_carrier;
			$shipping_status = $return_obj->status;
			//$real_array_of_users = ($return_obj->user_id) ? $return_obj->user_id : [];
			$return_initiated_date = $return_obj->return_date;
			$return_shipped_date = '[Remove From Display]'; 
			$returned_date = $return_obj->return_receipt_date;
			
			$box_list = ($return_obj->box_id) ? $return_obj->box_id : []; 
			$folderfile_list = ($return_obj->folderdoc_id) ? $return_obj->folderdoc_id : [];
			
			
			// Create single array with box and folderdocs
			$collated_box_folderfile_list = [];
			
			foreach( $box_list as $key=>$box ) {
				if( $box != $db_null ) {
					$collated_box_folderfile_list[] = $box;
				} else {
					$collated_box_folderfile_list[] = $folderfile_list[$key];
				}
					
			}		
			
			// Get data in the correct format for js 
 			//$searchByID = ['0000001-1','0000002-2-01-13', '0000002-3', '0000001-1-01-1', '0000001-3'];
 			$searchByID = $collated_box_folderfile_list; 			
 			$searchByIDjson = json_encode($searchByID);
 			$searchByID_alt = trim($searchByIDjson, '[');
 			$searchByID_alt = trim($searchByID_alt, ']'); 	
 			$searchByID_alt = str_replace('"', '', $searchByID_alt);
			
			
			// DEBUG	
			//
			//echo 'Current user: '.$current_user->ID.'<br>';
			//echo 'Current user term id: '.$assigned_agents[0];
			//echo "<br>Recall Object: <br>";	
			//print_r($recall_obj);
			//echo "count of array: ".count($recall_array);	
			//echo "<br>first index of array: ".array_key_first($recall_array);	
			//echo "<br>";	
			//echo "<br>Recall Array: <br>";
			//print_r($recall_array);
/*
			echo '<br><br>The lists: <br>';
			print_r($box_list);
			echo '<br>';
			print_r($folderfile_list);		
			echo '<br>'; 
			print_r($show_me);		
			echo '<br>';
			print_r($collated_box_folderfile_list);		
			echo '<br>';
*/

			
			
			// Make Status Pretty
			$status_term_id = $return_obj->return_status_id; //Not currently real. 
			$return_status_name = '';
			$status_background = get_term_meta($status_term_id, 'wppatt_return_status_background_color', true);
			$status_color = get_term_meta($status_term_id, 'wppatt_return_status_color', true);
			$status_style = "background-color:".$status_background.";color:".$status_color.";";
			//echo "<br>status style: ".$status_style."<br>";
			
			
			 // Register Box Status Taxonomy
			if( !taxonomy_exists('wppatt_return_statuses') ) {
				$args = array(
					'public' => false,
					'rewrite' => false
				);
				register_taxonomy( 'wppatt_return_statuses', 'wpsc_ticket', $args );
			}
			
			// Get List of Box Statuses
			$return_status_lut = get_terms([
				'taxonomy'   => 'wppatt_return_statuses',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
				'meta_query' => array('order_clause' => array('key' => 'wppatt_return_status_load_order')),
			]);
			
			foreach( $return_status_lut as $stat ) {
				if( $stat->term_id == $status_term_id ) {
					$return_status_name = $stat->name;
				}
			}
			
//			echo '<br>Return Statuses: <br>';
//			print_r($return_status_lut);
			
			
			
			// Tracking Info
			if ($tracking_num == $db_empty) {
				$tracking_num = "[No Tracking Number]";
			}
			
			
			// Get Users - should be failing
			$user_obj = get_user_by('id', $return_obj->user_id);
			$user_name = $user_obj->user_nicename;
			$user_email = $user_obj->user_email;

			$requestor = $user_name;
			$requestor_email = $user_email;
			$request_date = $return_obj->request_date;
			$received_date = $return_obj->request_receipt_date;
			// END should be failing
			
			
			// Set icons for shipping carriers
			$shipping_carrier_icon = '';
			if ($shipping_carrier == 'fedex' ) {
				$shipping_carrier_icon = '<i class="padding fab fa-fedex fa-lg"></i>';
			} elseif ($shipping_carrier == 'ups' ) {
				$shipping_carrier_icon = '<i class="padding fab fa-ups fa-lg"></i>';
			} elseif ($shipping_carrier == 'dhl' ) {
				$shipping_carrier_icon = '<i class="padding fab fa-dhl fa-lg"></i>';
			} elseif ($shipping_carrier == 'usps' ) {
				$shipping_carrier_icon = '<i class="padding fab fa-usps fa-lg"></i>';
			}
			
			
			// Update Date Format
			//$request_date = date('m/d/yy h:m', strtotime($request_date)); //$return_initiated_date
			$return_initiated_date = date('m/d/yy', strtotime($return_initiated_date));
			if( $received_date == $blank_date ) {
				$received_date = '[Not Yet Received]';
			} else {
				//$received_date = date('m/d/yy h:m', strtotime($received_date));
				$received_date = date('m/d/yy', strtotime($received_date));
			}
			
			if( $returned_date == $blank_date ) {
				$returned_date = '[Not Yet Returned]';
			} else {
				//$returned_date = date('m/d/yy h:m', strtotime($returned_date));
				$returned_date = date('m/d/yy', strtotime($returned_date));
			}
			
			
			
				
			// Role and user checks for editing restriciton
			// Checks if current user is on this request.
/*
			$current_user_on_request = 0;
			foreach( $real_array_of_users as $user ) {
				if( $user == $current_user->ID ) {
					$current_user_on_request = 1;
				}
			}
*/
			
			// Cancel button restriction 
			// if admin or on request
			$user_can_cancel = 0;
			if ( $agent_permissions['label'] == 'Administrator' ) {
				$user_can_cancel = 1;
			}
			
			$status_cancelled = 0;
			if ( $status_term_id == 791 ) { //Old 785; now: 791
				$status_cancelled = 1;
			}
			
?>


  <h3>Return Details</h3>

 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
	<div class="col-sm-12">
    	<button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=return';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> Return List</button>
    	
    	<button type="button" id="wppatt_return_cancel" onclick="wppatt_cancel_return();" class="btn btn-sm wpsc_action_btn" style="<?php echo $cancel_return_btn_css?>"><i class="fa fa-ban"></i> Cancel Return</button>
    	
  </div>
	
</div>

<div class="row" id="return_details_container" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">


  <div class="col-sm-8 col-md-9 wpsc_it_body">
<!--
    <div class="row wpsc_it_subject_widget">
	    <?php if($GLOBALS['return_id']) { ?>
    	<h3>[Return ID # <?php echo $GLOBALS['return_id']; ?>]</h3>
    </div>
-->
	
	<div id="return_details_sub_container">
		
		<h3>Return ID # RTN-<?php echo $GLOBALS['return_id']; ?></h3>
		<br>
		<div id="search_status"></div>
		<div class="">
			<label class="wpsc_ct_field_label">Return ID:</label>
			<span id="return_id" class=""><?php echo $GLOBALS['return_id']; ?></span>
		</div>
		<div class="">
			<label class="wpsc_ct_field_label">Return Reason: </label>
			<span id="return_reason" class=""><?php echo $return_reason; ?></span>
		</div>
		<div class="clear">
			<div class="">
				<label class="wpsc_ct_field_label">Comment: </label>
				<span id="comment" class=""><?php echo $comment; ?></span>
			</div>
		</div>

		<div class="">
			<label class="wpsc_ct_field_label">Shipping Tracking Number: </label>
			<span id="shipping_tracking" class=""><?php echo $shipping_carrier_icon; echo $tracking_num; ?></span>
			
			<?php		
				// if ( status is Recalled && digitization staff)
				// OR 
				// if ( status is On Loan && requester && requestor on this Recall
				// OR admin
				
//				($agent_permissions['label'] == 'Administrator')
//				($agent_permissions['label'] == 'Agent')
//				($agent_permissions['label'] == 'Requester')		
				if( $agent_permissions['label'] == 'Agent' || $agent_permissions['label'] == 'Administrator' ) 
				{
					if( $status_cancelled == 0 ) 
					{
		
			?>
			
			<a href="#" onclick="wppatt_get_shipping_tracking_editor()"><i class="fas fa-edit"></i></a>
			<?php
					}
				}
			?>
			
		</div>
		<div class="">
			<label class="wpsc_ct_field_label">Shipping Carrier: </label>
			<span id="shipping_carrier" class=""><?php echo $shipping_carrier_icon; echo strtoupper($shipping_carrier); ?></span>
		</div>
		<div class="">
			<label class="wpsc_ct_field_label">Status: </label>
			<span id="status" class="wpsp_admin_label" style="<?php echo $status_style ?>"><?php echo $return_status_name; ?></span>
		</div>
<!--
		<div class="requestor">
			<label class="wpsc_ct_field_label">Returning to Requestor(s): </label>
		</div>
		<div class="requestor">	
			<?php 
				$j = 0;
				foreach($real_array_of_users as $a_requestor) {
					$user_obj = get_user_by('id', $a_requestor);
					//print_r($user_obj);
					$user_name = $user_obj->user_nicename;
					$user_email = $user_obj->user_email;
					echo '<span id="return_requestor" class="requestor_name">'.$user_name.'</span>';
					echo '<span id="requestor_email" class="requestor_email">['.$user_email.']</span>';
					if( $j == 0 ) {
						
						// if user is requester && requestor on this Recall
						// OR admin
						if ( $agent_permissions['label'] == 'Administrator' || $current_user_on_request ) { 
							if( $status_cancelled == 0 ) 
							{
								echo '<a href="#" onclick="wppatt_get_return_requestor_editor()"><i class="fas fa-edit"></i></a>';
							}
						}
					}
					echo '<br>';
					$j++;
				}
				
			?>
		</div>	
-->
		<div class="clear">
			<label class="wpsc_ct_field_label">Return Initiated Date: </label>
			<span id="request_date" class=""><?php echo $return_initiated_date; ?></span>
			
		</div>
<!--
		<div class="">
			<label class="wpsc_ct_field_label">Return Shipped Date: </label>
			<span id="received_date" class=""><?php echo $return_shipped_date; ?></span>
			
		</div>
-->
		<div class="">
			<label class="wpsc_ct_field_label">Returned Date: </label>
			<span id="returned_date" class=""><?php echo $returned_date; ?></span>
		</div>
		
		
		

		
		
	</div>

	
	<div class="row create_ticket_fields_container">
			<label class="wpsc_ct_field_label" >
				Box/Folder/File IDs in Return
			</label>
			
			<table id="tbl_templates_return_details" class="table table-striped table-bordered" cellspacing="5" cellpadding="5" width="100%">
		        <thead>
		            <tr>
					<?php		
					if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
					{
					?>
					                <th class="datatable_header"></th>
					<?php
					}
					?>
		                <th class="datatable_header">Box/Folder/File ID</th>
		                <th class="datatable_header">Title</th>
		                <th class="datatable_header">Request ID</th>
		                <th class="datatable_header">Program Office</th>
<!-- 		                <th class="datatable_header">Validation</th> -->
		            </tr>
		        </thead>
		    </table>
			<br><br>
		</div>
	
	
	
	
	
	

<!-- Pop-up snippet start -->
<div id="wpsc_popup_background" style="display:none;"></div>
<div id="wpsc_popup_container" style="display:none;">
  <div class="bootstrap-iso">
    <div class="row">
      <div id="wpsc_popup" class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
        <div id="wpsc_popup_title" class="row"><h3>Modal Title</h3></div>
        <div id="wpsc_popup_body" class="row">I am body!</div>
        <div id="wpsc_popup_footer" class="row">
          <button type="button" class="btn wpsc_popup_close"><?php _e('Close','supportcandy');?></button>
          <button type="button" class="btn wpsc_popup_action"><?php _e('Save Changes','supportcandy');?></button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Pop-up snippet end -->

<br />

<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>
<script>
 jQuery(document).ready(function() {
	 
	dataTable = jQuery('#tbl_templates_return_details').DataTable({
		'processing': true,
		'serverSide': true,
		'stateSave': true,
		"bPaginate": false,
// 			"bInfo" : false,
/*
		'stateSaveParams': function(settings, data) {
			data.sg = jQuery('#searchGeneric').val();
			data.bid = jQuery('#searchByID').val();
			data.po = jQuery('#searchByProgramOffice').val();
			data.dc = jQuery('#searchByDigitizationCenter').val();
		},
		'stateLoadParams': function(settings, data) {
			jQuery('#searchGeneric').val(data.sg);
			jQuery('#searchByID').val(data.bid);
			jQuery('#searchByProgramOffice').val(data.po);
			jQuery('#searchByDigitizationCenter').val(data.dc);
		},
*/
		'serverMethod': 'post',
		'searching': false, // Remove default Search Control
		'ajax': {
			'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/return_details_processing.php',
			'data': function(data){
				// Read values
				var returnids = '<?php echo $searchByID_alt; ?>';
				
				// Append to data
				data.searchByID = returnids;
				
			},
			'complete': function(response) {
				console.log('success!!');
				console.log(response);
				console.log(response.responseJSON.errors);	
				console.log(typeof(response.responseJSON.errors));										
				//error_alerts(response.responseJSON.errors);
				//Object.entries(response.responseJSON.errors).forEach(error_alerts);
			}
		},
// 		'lengthMenu': [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
		'lengthMenu': [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
		<?php		
		if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
		{
		?>
			    'columnDefs': [	
		     {	
		        'targets': 0,
		        'checkboxes': {	
		           'selectRow': true	
		        }	
		     }
		  ],
		  'select': {	
		     'style': 'multi'	
		  },
		  'order': [[1, 'asc']],
		<?php
		}
		?>
		'columns': [
		<?php		
		if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
		{
		?>
		   { data: 'box_id' }, 
		<?php
		}
		?>
		   { data: 'box_id_flag' }, 
		   { data: 'title' },
		   { data: 'request_id' },
		   { data: 'program_office' },
// 		   { data: 'validation' },
		]
	});
	
	
	
	
	
	
	 
	 
	 
	// Updates Admin Menu to highlight the submenu page that this page is under. 
	jQuery('#toplevel_page_wpsc-tickets').removeClass('wp-not-current-submenu'); 
	jQuery('#toplevel_page_wpsc-tickets').addClass('wp-has-current-submenu'); 
	jQuery('#toplevel_page_wpsc-tickets').addClass('wp-menu-open'); 
	jQuery('#toplevel_page_wpsc-tickets a:first').removeClass('wp-not-current-submenu');
	jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-has-current-submenu'); 
	jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-menu-open');
	jQuery('#menu-dashboard').removeClass('current');
	jQuery('#menu-dashboard a:first').removeClass('current');
	 
	<?php
	if ($_GET['page'] == 'returncreate') {
	?>
		 jQuery('.wp-submenu li:nth-child(6)').addClass('current');
	<?php
	}
	?>
	<?php
	if ($_GET['page'] == 'returndetails') {
	?>
		 jQuery('.wp-submenu li:nth-child(6)').addClass('current');
	<?php
	}
	?>
	 
	 
	 
	 
	 // disable cancel if status not recalled. Or is user doesn't have role. 
	 jQuery('#wppatt_return_cancel').attr('disabled', 'disabled');
	 console.log(jQuery('#status').html());
	 var user_can_cancel = <?php echo $user_can_cancel ?>;
	 if(  jQuery('#status').html() == 'Return Initiated' && user_can_cancel) {
		jQuery('#wppatt_return_cancel').removeAttr('disabled');	 
	 }


} );

		function wpsc_get_folderfile_editor(doc_id){
<?php
			$box_il_val = '';
			if ($box_il == 1) {
?>
		  wpsc_modal_open('Edit Folder Metadata');
<?php
			} else {
?>
		  wpsc_modal_open('Edit File Metadata');
<?php
			}
?>

		  var data = {
		    action: 'wpsc_get_folderfile_editor',
		    doc_id: doc_id
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  });  
		}
</script>


  </div>
 

<?php
} else {

echo '<span style="padding-left: 10px">Please pass a valid Recall ID</span>';

}
?>
</div>
</div>
<!-- </div> -->


<!--
Podbelski New Scripts for the Page
Recall Editing
-->

<script>
	
	var return_id = "<?php echo $GLOBALS['return_id'] ?>";
	var ticket_id = "<?php echo $ticket_id ?>";
	//recall_id = 3; //Test data
	//IMPLEMENT: check to ensure that valid recall_id.
	
	function wppatt_get_shipping_tracking_editor() {
		//alert('edit tracking');
		var shipping_tracking = jQuery('#shipping_tracking').text();
		var shipping_carrier = jQuery('#shipping_carrier').text();
		//alert('shipping tracking: '+shipping_tracking+' carrier: '+shipping_carrier);
		
		wpsc_modal_open('Edit Shipping Details');
		
		var data = {
		    action: 'wppatt_recall_get_shipping',
		    recall_id: return_id,
		    recall_ids: [return_id],
		    return_ids: [return_id],		    
		    shipping_tracking: shipping_tracking,
		    shipping_carrier: shipping_carrier,
		    ticket_id: ticket_id,
		    category: 'return',
		    from_page: 'return-details'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
// 		    jQuery('#wpsc_popup_body').html(response_str);		    
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		}); 
	}
	
	function wppatt_get_return_requestor_editor() {
// 		alert('edit requestor');
		var return_requestor_array = [];
//		console.log('requestor array: ');
//		console.log(recall_requestor_array);
		
		jQuery('.requestor_name').each(function() {
			
			return_requestor_array.push(jQuery(this).text());
			//console.log(recall_requestor_array);
		});


		var requestor = jQuery('#return_requestor').text();

		wpsc_modal_open('Edit Requestor Details');
		var data = {
		    action: 'wppatt_recall_get_requestor',
		    recall_id: return_id,
		    ticket_id: ticket_id,
		    requestor: requestor
		    
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
// 		    jQuery('#wpsc_popup_body').html(response_str);		    
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		}); 
	} 
	
	
	function wppatt_get_date_editor(date_type) {
 		//alert('Date Type: '+date_type);
		//jQuery('.datepicker').datepicker();
		
		switch (date_type) {
			case 'request_date':
				var title = 'Request';
				var old_date = jQuery('#request_date').text();
				console.log("old date: "+old_date);
				break;
			case 'received_date':
				 var title = 'Received';
				 var old_date = jQuery('#received_date').text();
				break;
			case 'returned_date':
				 var title = 'Returned';
				 var old_date = jQuery('#returned_date').text();
				break;
			default:
				var title = 'false';
		}
		
// 		alert('Date Title: '+title);
		
		
		wpsc_modal_open('Edit '+title+' Date Details');
		var data = {
		    action: 'wppatt_recall_get_date',
		    recall_id: return_id,
		    date_type: date_type,
		    title: title,
		    old_date: old_date,
		    ticket_id: ticket_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
//		    jQuery('#wpsc_popup_body').html(response_str);		    
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		}); 
		
	}
	
	function wppatt_cancel_return(  ) {
		
		console.log('return_id: '+return_id);
		console.log('ticket id: '+ticket_id);
		
		wpsc_modal_open('Cancel Return: RTN-'+return_id);
		var data = {
		    action: 'wppatt_return_cancel',
		    return_id: return_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
//		    jQuery('#wpsc_popup_body').html(response_str);		    
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		}); 

	}

	
</script>


<!--
Podbelski New styling for Recall page
To be added to a css file later
-->
<style type="text/css">

	#return_details_sub_container {
		padding: 0 20px;	
	}

	#return_details_sub_container div {
		margin-bottom: 10px;
		font-size: 15px;
	}
	
	#return_details_sub_container div a {
		margin-left: 5px;
	}
	
	#return_details_sub_container span {
		font-size: 15px;
		padding-left: 7px;
	}
	
	.calendar {
		display: inline-flex;
	}
	
	.requestor {
		float: left;
		display: inline-block;

	}
	
	.clear {
		clear: both;
	}
	
	.padding {
		padding-right: 5px;
	}
	
</style>
