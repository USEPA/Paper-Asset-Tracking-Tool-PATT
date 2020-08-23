<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

// $GLOBALS['id'] = $_GET['id'];
$GLOBALS['recall_id'] = $_GET['id'];
$GLOBALS['pid'] = $_GET['pid'];
$subfolder_path = site_url( '', 'relative'); 

//TEST DATA 
//$GLOBALS['recall_id'] = 19;

$prefix = 'R-';
$str = $GLOBALS['recall_id'];
if (substr($str, 0, strlen($prefix)) == $prefix) {
    $GLOBALS['recall_id'] = substr($str, strlen($prefix));
} 



//include_once WPPATT_ABSPATH . 'includes/class-wppatt-functions.php';
//$load_styles = new wppatt_Functions();
//$load_styles->addStyles();

$agent_permissions = $wpscfunction->get_current_agent_permissions();

$general_appearance = get_option('wpsc_appearance_general_settings');

$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$cancel_recall_btn_css       = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';

?>


<div class="bootstrap-iso">
<?php

			/*
			* Get Data
			*/
		
			$where = [
				'recall_id' => $GLOBALS['recall_id']
			];
			$recall_array = Patt_Custom_Func::get_recall_data($where);
			//echo 'Current PHP version: ' . phpversion();
			//echo "<br>";
			
			//$recall_obj = $recall_array[0];
			
			//Added for servers running < PHP 7.3
			if (!function_exists('array_key_first')) {
			    function array_key_first(array $arr) {
			        foreach($arr as $key => $unused) {
			            return $key;
			        }
			        return NULL;
			    }
			}
			
			$recall_array_key = array_key_first($recall_array);	
			$recall_obj = $recall_array[$recall_array_key];
			
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

			
			//
			// REAL DATA
			//
			$db_null = -99999;
			$db_empty = '';
			$blank_date = '0000-00-00 00:00:00';
			//$db_null = '';			
			//$db_null = null;
			
			if($recall_obj->box_id > 0 && $recall_obj->folderdoc_id == $db_null ) {
				$recall_type = "Box";
				$title = "[Boxes Do Not Have Titles]";
				$recall_item_id = $recall_obj->box_id;
				$recall_item_id_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?pid=boxsearch&page=boxdetails&id='.$recall_item_id.'" >' .$recall_item_id. '</a>';
			} elseif ($recall_obj->box_id > 0 && $recall_obj->folderdoc_id !== $db_null ) {
				$recall_type = "Folder/File";
				$title = $recall_obj->title;
				$recall_item_id = $recall_obj->folderdoc_id;
				$recall_item_id_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?pid=boxsearch&page=filedetails&id='.$recall_item_id.'" >' .$recall_item_id. '</a>';			
			} elseif( $recall_obj->box_id > 0 && $recall_obj->folderdoc_id > 0 ) {
				$recall_type = "Test Data";
				$title = $recall_obj->title;	
				$recall_item_id = $recall_obj->folderdoc_id;			
			} elseif( $recall_obj->box_id == $db_null && $recall_obj->folderdoc_id == $db_null ) {
				$recall_type = "Not Real";
				$recall_item_id = 'Not Real Data';
			}
			
			//User Info - always put into an array.
//			if( !is_array($recall_obj->user_id))
			
			$user_obj = get_user_by('id', $recall_obj->user_id);
			//echo "<br><br>";
			//print_r($user_obj);
			$user_name = $user_obj->user_nicename;
			$user_email = $user_obj->user_email;
			
			if( is_array($recall_obj->user_id)) {	
				$real_array_of_users = $recall_obj->user_id;	
			} else {	
				$real_array_of_users = [$recall_obj->user_id];					
			}

			//$real_array_of_users = [$recall_obj->user_id];
			
			//Make Status Pretty
			$status_term_id = $recall_obj->recall_status_id;
			$status_background = get_term_meta($status_term_id, 'wppatt_recall_status_background_color', true);
			$status_color = get_term_meta($status_term_id, 'wppatt_recall_status_color', true);
			$status_style = "background-color:".$status_background.";color:".$status_color.";";
			//echo "<br>status style: ".$status_style."<br>";
			
			//Tracking Info
			$tracking_url = Patt_Custom_Func::get_tracking_url($recall_obj->tracking_number);
			$tracking_num = '<a href="' . $tracking_url.'" target="_blank">'.$recall_obj->tracking_number.'</a>';
			if ($tracking_num == $db_empty) {
				$tracking_num = "[No Tracking Number]";
			}
			
			
			//$recall_type = "Box";
			//$title = "The Most Important Document, Ever.";
// 			$record_schedule = $recall_obj->Record_Schedule_Number;
			$record_schedule = $recall_obj->Record_Schedule;
			$program_office = $recall_obj->office_acronym;
// 			$program_office = $recall_obj->office_code;
			$shipping_carrier = $recall_obj->shipping_carrier;
// 			$tracking_num = $recall_obj->tracking_number;
			$status = $recall_obj->recall_status;
			$requestor = $user_name;
			$requestor_email = $user_email;
			$comment = stripslashes($recall_obj->comments);
			$request_date = $recall_obj->request_date;
			$received_date = $recall_obj->request_receipt_date;
			$returned_date = $recall_obj->return_date;
			$ticket_id = $recall_obj->ticket_id;
			
			// Update Date Format
			//$request_date = date('m/d/yy h:m', strtotime($request_date));
			$request_date = date('m/d/yy', strtotime($request_date));
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
						
			// Role and user checks for editing restriciton
			// Checks if current user is on this request.
			$current_user_on_request = 0;
			foreach( $real_array_of_users as $user ) {
				if( $user == $current_user->ID ) {
					$current_user_on_request = 1;
				}
			}
			
			// Cancel button restriction 
			// if admin or on request
			$user_can_cancel = 0;
			if ( $agent_permissions['label'] == 'Administrator' || $current_user_on_request ) {
				$user_can_cancel = 1;
			}
			
			$status_cancelled = 0;
			if ( $status == 'Recall Cancelled' ) {
				$status_cancelled = 1;
			}
			
			
			
			//echo '<br>Current user is on request: '.$current_user_on_request.'<br>';
			//echo 'Current user can cancel: '.$user_can_cancel.'<br>';
			//echo 'Cancelled?: '.$status_cancelled.'<br>';
			
			
			
			//
			// END REAL DATA
			//
		
			
?>


  <h3>Recall Details</h3>

 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
	<div class="col-sm-12">
    	<button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=recall';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> Recall List</button>
    	
    	<button type="button" id="wppatt_recall_cancel" onclick="wppatt_cancel_recall();" class="btn btn-sm wpsc_action_btn" style="<?php echo $cancel_recall_btn_css?>"><i class="fa fa-ban"></i> Cancel Recall</button>
    	
  </div>
	
</div>

<div class="row" id="recall_details_container" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">


  <div class="col-sm-8 col-md-9 wpsc_it_body">
    <div class="row wpsc_it_subject_widget">
	    <?php if($GLOBALS['recall_id']) { ?>
    	<h3>[Recall ID # R-<?php echo $GLOBALS['recall_id']; ?>]</h3>
    </div>
	
	<div id="recall_details_sub_container">
		<div id="search_status"></div>
		<div class="">
			<label class="wpsc_ct_field_label">Recall ID:</label>
			<span id="recall_id" class=""><?php echo $GLOBALS['recall_id']; ?></span>
		</div>
		<div class="">
			<label class="wpsc_ct_field_label">Recall Type: </label>
			<span id="recall_type" class=""><?php echo $recall_type; ?></span>
		</div>
		<div class="">
			<label class="wpsc_ct_field_label"><?php echo $recall_type; ?> ID: </label>
			<span id="recall_type" class=""><?php echo $recall_item_id_link; ?></span> 
<!-- 			<span id="recall_type" class=""><?php echo $recall_item_id; ?></span>  -->
		</div>
		<div class="">
			<label class="wpsc_ct_field_label">Title: </label>
			<span id="recall_title" class=""><?php echo $title; ?></span>
		</div>
		<div class="">
			<label class="wpsc_ct_field_label">Record Schedule: </label>
			<span id="record_schedule" class=""><?php echo $record_schedule; ?></span>
		</div>
		<div class="">
			<label class="wpsc_ct_field_label">Program Office: </label>
			<span id="program_office" class=""><?php echo $program_office; ?></span>
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
				if( ($status == 'Recalled' && $agent_permissions['label'] == 'Agent') || ($status == 'On Loan' && $current_user_on_request) || $agent_permissions['label'] == 'Administrator' ) 
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
			<span id="status" class="wpsp_admin_label" style="<?php echo $status_style ?>"><?php echo $status; ?></span>
		</div>
		<div class="requestor">
			<label class="wpsc_ct_field_label">Recall Requestor(s): </label>
		</div>
		<div class="requestor">	
			<?php 
				$j = 0;
				foreach($real_array_of_users as $a_requestor) {
					$user_obj = get_user_by('id', $a_requestor);
					//print_r($user_obj);
					$user_name = $user_obj->user_nicename;
					$user_email = $user_obj->user_email;
					echo '<span id="recall_requestor" class="requestor_name">'.$user_name.'</span>';
					echo '<span id="requestor_email" class="requestor_email">['.$user_email.']</span>';
					if( $j == 0 ) {
						
						// if user is requester && requestor on this Recall
						// OR admin
						if ( $agent_permissions['label'] == 'Administrator' || $current_user_on_request ) { 
							if( $status_cancelled == 0 ) 
							{
								echo '<a href="#" onclick="wppatt_get_recall_requestor_editor()"><i class="fas fa-edit"></i></a>';
							}
						}
					}
					echo '<br>';
					$j++;
				}
				
			?>
<!--
			<span id="recall_requestor" class="requestor_name"><?php echo $requestor; ?></span>
			<span id="requestor_email" class="requestor_email">[<?php echo $requestor_email; ?>]</span>
			<a href="#" onclick="wppatt_get_recall_requestor_editor()"><i class="fas fa-edit"></i></a>
			<br>
			<span id="recall_requestor" class="requestor_name">Capt. John Yossarian</span>
			<span id="requestor_email" class="requestor_email">[Yossarian@epa.gov]</span>
-->			
			
		</div>
<!--
		<div class="">
			<label class="wpsc_ct_field_label">Recall Requestor Email: </label>
			<span id="requestor_email" class=""><?php echo $requestor_email; ?></span>
		</div>
-->
		<div class="clear">
			<div class="">
				<label class="wpsc_ct_field_label">Comment: </label>
				<span id="comment" class=""><?php echo $comment; ?></span>
			</div>
		</div>
		<div class="clear">
			<label class="wpsc_ct_field_label">Request Date: </label>
			<span id="request_date" class=""><?php echo $request_date; ?></span>
			<?php		
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
				{
					if( $status_cancelled == 0 ) 
					{
			?>
<!-- 			<a href="#" onclick="wppatt_get_date_editor('request_date')"><i class="fas fa-edit"></i></a> -->
			<div id="request_date_editor" class="calendar"></div>
			<?php
					}
				}
			?>
		</div>
		<div class="">
			<label class="wpsc_ct_field_label">Received Date: </label>
			<span id="received_date" class=""><?php echo $received_date; ?></span>
			<?php
				
				//
				// if requester && requestor on this Recall && status == shipped
				// OR Admin 
				//
						
				if ( ($status == 'Shipped' && $current_user_on_request) || $agent_permissions['label'] == 'Administrator' ) 
				{
					if( $status_cancelled == 0 ) 
					{
			?>
					<a href="#" onclick="wppatt_get_date_editor('received_date')"><i class="fas fa-edit"></i></a>

			<?php
					}
				}
			?>
		</div>
		<div class="">
			<label class="wpsc_ct_field_label">Returned Date: </label>
			<span id="returned_date" class=""><?php echo $returned_date; ?></span>
			<?php
				
				//
				// if digitzation staff && status == shipped back -- DONE
				// OR Admin 
				//
						
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
				{
					if ( $status == 'Shipped Back' ) 
					{
			?>
						<a href="#" onclick="wppatt_get_date_editor('returned_date')"><i class="fas fa-edit"></i></a>	
						<div id="returned_date_editor" class="calendar"></div>	
			<?php
					}
				}
			?>
		</div>
		
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
    if ($_GET['page'] == 'recallcreate') {
    ?>
         jQuery('.wp-submenu li:nth-child(5)').addClass('current');
    <?php
    }
    ?>
    <?php
    if ($_GET['page'] == 'recalldetails') {
    ?>
         jQuery('.wp-submenu li:nth-child(5)').addClass('current');
    <?php
    }
    ?>
	 
	 
	 // disable cancel if status not recalled. Or is user doesn't have role. 
	 jQuery('#wppatt_recall_cancel').attr('disabled', 'disabled');
	 console.log(jQuery('#status').html());
	 var user_can_cancel = <?php echo $user_can_cancel ?>;
	 if(  jQuery('#status').html() == 'Recalled' && user_can_cancel) {
		jQuery('#wppatt_recall_cancel').removeAttr('disabled');	 
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
 
<!--
	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">

							<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					      <h4 class="widget_header"><i class="fa fa-arrow-circle-right"></i> Location
					      </h4>
								<hr class="widget_divider">
								<div class="wpsp_sidebar_labels"><strong>Request ID:</strong> 
	                            <?php 
	                                echo "<a href='admin.php?page=wpsc-tickets&id=" . $box_requestid . "'>" . $box_requestid . "</a>";
	                            ?>
	                            </div>
	                            <div class="wpsp_sidebar_labels"><strong>Box ID:</strong> 
	                            <?php 
	                            if (!empty($box_boxid)) {
	                                if ($GLOBALS['pid'] == 'requestdetails') {
	                                echo "<a href='admin.php?pid=requestdetails&page=boxdetails&id=" . $box_boxid . "'>" . $box_boxid . "</a>";
	                                }
	                                if ($GLOBALS['pid'] == 'boxsearch') {
	                                echo "<a href='admin.php?pid=boxsearch&page=boxdetails&id=" . $box_boxid . "'>" . $box_boxid . "</a>";
	                                }
	                                if ($GLOBALS['pid'] == 'docsearch') {
	                                echo "<a href='admin.php?pid=docsearch&page=boxdetails&id=" . $box_boxid . "'>" . $box_boxid . "</a>";
	                                }
	                                } ?>
	                            </div>
	                            <?php
	                            //if digitization_center field is empty, will not display location on front end
	                            if(!empty($box_location)) {
	                            echo '<div class="wpsp_sidebar_labels"><strong>Digitization Center: </strong>';
	                            echo $box_location . "<br />";
	                                //if aisle/bay/shelf/position <= 0, does not display location on front end
    	                            if(!($box_aisle <= 0 || $box_bay <= 0 || $box_shelf <= 0 || $box_position <= 0))
    								{
        								echo '<div class="wpsp_sidebar_labels"><strong>Aisle: </strong>';
        	                            echo $box_aisle . "<br />";
        	                            echo '</div>';
        								echo '<div class="wpsp_sidebar_labels"><strong>Bay: </strong>';
        	                            echo $box_bay . "<br />";
        	                            echo '</div>';
        								echo '<div class="wpsp_sidebar_labels"><strong>Shelf: </strong>';
        	                            echo $box_shelf . "<br />";
        								echo '</div>';
        								echo '<div class="wpsp_sidebar_labels"><strong>Position: </strong>';
        	                            echo $box_position . "<br />";
        	                            echo '</div>';
    								}
	                            }
	                            ?> 
	                            </div>
			    		</div>
	
	</div>
-->
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
	
	var recall_id = "<?php echo $GLOBALS['recall_id'] ?>";
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
		    recall_id: recall_id,
		    recall_ids: [recall_id],
		    shipping_tracking: shipping_tracking,
		    shipping_carrier: shipping_carrier,
		    ticket_id: ticket_id,
		    from_page: 'recall-details'		    
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
// 		    jQuery('#wpsc_popup_body').html(response_str);		    
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		}); 
	}
	
	function wppatt_get_recall_requestor_editor() {
// 		alert('edit requestor');
		var recall_requestor_array = [];
//		console.log('requestor array: ');
//		console.log(recall_requestor_array);
		
		jQuery('.requestor_name').each(function() {
			
			recall_requestor_array.push(jQuery(this).text());
			//console.log(recall_requestor_array);
		});


		var requestor = jQuery('#recall_requestor').text();

		wpsc_modal_open('Edit Requestor Details');
		var data = {
		    action: 'wppatt_recall_get_requestor',
		    recall_id: recall_id,
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
		    recall_id: recall_id,
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
	
	function wppatt_cancel_recall(  ) {
		
		console.log('recall_id: '+recall_id);
		console.log('ticket id: '+ticket_id);
		
		wpsc_modal_open('Cancel Recall: R-'+recall_id);
		var data = {
		    action: 'wppatt_recall_cancel',
		    recall_id: recall_id,
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

	
</script>


<!--
Podbelski New styling for Recall page
To be added to a css file later
-->
<style type="text/css">

	#recall_details_sub_container div {
		margin-bottom: 10px;
		font-size: 15px;
	}
	
	#recall_details_sub_container div a {
		margin-left: 5px;
	}
	
	#recall_details_sub_container span {
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
