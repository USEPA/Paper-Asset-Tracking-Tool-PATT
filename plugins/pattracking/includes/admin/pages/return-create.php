<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

//$GLOBALS['id'] = $_GET['id'];
//$recall_submit_successful = $_GET['success'];
//$recall_submit_successful_id = $_GET['id'];
$subfolder_path = site_url( '', 'relative'); 

// Get current user id & convert to wpsc agent id.
$agent_ids = array();
$agents = get_terms([
	'taxonomy'   => 'wpsc_agents',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
]);
foreach ($agents as $agent) {
	$agent_ids[] = [
		'agent_term_id' => $agent->term_id,
		'wp_user_id' => get_term_meta( $agent->term_id, 'user_id', true),
	];
}

$key = array_search($current_user->ID, array_column($agent_ids, 'wp_user_id'));
$agent_term_id = $agent_ids[$key]['agent_term_id']; //current user agent term id

$test_key = array_search(5, array_column($agent_ids, 'wp_user_id'));

// Get all Return descriptions to be displayed when selecting the Return reason
if( !taxonomy_exists('wppatt_return_reason') ) {
	$args = array(
		'public' => false,
		'rewrite' => false
	);
	register_taxonomy( 'wppatt_return_reason', 'wpsc_ticket', $args );
}

$reasons = get_terms([
	'taxonomy'   => 'wppatt_return_reason',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wppatt_return_reason_load_order')),
]);

$return_reason_description_array = [];
foreach( $reasons as $reason_obj ) {
	
	$return_reason_description_array[$reason_obj->name] = get_term_meta( $reason_obj->term_id, 'wppatt_return_reason_description', true);
}


/*
echo '<pre>';
print_r($return_reason_description_array);
echo '</pre>';
*/
       
//echo 'current user id: '.$current_user->ID.'<br>';
//echo 'test_key: '.$test_key.'<br>';
//echo 'test agent term id: '.$agent_ids[$test_key]['agent_term_id'].'<br>';
//echo 'User: '.get_user_by('id', 5);
//print_r($agent_ids);


//include_once WPPATT_ABSPATH . 'includes/class-wppatt-functions.php';
//$load_styles = new wppatt_Functions();
//$load_styles->addStyles();

//PHP Styles & Appearances
$general_appearance = get_option('wpsc_appearance_general_settings');

$create_return_btn_css       = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';

$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';

$required_html = '<span style="color:red;">*</span>';


//NEW ADDITIONs PODBELSKI from ticket

$wpsc_captcha                   = get_option('wpsc_captcha');
$wpsc_terms_and_conditions      = get_option('wpsc_terms_and_conditions');
$wpsc_set_in_gdpr               = get_option('wpsc_set_in_gdpr');
$wpsc_gdpr_html                 = get_option('wpsc_gdpr_html');
$term_url                       = get_option('wpsc_term_page_url');
$wpsc_terms_and_conditions_html = get_option('wpsc_terms_and_conditions_html');
$wpsc_recaptcha_type            = get_option('wpsc_recaptcha_type');
$wpsc_get_site_key= get_option('wpsc_get_site_key');
$wpsc_allow_rich_text_editor = get_option('wpsc_allow_rich_text_editor');

$fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
		array(
      'key'       => 'agentonly',
      'value'     => '0',
      'compare'   => '='
    )
	),
]);

include WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/class-ticket-form-field-format.php';

$form_field = new WPSC_Ticket_Form_Field();

$general_appearance = get_option('wpsc_appearance_general_settings');

$create_ticket_btn_css = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';
$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_create_ticket = get_option('wpsc_create_ticket');

$description = get_term_by('slug', 'ticket_description', 'wpsc_ticket_custom_fields' );
$wpsc_desc_status = get_term_meta( $description->term_id, 'wpsc_tf_status', true);


?>

<div class="bootstrap-iso">
    
  <h3>New Decline</h3>
  
 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">



<div class="row wpsc_tl_action_bar"
	style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
	<div class="col-sm-12">
<!-- 		<button type="button" id="wpsc_load_new_create_ticket_btn" onclick="location.href='admin.php?page=returncreate';" -->
		<button type="button" id="wpsc_load_new_create_ticket_btn" onclick="location.href='admin.php?page=declinecreate';"
			class="btn btn-sm wpsc_create_ticket_btn" style="<?php echo $create_ticket_btn_css?>"><i
				class="fa fa-plus"></i> New Decline</button>
		<?php if($current_user->ID):?>
<!-- 		<button type="button" id="wpsc_load_ticket_list_btn" onclick="location.href='admin.php?page=return';" -->
		<button type="button" id="wpsc_load_ticket_list_btn" onclick="location.href='admin.php?page=decline';"
			class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i
				class="fa fa-list-ul"></i> Decline List</button>
		<?php endif;?>
	</div>
</div>
<?php
do_action('wpsc_before_create_ticket');
if(apply_filters('wpsc_print_create_ticket_html',true)):
?>


<div id="create_ticket_body" class="row"
	style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">
<!-- <div id='alert_status' class=''></div>  -->

	<form id="wppatt_frm_create_return" onsubmit="return wppatt_submit_return();" method="post">
		<div id="wppatt_loading_icon"><img src="<?php echo WPSC_PLUGIN_URL ?>asset/images/ajax-loader@2x.gif"></div>
		<div class="col-sm-3">
			
			<div class=" wpsc_sidebar individual_ticket_widget">
		    	<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					<h4 class="widget_header"><i class="fa fa-filter"></i> Add Box to Decline</h4>
		            <hr class="widget_divider">
					<div class="wpsp_sidebar_labels">Enter one or more Box IDs:<br>
						<input type='text' id='searchByID' class="form-control" data-role="tagsinput" style="height: 175px !important;" ><br>
					</div>
				</div>
			</div>	
			
			<div class="row  create_return_form_submit">
				<button type="submit" id="wpsc_create_return_submit" class="btn"
					style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_border_color']?> !important;"> Submit Decline</button>
				<button type="button" id="wpsc_create_ticket_reset" onclick="reset_return_form();" class="btn"
					style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_border_color']?> !important;"><?php _e('Reset Form','supportcandy')?></button>
				<?php do_action('wpsc_after_create_ticket_frm_btn');?>
			</div>
			
		</div>
		
		<div class="col-sm-9 wpsc_it_body">

			<div id='alert_status' class=''></div>
			
			<div class="row ">
				<div data-fieldtype="text" data-visibility="" class="col-sm-4 form-group wpsc_form_field field_222 visible wppatt_required"> 
					<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
						Decline Reason <?php echo $required_html?>
					</label>
					<div id="return_reason_div">
						<select id='return_reason'>
							<option value=''>-- Select Decline Reason --</option>
							<option value='Damaged'>Damaged</option>
							<option value='Non-record'>Non-record</option>
							<option value='Duplicate'>Duplicate</option>
							<option value='Unscannable'>Unscannable</option>
							<option value='Copyright Material'>Copyright Material</option>
							<option value='Request Cancelled before Arrival'>Request cancelled before arrival</option>
							<option value='Contents Not Prepared to Standards'>Contents not prepared to standards</option>
							<option value='Box Listing Incomplete/Missing'>Box Listing incomplete/missing</option>
							
						</select>
				  	</div>
				</div>
			
				<div data-fieldtype="text" data-visibility="" class="col-sm-8 description"> 
<!-- 					<span id="return_description" class=""></span> -->
					<div id="return_description" class=""></div>					
				</div>

			
			</div>
				
				<div  data-fieldtype="textarea" data-visibility="" class="col-sm-9 visible wppatt_required form-group wpsc_form_field ">
					<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
						Comment <?php echo $required_html ?>
					</label>
					
					<textarea id="return_comment_text" name="return_comment" rows="2" cols="30" class="form-control " style="height: auto !important;" ></textarea>
				</div>
				
				<div  data-fieldtype="text" data-visibility="" class="col-sm-9 visible wppatt_required form-group wpsc_form_field">
					<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
						Shipping Tracking Number <?php echo $required_html ?>
					</label>
					
					<input id="return_shipping_tracking" name="return_shipping_tracking" cols="30" class="form-control" > </input>
				</div>
				
<!--
				<div  data-fieldtype="text" data-visibility="" class="col-sm-9 visible wppatt_required form-group wpsc_form_field">	
					<select id='return_shipping_carrier' >
						<option value=''>-- Select Shipping Carrier --</option>
						<option value='usps'>USPS</option>
						<option value='fedex'>FedEx</option>
						<option value='ups'>UPS</option>
						<option value='dhl'>DHL</option>							
					</select>
				</div>
-->
				
				<div class="row create_ticket_fields_container">
				</div>
				
				<div class="row create_ticket_fields_container">
					<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
						Box IDs in Decline <?php echo $required_html ?>
					</label>
					
					<table id="tbl_templates_create_return" class="table table-striped table-bordered" cellspacing="5" cellpadding="5" width="100%">
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
				                <th class="datatable_header">Box ID</th>
				                <th class="datatable_header">Title</th>
				                <th class="datatable_header">Request ID</th>
				                <th class="datatable_header">Program Office</th>
<!-- 				                <th class="datatable_header">Validation</th> -->
				            </tr>
				        </thead>
				    </table>
					<br><br>
				</div>
				

		</div>
		


		
		<div class="row create_ticket_fields_container">
			<div data-fieldtype="search-results" data-visibility="" class="wpsc_required visible">
				<div id="search_results">				<!-- <div id="search_details"> -->
					
				</div>
			</div>
		</div>
		

		<input type="file" id="attachment_upload" class="hidden" onchange="">
		<input type="hidden" id="wpsc_nonce" value="<?php echo wp_create_nonce()?>">

		<input type="hidden" name="action" value="wppatt_return_submit">
		<input type="hidden" name="setting_action" value="submit_return">
		
		<input type="hidden" id="captcha_code" name="captcha_code" value="">
		<input type="hidden" id="box_fk" name="box_fk" value="">
		<input type="hidden" id="folderdoc_fk" name="folderdoc_fk" value="">				
		<input type="hidden" id="program_office_fk" name="program_office" value="">
		<input type="hidden" id="record_schedule_fk" name="record_schedule" value="">
		<input type="hidden" id="user_id" name="user_id" value="">
		<input type="hidden" id="current_date" name="current_date" value="">
		
		
<!--
		<input type="hidden" id="xxx" name="xxx" value="">		
		<input type="hidden" id="xxx" name="xxx" value="">
		<input type="hidden" id="xxx" name="xxx" value="">
-->
	</form>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.css" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.js" crossorigin="anonymous"></script>

<!--
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
-->

<style>
.readonly-input {
	display: inline-block !important;
	width: 100px !important;
	height: 0.8em !important;
}
	
#search_details {
	margin-top: 15px;
}	

#search_details > span {
	margin-left: 5px;
}

#search_results {
	padding-left: 15px;
}

#found_item_id {
	color: #555555;
	background-color: #ffffff;
	border: 1px solid #cccccc;
	border-radius: 4px;
	box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
}

.alert_spacing {
/* 	margin: 15px 0px 25px 15px; */
margin: 0px 0px 25px 15px;
}

#alert_status {
	
}

.fa-snowflake {
	color: #009ACD;
}

.fa-flag {
	color: #8b0000;
}

.fa-ban {
	color: #FF0000;
}

.create_return_form_submit {
	margin: 30px 0;
	text-align: center;
}

.description {
	padding-top: 28px;
}

#return_description {
	margin-left: -35px;
}

#wppatt_loading_icon {
	width: 100%;
	height: 100%;
	position: absolute;
	top: 35%;
	left: 43%;
	z-index: 100;
}
	
</style>


<!-- BEGIN CAR - Added Custom PATT Action -->
<?php do_action('patt_custom_imports_tickets', WPSC_PLUGIN_URL); ?>

<?php
	//$box_file_details = Patt_Custom_Func::get_box_file_details_by_id('0000288-1');
	//print_r($box_file_details);	
	//echo "<br>";
// 	$box_file_details = Patt_Custom_Func::get_box_file_details_by_id('0000288-1-01-3'); 	0000240-3-01-17
	//$box_file_details = Patt_Custom_Func::get_box_file_details_by_id('0000240-3'); 	
	//print_r($box_file_details);
	//echo "<br>";
	//$new_array = json_decode(json_encode($box_file_details), true);
	//print_r($new_array);
	//echo "<br>";
?>
<!-- END CAR - Added Custom PATT Action -->
<script type="text/javascript">
	
	var loading_icon = jQuery('#wppatt_loading_icon').hide();
	
	jQuery(document)
		.ajaxStart(function () {
			loading_icon.show();
			console.log('ajax start');
		})
		.ajaxStop(function () {
			loading_icon.hide();
			console.log('ajax end');
		});
	
	var subfolder = '<?php echo $subfolder_path ?>';
	var dataTable = null;
	
	jQuery(document).ready(function(){
		
		dataTable = jQuery('#tbl_templates_create_return').DataTable({
			'processing': true,
			'serverSide': true,
			'stateSave': true,
			"bPaginate": false,
// 			"bInfo" : false,
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
			'serverMethod': 'post',
			'searching': false, // Remove default Search Control
			'ajax': {
				'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/return_search.php',
				'data': function(data){
					// Read values
					var po_value = jQuery('#searchByProgramOffice').val();
					var po = jQuery('#searchByProgramOfficeList [value="' + po_value + '"]').data('value');
					var sg = jQuery('#searchGeneric').val();
					var boxid = jQuery('#searchByID').val();
					var dc = jQuery('#searchByDigitizationCenter').val();
					// Append to data
					data.searchGeneric = sg;
					data.searchByID = boxid;
					data.searchByProgramOffice = po;
					data.searchByDigitizationCenter = dc;
				},
				'complete': function(response) {
					console.log('success!!');
					console.log(response);
					console.log(response.responseJSON.errors);	
					console.log(typeof(response.responseJSON.errors));										
					//error_alerts(response.responseJSON.errors);
					Object.entries(response.responseJSON.errors).forEach(error_alerts);
				}
			},
// 			'lengthMenu': [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
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
// 			   { data: 'validation' },
			]
		});
  


/*
		if (jQuery('.wpsc_drop_down,.wpsc_checkbox,.wpsc_radio_btn,.wpsc_category,.wpsc_priority').val != '') {
			wpsc_reset_visibility();
		}
*/
		if (jQuery('.wpsc_drop_down,.wpsc_radio_btn,.wpsc_category,.wpsc_priority').val != '') {
			wpsc_reset_visibility();
		}		

/*
		jQuery('.wpsc_drop_down,.wpsc_checkbox,.wpsc_radio_btn,.wpsc_category,.wpsc_priority').change(function () {
			wpsc_reset_visibility();
		});
*/
		
		jQuery('.wpsc_drop_down,.wpsc_radio_btn,.wpsc_category,.wpsc_priority').change(function () {
			wpsc_reset_visibility();
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
// 		if ($_GET['page'] == 'returncreate') {
		if ($_GET['page'] == 'declinecreate') {	
		?>
			 jQuery('.wp-submenu li:nth-child(6)').addClass('current');
		<?php
		}
		?>
		<?php
// 		if ($_GET['page'] == 'returndetails') {
		if ($_GET['page'] == 'declinedetails') {	
		?>
			 jQuery('.wp-submenu li:nth-child(6)').addClass('current');
		<?php
		}
		?>
		// End highlight submenu page
		
		jQuery("#searchByID").tagsInput({
		   'defaultText':'',
		   'onAddTag': onAddTag,
		   'onRemoveTag': onRemoveTag,
		   'width':'100%'
		});
		
		jQuery("#searchByID_tag").on('paste',function(e){
		    var element=this;
		    setTimeout(function () {
		        var text = jQuery(element).val();
		        var target=jQuery("#searchByID");
		        var tags = (text).split(/[ ,]+/);
		        for (var i = 0, z = tags.length; i<z; i++) {
		              var tag = jQuery.trim(tags[i]);
		              if (!target.tagExist(tag)) {
		                    target.addTag(tag);
		              }
		              else
		              {
		                  jQuery("#searchByID_tag").val('');
		              }
		                
		         }
		    }, 0);
		});

		// Adding Return (Decline) Description on select
		jQuery('#return_reason').change( function() {

			let return_desc_str = '';
			let return_reason_description_array =  <?php echo json_encode($return_reason_description_array) ?>;
			
			return_desc_str = return_reason_description_array[jQuery('#return_reason').val()];
			
			jQuery('#return_description').html(return_desc_str);
		});



	}); // End jQuery Ready
	
	function onAddTag(tag) {
	    dataTable.state.save();
		dataTable.draw();
		console.log('Search IDs: '+jQuery('#searchByID').val());
		console.log(tag);
	}
	
	function onRemoveTag(tag) {
	    dataTable.state.save();
		dataTable.draw();
		console.log('Search IDs: '+jQuery('#searchByID').val());			
	}
	
	function error_alerts( error, index ) {
		console.log('error alert');
		console.log(index);
		console.log(error);
		
		if( error[1]['search_error'] == true ) {
			let error_message = 'Box ID: <b>"'+error[0]+'"</b> Not Found.'; 			
			set_alert( 'danger', error_message);
/*
			jQuery('#alert_status').html('<span class=" alert alert-danger">'+error_message+'</span>'); //badge badge-danger
			jQuery('#alert_status').addClass('alert_spacing');	
*/
			

			jQuery('#searchByID').removeTag(error[0]);
			dataTable.state.save();

			
		} else if( error[1]['item_error'] != null ) {
			
			let error_message;
			if( error[1]['return_id'] == null ) {
				error_message = error[1]['item_error']; 			
			}
			if (error[1]['return_id'] != null ) {
// 				let return_link = '<a href="'+subfolder+'/wp-admin/admin.php?page=returndetails&id=RTN-'+error[1]['return_id']+'" >RTN-'+error[1]['return_id']+'</a>';
// 				let return_link = '<a href="'+subfolder+'/wp-admin/admin.php?page=returndetails&id=D-'+error[1]['return_id']+'" >D-'+error[1]['return_id']+'</a>';
				let return_link = '<a href="'+subfolder+'/wp-admin/admin.php?page=declinedetails&id=D-'+error[1]['return_id']+'" >D-'+error[1]['return_id']+'</a>';
				error_message = error[1]['item_error']+return_link;
			}

			
			//let return_link = '<a href="'+subfolder+'/wp-admin/admin.php?page=returndetails&id=RTN-'+error[1]['return_id']+'" >RTN-'+error[1]['return_id']+'</a>';
			//let error_message = error[1]['item_error']+return_link; 			
			set_alert( 'danger', error_message);
			jQuery('#searchByID').removeTag(error[0]);
			dataTable.state.save();
		}
	
	}
	
	
	// Simple hash function based on java's. Used for set_alert.
	String.prototype.hashCode = function(){
	    var hash = 0;
	    for (var i = 0; i < this.length; i++) {
	        var character = this.charCodeAt(i);
	        hash = ((hash<<5)-hash)+character;
	        hash = hash & hash; // Convert to 32bit integer
	    }
	    return hash;
	}
	
	// Sets an alert
	function set_alert( type, message ) {
		
		let alert_style = '';
		let hash = message.hashCode();
		console.log({hash:hash});
		
		switch( type ) {
			case 'success':
				alert_style = 'alert-success';		
				break;
			case 'warning':
				alert_style = 'alert-warning';
				break;
			case 'danger':
				alert_style = 'alert-danger';
				break;		
		}
		jQuery('#alert_status').show();
// 		jQuery('#alert_status').html('<div class=" alert '+alert_style+'">'+message+'</div>'); //badge badge-danger
		jQuery('#alert_status').html('<div id="alert-' + hash + '" class=" alert '+alert_style+'">'+message+'</div>'); //badge badge-danger
		jQuery('#alert_status').addClass('alert_spacing');
		
		alert_dismiss( hash );
	}
	
	// Sets the time for dismissing the error notification
	function alert_dismiss( hash ) {
// 		setTimeout(function(){ jQuery('#alert_status').fadeOut(1000); }, 9000);	
		setTimeout( function(){ jQuery( '#alert-'+hash ).fadeOut( 1000 ); }, 9000 );	
	}
	
	function reset_return_form() {
		
		remove_all_tags();
		reset_all_fields();
// 		location.href='admin.php?page=returncreate';
		location.href='admin.php?page=declinecreate';
		
	}
	
	function remove_all_tags() {
		let searchIDs = jQuery('#searchByID').val().split(',');
		searchIDs.forEach( function(tag) {
			jQuery('#searchByID').removeTag(tag);
		});
		
		dataTable.state.clear();
		dataTable.draw();
	}
	
	function reset_all_fields() {
		jQuery('#return_reason').val('');
		jQuery('#return_description').html('');
		
		jQuery('#return_comment_text').val('');
		//jQuery('#return_shipping_carrier').val('');
		jQuery('#return_reason').val('');
	}
	
	
	
	
	
	
	
	
	
	
	// Search for Doc Folder File ID
	function wppatt_search_id() {
		search_id = jQuery('#wppatt_search_id_box').val().trim();
		required_html = ' <span style="color:red;">*</span>';
		//console.log("Search ID: "+search_id);
		
		if(search_id == '') {
			search_failed('blank');
		} else {
			jQuery('#found_item_id').html(wpsc_admin.loading_html);
	
			var data = {
			    action: 'wppatt_recall_search_id',
			    setting_action : 'find_search_id',
			    label: search_id
			};
			
			jQuery.ajax({
				type: "POST",
				url: wpsc_admin.ajax_url,
				data: data,
				dataType: "json",
				cache: false,
				success: function(response){
					console.log(response);
					if(response) {												
						if(response.search_error == true ) {
							search_failed( 'notfound' );
						} else {
							update_return_box_folder_file(response);
						}
					} else {
						console.log("not a valid search ID");
						search_failed( 'notfound' );
					}
				}
			
			});
		}
	}
	
	function search_failed( failure_type ) {
		var error_str = '';
		if( failure_type == 'blank' ) {
			error_str = 'Search Field Blank';
		} else if (failure_type == 'notfound' ) {
			error_str = 'Box <b>"'+search_id+'"</b> Not Found';
		}
		
		jQuery('#alert_status').html('<span class=" alert alert-danger">'+error_str+'</span>'); //badge badge-danger
		jQuery('#alert_status').addClass('alert_spacing');	
		
		jQuery('#bff_id').html('<b>No</b> Box ID'+required_html); 
		
		//Clear out any old searches
		jQuery('#found_item_id').val('');
		jQuery('#title_from_id').html('');
		jQuery('#box_fk').val('');
		jQuery('#folderdoc_fk').val('');
		jQuery('#record_schedule_name').html('');
		jQuery('#record_schedule_fk').val('');
		jQuery('#program_office_name').html('');
		jQuery('#program_office_fk').val('');	
		
	}
		
	function update_return_box_folder_file(response) {
		console.log("In update recall_box_folder_file ");
		//console.log(response);
		
		var the_id = "";
		var title = "";
		var box_fk = "";
		var folderdoc_fk;
		var db_null = -99999;
		var icon_freeze = '<i class="fas fa-snowflake" title="Freeze"></i>';
		var icon_ua_destruction = '<i class="fas fa-flag" title="Unauthorized Destruction"></i>';
		var icon_box_destroyed = '<i class="fas fa-ban" title="Box Destroyed"></i>';
		
		
		if(response.type == "Box") {
			the_id = response.box_id;
			title = '[No Title]';
			box_fk = response.Box_id_FK;
			folderdoc_fk = db_null;
			jQuery('#bff_id').html('Box ID'+required_html); 
			jQuery('#alert_status').html('<span class=" alert alert-success">Box <b>'+search_id+'</b> Found</span>'); 
			item_link = '<b><a href='+ subfolder + '/wp-admin/admin.php?pid=boxsearch&page=boxdetails&id='+the_id+' >' + the_id + '</a></b>'; 
			

		} else if(response.type == "Folder/Doc") {
			the_id = response.Folderdoc_Info_id;
			title = response.title;
// 			box_fk = db_null; 
			box_fk = response.Box_id_FK; 
			box_id = response.box_id;
			folderdoc_fk = response.Folderdoc_Info_id_FK;
			jQuery('#bff_id').html('Folder/File ID'+required_html); 
			jQuery('#alert_status').html('<span class=" alert alert-success">Folder/File <b>'+search_id+'</b> Found</span>');
			item_link = '<b><a href="'+ subfolder + '/wp-admin/admin.php?pid=boxsearch&page=filedetails&id=' + the_id +'">'+the_id+'</a></b>';
			containing_box_link = '<b><a href='+ subfolder + '/wp-admin/admin.php?pid=boxsearch&page=boxdetails&id='+box_id+' >' + box_id + '</a></b>'; 
		}
		
		jQuery('#found_item_id').val(the_id);
		jQuery('#title_from_id').html(title);
		jQuery('#box_fk').val(box_fk);
		jQuery('#folderdoc_fk').val(folderdoc_fk);
		jQuery('#record_schedule_name').html(response.Record_Schedule_Number +': '+response.Schedule_Title);
		jQuery('#record_schedule_fk').val(response.Record_Schedule_id_FK);
		jQuery('#program_office_name').html(response.office_acronym+': '+response.office_name);
		jQuery('#program_office_fk').val(response.Program_Office_id_FK);		
		

		jQuery('#alert_status').addClass('alert_spacing');		
		
		console.log('destroyed: '+response.box_destroyed);
		console.log('frozed: '+response.freeze);		
		
		// IF box destroyed  // Cannot Recall
		if ( response.box_destroyed == 1 ) {
			jQuery('#found_item_id').val('');
			//var box_link = '<b><a href=/wp-admin/admin.php?pid=boxsearch&page=boxdetails&id='+the_id+' >' + the_id + '</a></b>'; //search_id
			jQuery('#alert_status').html('<span class=" alert alert-danger">Box '+ item_link +' Destroyed - Cannot be Recalled. '+ icon_box_destroyed +'</span>'); 		
		}
		
		// If folder/file is frozen no update // Warning
		if ( response.freeze == 1 ) {
			jQuery('#alert_status').html('<span class=" alert alert-success">Folder/File <b>'+item_link+'</b> Found. '+icon_freeze+'</span>'); 	
		}
		
		// If item is already recalled // Cannot Recall
		if ( response.in_recall == true ) {
			jQuery('#found_item_id').val('');
			jQuery('#alert_status').html('<span class=" alert alert-danger">'+response.type+': '+item_link+' is currently Recalled. Recall # <b><a href="' 				+ subfolder+'/wp-admin/admin.php?page=recalldetails&id='+'R-'+response.in_recall_where+'">'+'R-'+response.in_recall_where+'</b> </span>'); 	
		}
		
		// If box contains destroyed files // Warning
		if (response.error_message == 'Box Contains Destroyed Files') {
			
			var link_str = '';
			response.destroyed_files.forEach( function(file) {
				link_str += '<b><a href="'+ subfolder + '/wp-admin/admin.php?pid=boxsearch&page=filedetails&id=' + file +'">'+file+'</a></b> ' + 							icon_ua_destruction +', ';
			});
			link_str = link_str.slice(0, -2); 
			
			var destroyed_file_num = response.destroyed_files.length;
			
			jQuery('#alert_status').html('<span class=" alert alert-warning">'+response.type+': '+ item_link +' contains ' + destroyed_file_num 
				+ ' unauthorized destroyed file(s): ' + link_str + '</span>'); 
			
		}
		
		// If Folder/File is flagged as unauthorized destruction // Cannot Recall
		if (response.error_message == 'Folder/File Unauthorized Destruction') {
			//console.log('inside recalled box');
			jQuery('#found_item_id').val('');
			jQuery('#alert_status').html('<span class=" alert alert-danger"> '+response.type+ ': ' +item_link +' flagged as Unauthorized Destruction - Cannot be Recalled. '+ icon_ua_destruction +'</span>'); 	
			
		}

		// If Folder/File is in a Recalled Box // Cannot Recall
		if (response.error_message == 'Folder/File in Recalled Box') {
			//console.log('inside recalled box');
			jQuery('#found_item_id').val('');
			jQuery('#alert_status').html('<span class=" alert alert-danger">'+response.type+': '+ item_link +' is inside Recalled Box ' + 							containing_box_link + '. Recall # <b><a href="'+ subfolder +'/wp-admin/admin.php?page=recalldetails&id='+'R-'+ 											response.in_recall_where+'">'+'R-'+ response.in_recall_where+ '</b> </span>'); 
			
		}
		
		
	}
	
	jQuery(document).ready(function () {
		
	});

	function get_captcha_code(e) {
		jQuery(e).hide();
		jQuery('#captcha_wait').show();
		var data = {
			action: 'wpsc_tickets',
			setting_action: 'get_captcha_code'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function (response) {
			jQuery('#captcha_code').val(response.trim());;
			jQuery('#captcha_wait').hide();
			jQuery(e).show();
			jQuery(e).prop('disabled', true);
		});
	}

	function wpsc_reset_visibility() {

		jQuery('.wpsc_form_field').each(function () {
			var visible_flag = false;
			var visibility = jQuery(this).data('visibility').trim();
			if (visibility) {
				visibility = visibility.split(';;');
				jQuery(visibility).each(function (key, val) {
					var condition = val.split('--');
					var cond_obj = jQuery('.field_' + condition[0]);
					var field_type = jQuery(cond_obj).data('fieldtype');
					switch (field_type) {

						case 'dropdown':
							if (jQuery(cond_obj).hasClass('visible') && jQuery(cond_obj).find('select')
								.val() == condition[1]) visible_flag = true;
							break;

						case 'checkbox':
							var check = false;
							jQuery(cond_obj).find('input:checked').each(function () {
								if (jQuery(this).val() == condition[1]) check = true;
							});
							if (jQuery(cond_obj).hasClass('visible') && check) visible_flag = true;
							break;

						case 'radio':
							if (jQuery(cond_obj).hasClass('visible') && jQuery(cond_obj).find(
									'input:checked').val() == condition[1]) visible_flag = true;
							break;

					}
				});
				if (visible_flag) {
					jQuery(this).removeClass('hidden');
					jQuery(this).addClass('visible');
				} else {
					jQuery(this).removeClass('visible');
					jQuery(this).addClass('hidden');
					var field_type = jQuery(this).data('fieldtype');
					switch (field_type) {

						case 'text':
						case 'email':
						case 'number':
						case 'date':
						case 'datetime':
						case 'url':
						case 'time':
							jQuery(this).find('input').val('');
							break;

						case 'textarea':
							jQuery(this).find('textarea').val('');
							break;

						case 'dropdown':
							jQuery(this).find('select').val('');
							break;

						case 'checkbox':
							jQuery(this).find('input:checked').each(function () {
								jQuery(this).prop('checked', false);
							});
							break;

						case 'radio':
							jQuery(this).find('input:checked').prop('checked', false);
							break;
					}
				}
			}
		});
	}
	/*
	 BEGIN CAR - Added Custom PATT Action
	 */
	<?php do_action('patt_print_js_functions_create'); ?>
	/*
	 END CAR - Added Custom PATT Action
	 */
	
	
	// Timeout Required for use case where user enters an invalid ID 
	// in #searchByID then clicks submit. The ajax function that checks
	// the validity of the ID requires a little time to confirm the ID
	// as invalid then remove it. 
	function wppatt_submit_return() {
		setTimeout(wppatt_submit_return2, 100);
		return false;
	}
	
	function wppatt_submit_return2() {
		//setTimeout(function(){ alert("Hello"); }, 3000);
		//alert("submitting");
		var validation = true;
		
		var currentUser = '<?php echo $current_user->ID; ?>';
		var currentUserAgentTermID = '<?php echo $agent_term_id; ?>';
		
		

				
		/*
			Required fields Validation
		*/
		jQuery('.visible.wppatt_required').each(function (e) { 
			var field_type = jQuery(this).data('fieldtype');
			console.log('in validation');
			console.log('field_type: '+field_type);
			console.log(this);
			switch (field_type) {

				case 'text':
					//console.log('in text');
					//console.log(!jQuery(this).find('input').val() == '');
					if (jQuery(this).find('input').val() == '') validation = false;
					if (jQuery(this).find('select').val() == '') validation = false;
					break;
				case 'textarea':
					//console.log('in textarea');
					//console.log(!jQuery(this).find('textarea').val() == '');
					if (jQuery(this).find('textarea').val() == '') validation = false;
					break;
				case 'email':
					//console.log('in email');
					//console.log(!jQuery(this).find('input').val() == '');
					if (jQuery(this).find('input').val() == '') validation = false;
					break;
				case 'number':
				case 'date':
				case 'search-results':
					//console.log('in search-results');
					//console.log(!jQuery(this).find('input').val() == '');
					if (jQuery(this).find('input').val() == '') validation = false;
					break;

				case 'textarea':
					if (jQuery(this).find('textarea').val() == '') validation = false;
					break;
			}
			

			
			if (!validation) return;
		});
		
		console.log('Validation after field elements: '+ validation);
		
		// Validate IDs in Return (Decline)
		let searchIDs = jQuery('#searchByID').val().split(',');
		//console.log('The SearchIDs: ');
		//console.log(searchIDs);
		//console.log(jQuery.inArray("", searchIDs));
		//searchIDs.splice( jQuery.inArray("", searchIDs), 1 );
		
		let searchIDs_no_blanks = searchIDs.filter(function(v){return v!==''});
		
		//console.log('Search ID Length: '+searchIDs_no_blanks.length);
		//console.log(searchIDs_no_blanks);
		if( searchIDs_no_blanks.length < 1 ) {
			console.log('in No searchIDs');
			validation = false;
		}
		
		console.log('Validation after Search IDs: '+ validation);
		
		// Validate that the shipping number entered is a valid shipping number
		let return_tracking_num = jQuery('#return_shipping_tracking').val(); 
		console.log('The tracking number in question: '+return_tracking_num);
		if( return_tracking_num ) {
			console.log('inside the tracking validation');
			var trackingIsTrue = '';
		    if (/\b(1Z ?[0-9A-Z]{3} ?[0-9A-Z]{3} ?[0-9A-Z]{2} ?[0-9A-Z]{4} ?[0-9A-Z]{3} ?[0-9A-Z]|T\d{3} ?\d{4} ?\d{3})\b/i.test(return_tracking_num)
		    || /\b((420 ?\d{5} ?)?(91|92|93|94|01|03|04|70|23|13)\d{2} ?\d{4} ?\d{4} ?\d{4} ?\d{4}( ?\d{2,6})?)\b/i.test(return_tracking_num)
		    || /\b((M|P[A-Z]?|D[C-Z]|LK|E[A-C]|V[A-Z]|R[A-Z]|CP|CJ|LC|LJ) ?\d{3} ?\d{3} ?\d{3} ?[A-Z]?[A-Z]?)\b/i.test(return_tracking_num)
		    || /\b(82 ?\d{3} ?\d{3} ?\d{2})\b/i.test(return_tracking_num)
		    || /\b(((96\d\d|6\d)\d{3} ?\d{4}|96\d{2}|\d{4}) ?\d{4} ?\d{4}( ?\d{3})?)\b/i.test(return_tracking_num)
		    || /\b(\d{4}[- ]?\d{4}[- ]?\d{2}|\d{3}[- ]?\d{8}|[A-Z]{3}\d{7})\b/i.test(return_tracking_num)) {
		    	var trackingIsTrue = true;
		    } else {
		    	var trackingIsTrue = false;
		    	//validation = false; // if set false here, then error will display incorrectly
		    	
		    	//let error_message = 'The Shipping Tracking Number is not valid.'; 			
				//set_alert( 'danger', error_message);
		    }
			
		}
		console.log('tracking validation: '+trackingIsTrue);
		
		// If Not Valid
		if (!validation) {
			console.log('Not valid.');
			let error_message = 'Required fields cannot be empty.';
/*
			if( !trackingIsTrue ) {
				error_message = 'The Shipping Tracking Number is not valid.';
			}
*/
			set_alert( 'danger', error_message);
			//jQuery('#alert_status').html('<span class="alert alert-danger">Required fields cannot be empty.</span>');
			//jQuery('#alert_status').addClass('alert_spacing');
			//alert("<?php _e('Required fields cannot be empty.','supportcandy')?>");
			return false;
		}
		
		if( !trackingIsTrue ) {
			error_message = 'The Shipping Tracking Number is not valid.';
			set_alert( 'danger', error_message);
			return false;
		}
		
		
		<?php do_action('wpsc_create_ticket_validation'); ?>
		
		if (validation) {
			
			let return_reason = jQuery('#return_reason').val();
			let return_comment = jQuery('#return_comment_text').val();
			let shipping_tracking = jQuery('#return_shipping_tracking').val();
			//let shipping_carrier = jQuery('#return_shipping_carrier').val();

			
			console.log('AJAX Data: ');
			console.log('wp_user_id: ' + currentUser);
			console.log('agent_user_id: ' + currentUserAgentTermID );
			console.log('return_reason: '+ return_reason );
			console.log('comment: ' + return_comment);
			console.log('shipping_tracking: ' + shipping_tracking);
			//console.log('shipping_carrier: ' + shipping_carrier);
			console.log('item_ids: ');
			console.log(searchIDs_no_blanks);
				
			var data = {
			    action: 'wppatt_return_submit',
			    title: 'this is real',
			    wp_user_id: currentUser,
			    agent_user_id: currentUserAgentTermID,
			    return_reason: return_reason,
			    comment: return_comment,
			    shipping_tracking: shipping_tracking,
			   // shipping_carrier: shipping_carrier,
			    item_ids: searchIDs_no_blanks,		    
			};
			
			
			

			
			
			// Submits the Return
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			    console.log("Submit Post reponse_str: ");
				console.log(response_str);
			    
			    let response = JSON.parse(response_str);
			    console.log(response.customer_name);
			    console.log(response.data);	
			    console.log(response.date);
			    console.log('return id: '+response.return_id);	  
			    console.log('error: '+response.error);  
			    
			    //window.location.reload();
			    if( response.return_id == 0 ) {
				    var failed_str = "Decline Failed. Please ensure these items are declinable. " +response.return_id;
				    alert(failed_str);
// 				    location.href='admin.php?page=returncreate&success=false';
				    location.href='admin.php?page=declinecreate&success=false';
				    

			    } else {
					var success_str = "Decline "+response.return_id+" successfully created.";   
					alert(success_str);
					remove_all_tags();
// 					location.href='admin.php?page=returncreate&success=true&id='+response.return_id; 
					location.href='admin.php?page=declinecreate&success=true&id='+response.return_id; 
			    }
			}); 
			
			
			
		}
		return false;
	}
	
	// Function for jquery equivolent of $_GET
	jQuery.extend({ 
	  getUrlVars: function(){
	    var vars = [], hash;
	    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	    for(var i = 0; i < hashes.length; i++)
	    {
	      hash = hashes[i].split('=');
	      vars.push(hash[0]);
	      vars[hash[0]] = hash[1];
	    }
	    return vars;
	  },
	  getUrlVar: function(name){
	    return jQuery.getUrlVars()[name];
	  }
	});
	
	// Get the $_GET
	var submit_success = jQuery.getUrlVar('success');
	var allVars = jQuery.getUrlVars();
	var subfolder = '<?php echo $subfolder_path ?>';
	
	// Display alert for submit
	if( submit_success == 'true' ) {
// 		jQuery('#alert_status').html('<span class="alert alert-success">Recall <b>'+allVars['id']+'</b> Created</span>');
		//jQuery('#alert_status').html('<span class="alert alert-success">Return <b><a href="'+subfolder+'/wp-admin/admin.php?page=returndetails&id='+allVars['id']+'">'+allVars['id']+'</a></b> Created</span>');
		//jQuery('#alert_status').addClass('alert_spacing');
// 		let error_message = 'Decline <b><a href="'+subfolder+'/wp-admin/admin.php?page=returndetails&id='+allVars['id']+'">'+allVars['id']+'</a></b> Created';
		let error_message = 'Decline <b><a href="'+subfolder+'/wp-admin/admin.php?page=declinedetails&id='+allVars['id']+'">'+allVars['id']+'</a></b> Created';
		set_alert( 'success', error_message );
		//alert_dismiss();
	} else if ( submit_success == 'false' ) {
		//jQuery('#alert_status').html('<span class="alert alert-danger"><b>ERROR</b> - Return Not Created</span>');
		//jQuery('#alert_status').addClass('alert_spacing');
		//alert_dismiss();
		let error_message = '<b>ERROR</b> - Decline Not Created';
		set_alert( 'danger', error_message );
	}
	
	
	

// 	<?php do_action('wpsc_print_ext_js_create_ticket'); ?>
</script>


<?php if (!$wpsc_recaptcha_type && $wpsc_captcha): ?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<?php endif; ?>
<?php
endif;
?>









	 
	 

	 
	 