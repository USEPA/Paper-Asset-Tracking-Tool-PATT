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

$create_recall_btn_css       = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';

$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';

$required_html = '<span style="color:red;">*</span>';


//NEW Additions from ticket

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
    
  <h3>New Recall</h3>
  
 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">



<div class="row wpsc_tl_action_bar"
	style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
	<div class="col-sm-12">
		<button type="button" id="wpsc_load_new_create_ticket_btn" onclick="location.href='admin.php?page=recallcreate';"
			class="btn btn-sm wpsc_create_ticket_btn" style="<?php echo $create_ticket_btn_css?>"><i
				class="fa fa-plus"></i> New Recall</button>
		<?php if($current_user->ID):?>
		<button type="button" id="wpsc_load_ticket_list_btn" onclick="location.href='admin.php?page=recall';"
			class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i
				class="fa fa-list-ul"></i> Recall List</button>
		<?php endif;?>
	</div>
</div>
<?php
do_action('wpsc_before_create_ticket');
if(apply_filters('wpsc_print_create_ticket_html',true)):
?>








<!-- ADD NEW -->
<div id="create_ticket_body" class="row"
	style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">
<!-- <div id='alert_status' class=''></div>  -->

	<form id="wppatt_frm_create_recall" onsubmit="return wppatt_submit_recall();" method="post">
		<div id="wppatt_loading_icon"><img src="<?php echo WPSC_PLUGIN_URL ?>asset/images/ajax-loader@2x.gif"></div>
		<div class="col-sm-3">
			
			<div class=" wpsc_sidebar individual_ticket_widget">
		    	<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					<h4 class="widget_header"><i class="fa fa-filter"></i> Add Box/Folder/File to Recall</h4>
		            <hr class="widget_divider">
					<div class="wpsp_sidebar_labels">Enter a single Box/Folder/File ID:<br>
						<input type='text' id='searchByID' class="form-control" data-role="tagsinput" style="height: 50px !important;" ><br>
					</div>
				</div>
			</div>	
			
			<div class="row  create_return_form_submit">
				<button type="submit" id="wpsc_create_recall_submit" class="btn"
					style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_border_color']?> !important;"> Submit Recall</button>
				<button type="button" id="wpsc_create_ticket_reset" onclick="reset_recall_form();" class="btn"
					style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_border_color']?> !important;"><?php _e('Reset Form','supportcandy')?></button>
				<?php do_action('wpsc_after_create_ticket_frm_btn');?>
			</div>
			
		</div>
		
		<div class="col-sm-9 wpsc_it_body">

			<div id='alert_status' class=''></div>
			
			<div class="row ">
<!--
				<div data-fieldtype="text" data-visibility="" class="col-sm-4 form-group wpsc_form_field field_222 visible wppatt_required"> 
					<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
						Return Reason <?php echo $required_html?>
					</label>
					<div id="return_reason_div">
						<select id='return_reason'>
							<option value=''>-- Select Return Reason --</option>
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
					<div id="return_description" class=""></div>					
				</div>
-->

				<div  data-fieldtype="text" data-visibility="" class="col-sm-6 form-group wpsc_form_field field_222 requestor_hidden"> 
					<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
						Requestor Username <?php echo $required_html?> <span class="testing_only">** FOR TESTING ONLY **</span>
					</label>
					<div id="assigned_agent">
						<div class="form-group wpsc_display_assign_agent ">
						    <input id="requestor_input" class="form-control  wpsc_assign_agents ui-autocomplete-input" name="assigned_agent"  type="text" autocomplete="off" placeholder="<?php _e('Search agent ...','supportcandy')?>" onkeypress="check_color(this.id)" />
								<ui class="wpsp_filter_display_container"></ui>
						</div>
					</div>
					<div id="assigned_agents" class="form-group col-md-12 visible wpsc_required">
						<?php
							 $agent_name = get_term_meta( $agent_term_id, 'label', true);
							 	
							if($agent_term_id && $agent_name):
						?>
								<div class="form-group wpsp_filter_display_element wpsc_assign_agents ">
	<!-- 								<div class="flex-container" style="padding:10px;font-size:1.0em;"> -->
									<div class="flex-container staff-badge" style=""> 									
										<?php echo htmlentities($agent_name)?><span class="staff-close" onclick="wpsc_remove_filter(this);remove_user();"><i class="fa fa-times"></i></span>
										  <input type="hidden" name="assigned_agent[]" value="<?php echo htmlentities($agent_term_id) ?>" />
									</div>
								</div>
						<?php
							endif;
						?>
				  	</div>
	<!--
					<input type="hidden" name="action" value="wpsc_tickets" />
					<input type="hidden" name="setting_action" value="set_change_assign_agent" />
					<input type="hidden" name="recall_id" value="<?php echo htmlentities($recall_id) ?>" />
	-->
				</div>
			
			</div>
				
				<div  data-fieldtype="textarea" data-visibility="" class="col-sm-9 <?php echo $form_field->visibility? 'hidden':'visible'?> <?php echo $form_field->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
					<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
						Comment <?php echo $required_html ?>
					</label>
					
					<textarea name="recall_comment" rows="3" cols="30" class="form-control " style="height: auto !important;" onkeypress="check_color(this.id)" ></textarea>
				</div>
				
				
				
<!--
				<div  data-fieldtype="textarea" data-visibility="" class="col-sm-9 visible wppatt_required form-group wpsc_form_field ">
					<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
						Comment <?php echo $required_html ?>
					</label>
					
					<textarea id="return_comment_text" name="return_comment" rows="2" cols="30" class="form-control " style="height: auto !important;" ></textarea>
				</div>
-->
				
<!--
				<div  data-fieldtype="text" data-visibility="" class="col-sm-9 visible wppatt_required form-group wpsc_form_field">
					<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
						Shipping Tracking Number <?php echo $required_html ?>
					</label>
					
					<input id="return_shipping_tracking" name="return_shipping_tracking" cols="30" class="form-control" > </input>
				</div>
				
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
						Box/Folder/File IDs in Recall <?php echo $required_html ?>
					</label>
					
					<table id="tbl_templates_create_recall" class="table table-striped table-bordered" cellspacing="5" cellpadding="5" width="100%">
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
				                <th class="datatable_header">Record Schedule</th>
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
		

<!--
		<input type="file" id="attachment_upload" class="hidden" onchange="">
		<input type="hidden" id="wpsc_nonce" value="<?php echo wp_create_nonce()?>">
-->

<!--
		<input type="hidden" name="action" value="wppatt_return_submit">
		<input type="hidden" name="setting_action" value="submit_return">
-->
		
		<input type="hidden" id="captcha_code" name="captcha_code" value="">
		<input type="hidden" id="box_fk" name="box_fk" value="">
		<input type="hidden" id="folderdoc_fk" name="folderdoc_fk" value="">				
		<input type="hidden" id="program_office_fk" name="program_office" value="">
		<input type="hidden" id="record_schedule_fk" name="record_schedule" value="">
		<input type="hidden" id="user_id" name="user_id" value="">
		<input type="hidden" id="current_date" name="current_date" value="">
		
		

	</form>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.css" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.js" crossorigin="anonymous"></script>

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
	margin: 15px 0px 25px 15px;
/* margin: 0px 0px 25px 15px; */


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

.requestor_hidden {
/* 	display: none; */
}

.testing_only {
	color: #808080;
}
	
</style>

<!-- ADD DONE END -->








<!--
<div id="create_ticket_body" class="row"
	style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">
-->
<!-- <div id='alert_status' class=''></div>  -->

<!-- 	<form id="wppatt_frm_create_recall" onsubmit="return wppatt_submit_recall();" method="post"> -->

		
		
		<script>

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
			
			
		jQuery(document).ready(function(){
			
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
			
			jQuery("input[name='assigned_agent']").keypress(function(e) {
				//Enter key
				if (e.which == 13) {
					return false;
				}
			});
			
			jQuery( ".wpsc_assign_agents" ).autocomplete({
					minLength: 0,
					appendTo: jQuery('.wpsc_assign_agents').parent(),
					source: function( request, response ) {
						var term = request.term;
						request = {
							action: 'wpsc_tickets',
							setting_action : 'filter_autocomplete',
							term : term,
							field : 'assigned_agent',
						}
						jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
							response(data);
						});
					},
					select: function (event, ui) {
		/*
						var html_str = '<li class="wpsp_filter_display_element">'
														+'<div class="flex-container">'
															+'<div class="wpsp_filter_display_text">'
																+ui.item.label
																+'<input type="hidden" name="assigned_agent[]" value="'+ui.item.flag_val+'">'
		// 														+'<input type="hidden" name="new_requestor" value="'+ui.item.flag_val+'">'
															+'</div>'
															+'<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></div>'
														+'</div>'
													+'</li>';
		*/
													
						html_str = get_display_user_html(ui.item.label, ui.item.flag_val);
		// 				jQuery('#assigned_agent .wpsp_filter_display_container').append(html_str);
						jQuery('#assigned_agents').append(html_str);
						
						
		// 				jQuery('#assigned_agent .wpsp_filter_display_container').replace(html_str);
						//Add code for only single user: https://stackoverflow.com/questions/22971580/jquery-append-element-if-it-doesnt-exist-otherwise-replace
						jQuery("#button_requestor_submit").show();
					    jQuery(this).val(''); 
					    return false;
					}
			}).focus(function() {
					jQuery(this).autocomplete("search", "");
			});
		
		});
		
		function get_display_user_html(user_name, termmeta_user_val) {
			//console.log("in display_user");
			var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
			
			if( requestor_list.indexOf(termmeta_user_val.toString()) >= 0 ) {
				console.log('termmeta_user_val: '+termmeta_user_val+' is already listed');
				html_str = '';
			} else {
		
				var html_str = '<div class="form-group wpsp_filter_display_element wpsc_assign_agents ">'
								+'<div class="flex-container staff-badge" style="">'
									+user_name
									+'<span class="staff-close" onclick="wpsc_remove_filter(this);remove_user();"><i class="fa fa-times"></i></span>'
								+'<input type="hidden" name="assigned_agent[]" value="'+termmeta_user_val+'" />'
								+'</div>'
							+'</div>';	
		
			}
					
			return html_str;		
		
		}
		
		function remove_user() {
			//if zero users remove save
			//if more than 1 user show save
			var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
			if( requestor_list.length >= 0 ) {
				jQuery("#button_requestor_submit").show();
			} else {
				jQuery("#button_requestor_submit").hide();
			}
		}
		
		</script>
		
		
		
		
		


	

<!--
		<input type="hidden" name="action" value="wppatt_recall_submit">
		<input type="hidden" name="setting_action" value="submit_recall">
		
		<input type="hidden" id="captcha_code" name="captcha_code" value="">
		<input type="hidden" id="box_fk" name="box_fk" value="">
		<input type="hidden" id="folderdoc_fk" name="folderdoc_fk" value="">				
		<input type="hidden" id="program_office_fk" name="program_office" value="">
		<input type="hidden" id="record_schedule_fk" name="record_schedule" value="">
		<input type="hidden" id="user_id" name="user_id" value="">
		<input type="hidden" id="current_date" name="current_date" value="">
-->
		
		

<!-- 	</form> -->
<!-- </div> -->

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

.staff-badge {
	padding: 3px 3px 3px 5px;
	font-size:1.0em !important;
	vertical-align: middle;
}

.staff-close {
	margin-left: 3px;
	margin-right: 3px;
}

#wppatt_search_id_button {
	margin-top: 10px;
}

.form-group {
	margin-bottom: 5px !important;
}

.blank_validation {
	background-color: #E8C6C6 !important;
}
	
</style>


<!-- BEGIN CAR - Added Custom PATT Action -->
<?php do_action('patt_custom_imports_tickets', WPSC_PLUGIN_URL); ?>


<!-- END CAR - Added Custom PATT Action -->
<script type="text/javascript">
	
	var subfolder = '<?php echo $subfolder_path ?>';
	var style_normal = 'background-color: #ffffff !important';
	var style_danger = 'background-color: #E8C6C6 !important';
	
	function check_color(id) {
		console.log(id);
		if( id == 'wppatt_search_id_box') {
			jQuery('input#wppatt_search_id_box').attr('style', style_normal);	
		} else if( id == '') {
			jQuery('textarea[name="recall_comment"]').attr('style', style_normal);	
		} else if( id == 'found_item_id') {
			jQuery('input#found_item_id').attr('style', style_normal);						
		} else if( id == 'requestor_input') {
			jQuery('input#requestor_input').attr('style', style_normal);
		} 
		
	}
	
	
	
	
	
	
	
	
	
	
	// DATATABLE NEW - START
	var dataTable = null;
	
	jQuery(document).ready(function(){
		
		dataTable = jQuery('#tbl_templates_create_recall').DataTable({
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
				'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/ajax/get_recall_search_id.php',
				'data': function(data){
					// Read values
					//var po_value = jQuery('#searchByProgramOffice').val();
					//var po = jQuery('#searchByProgramOfficeList [value="' + po_value + '"]').data('value');
					//var sg = jQuery('#searchGeneric').val();
					var boxid = jQuery('#searchByID').val();
					//var dc = jQuery('#searchByDigitizationCenter').val();
					// Append to data
					//data.searchGeneric = sg;
					data.searchByID = boxid;
					//data.searchByProgramOffice = po;
					//data.searchByDigitizationCenter = dc;
				},
				'complete': function(response) {
					console.log('success!!');
					console.log(response);
					//console.log(response.responseJSON.errors);	
					//console.log(typeof(response.responseJSON.errors));
					
					let res = response.responseJSON.details;
					console.log('The res');
					console.log(res);
					
					if(res.searchByID) {												
						if(res.search_error == true ) {
							console.log("notfound");
							search_failed_2( 'notfound', res.searchByID[0] ); // use search_failed for OLD button Recall page
						} else {
							//update_recall_box_folder_file(res); // used for OLD button Recall page
							//error_alerts(res);
							update_page(res);
						}
					} else {
						console.log("not a valid search ID");
						//search_failed_2( 'blank', '' ); // use search_failed for OLD button Recall page
					}
					
															
					
					//Object.entries(response.responseJSON.errors).forEach(error_alerts);
					
					
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
			     },
			     { width: '25px', targets: 1 },
				 { width: '50px', targets: 2 },
				 { width: '40px', targets: 3 },
				 { width: '50px', targets: 4 }
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
		
		
		jQuery("#searchByID").tagsInput({
		   'defaultText':'',
		   'onAddTag': onAddTag,
		   'onRemoveTag': onRemoveTag,
		   'width':'100%',
		   'min-height' :'50px',
		   'height' :'50px'
		});
		
		// Pasting ID into search box
		jQuery("#searchByID_tag").on('paste',function(e){
		    var element=this;
		    setTimeout(function () {
		        var text = jQuery(element).val();
		        var target=jQuery("#searchByID");
		        var tags = (text).split(/[ ,]+/);
		        for (var i = 0, z = tags.length; i<z; i++) {
					var tag = jQuery.trim(tags[i]);
					//if (!target.tagExist(tag)) {
					if (!target.tagExist(tag) && i < 1 ) {	// only allow one tag to be pasted. 
					    target.addTag(tag);
					}
					else
					{
					  jQuery("#searchByID_tag").val('');
					}
		                
		        }
		    }, 0);
		});
		
		//
		
		console.log('num of tags: ');

		// Removes 2nd entered tag.
	    jQuery("#searchByID_tag").on("change", function() {
		    console.log('we inside!!');
		    setTimeout( function() {
				console.log('the length: ');
				let num_of_tags = jQuery('#searchByID_tagsinput').children('.tag').length;
				console.log(num_of_tags);
				
				let last_tag = jQuery('#searchByID_tagsinput').children('.tag').last();
				console.log(last_tag);
				console.log(last_tag[0]['textContent']);
				
				if( num_of_tags > 1 ) {
					//$('#tags').removeTag('bar');
					console.log('remove ');
					
					let tag_list = jQuery('#searchByID').val().split(',');
					let tag_exists = jQuery("#searchByID").tagExist(tag_list[1]);
					
					console.log(tag_list);
					console.log(tag_exists);
					
					if( tag_exists ) {
						jQuery("#searchByID").removeTag(tag_list[1]);
						console.log('tag removed');
						// Need to set alert: only one Box/Folder/File ID per Recall. 
						// Issue with pasting IDs. 
					}
				}
				
  			}, 0);
	    });

		
	}); // End jQuery Ready
	
	
	function remove_search_tags() {
		jQuery('#searchByID').importTags('');
		console.log('all search tags removed.');
	}
	
	function onAddTag(tag) {
	    
	    //dataTable.state.save();
		dataTable.draw();
		console.log('Search IDs: '+jQuery('#searchByID').val());
		console.log(tag);
	}
	
	function onRemoveTag(tag) {
	    //dataTable.state.save();
		dataTable.draw();
		console.log('Search IDs: '+jQuery('#searchByID').val());			
	}
	
	// DATATABLE - END
	
	
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
	
	
	// Sets an error message notificaiton
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
 		jQuery('#alert_status').html('<div id="alert-' + hash + '" class=" alert '+alert_style+'">'+message+'</div>'); //badge badge-danger
		//jQuery('#alert_status').append('<div id="alert-' + hash + '" class=" alert '+alert_style+'">'+message+'</div>'); // shows more notificaitons than desired. 
		jQuery('#alert_status').addClass('alert_spacing');
		
		alert_dismiss( hash );
	}
	
	// Sets the time for dismissing the error notification
	function alert_dismiss( hash ) {
// 		setTimeout(function(){ jQuery('#alert_status').fadeOut(1000); }, 9000);	
		setTimeout( function(){ jQuery( '#alert-'+hash ).fadeOut( 1000 ); }, 9000 );	
	}
	
	// Sets error notification for DataTable based New Recall and clears the tags.
	function search_failed_2( failure_type, item_id ) {
		var error_str = '';
		if( failure_type == 'blank' ) {
			error_str = 'Search Field Blank';
			console.log('This should never happen. A user can no longer accidently search for nothing.');			

		} else if (failure_type == 'notfound' ) {
			error_str = 'Box/Folder/File <b>"'+item_id+'"</b> Not Found.';
			//jQuery('input#found_item_id').attr('style', style_danger);	
		}
		
		set_alert( 'danger', error_str );
		remove_search_tags();
		
	}
	
	// Handles all error notifications for search response
	function update_page( data ) {
		console.log('update_page');
		console.log(data);
		
		var the_id = "";
		var title = "";
		var box_fk = "";
		var folderdoc_fk;
		var db_null = -99999;
		var icon_freeze = '<i class="fas fa-snowflake" title="Freeze"></i>';
		var icon_ua_destruction = '<i class="fas fa-flag" title="Unauthorized Destruction"></i>';
		var icon_box_destroyed = '<i class="fas fa-ban" title="Box Destroyed"></i>';
		
		
		let search_id = data.searchByID[0];
		let item_link = '';
		let containing_box_link = '';
		let message = '';
		
		
		if(data.type == "Box") {
			the_id = data.box_id;
			title = '[No Title]';
			box_fk = data.Box_id_FK;
			folderdoc_fk = db_null;
			//jQuery('#bff_id').html('Box ID'+required_html); 
			//jQuery('#alert_status').html('<span class=" alert alert-success">Box <b>'+search_id+'</b> Found</span>'); 
			message = 'Box <b>'+search_id+'</b> Found.';
			set_alert( 'success', message );
			item_link = '<b><a href='+ subfolder + '/wp-admin/admin.php?pid=boxsearch&page=boxdetails&id='+the_id+' >' + the_id + '</a></b>'; 
			

		} else if(data.type == "Folder/Doc") {
			the_id = data.Folderdoc_Info_id;
			title = data.title;
// 			box_fk = db_null; 
			box_fk = data.Box_id_FK; 
			box_id = data.box_id;
			folderdoc_fk = data.Folderdoc_Info_id_FK;
			
			message = 'Folder/File <b>'+search_id+'</b> Found.';
			set_alert( 'success', message );
			item_link = '<b><a href="'+ subfolder + '/wp-admin/admin.php?pid=boxsearch&page=filedetails&id=' + the_id +'">'+the_id+'</a></b>';
			containing_box_link = '<b><a href='+ subfolder + '/wp-admin/admin.php?pid=boxsearch&page=boxdetails&id='+box_id+' >' + box_id + '</a></b>'; 
		}
		


		
		
		
		//jQuery('#found_item_id').val(the_id);
		//jQuery('#title_from_id').html(title);
		
		// Update hidden fields.
		jQuery('#box_fk').val(box_fk); //
		jQuery('#folderdoc_fk').val(folderdoc_fk);
		jQuery('#record_schedule_name').html(data.Record_Schedule_Number +': '+data.Schedule_Title);
		jQuery('#record_schedule_fk').val(data.Record_Schedule_id_FK);
		jQuery('#program_office_name').html(data.office_acronym+': '+data.office_name);
		jQuery('#program_office_fk').val(data.Program_Office_id_FK);		
		

		//jQuery('#alert_status').addClass('alert_spacing');		
		
		console.log('destroyed: '+data.box_destroyed);
		console.log('frozed: '+data.freeze);		
		
		// IF box destroyed  // Cannot Recall
		if ( data.box_destroyed == 1 ) { 
			message = 'Box '+ item_link +' Destroyed. Box cannot be Recalled. ' + icon_box_destroyed;
			set_alert( 'danger', message );
			remove_all_tags();	
		}
		
		// If folder/file is frozen no update // Warning
		if ( data.freeze == 1 ) {
			message = 'Folder/File <b>'+item_link+'</b> Found. ' + icon_freeze;
			set_alert( 'success', message );
		}
		
		// If Box Status is not recallable
		if (data.error == 'Box Status Not Recallable' ) {
			message = data.type+ ': ' +item_link +' has status <b>'+data.box_status_name+'</b>. '+ data.type + ' cannot be Recalled.';
			set_alert( 'danger', message );
			remove_all_tags();
		}

		
		// If item is already recalled // Cannot Recall
		if ( data.in_recall == true ) {
			//jQuery('#found_item_id').val('');
			//jQuery('#alert_status').html('<span class=" alert alert-danger">'+data.type+': '+item_link+' is currently Recalled. Recall # <b><a href="' 				+ subfolder+'/wp-admin/admin.php?page=recalldetails&id='+'R-'+data.in_recall_where+'">'+'R-'+data.in_recall_where+'</b> </span>'); 	
			
			message = data.type+ ': ' +item_link +' is currently Recalled. Recall # <b><a href="' 
						+ subfolder+'/wp-admin/admin.php?page=recalldetails&id='+'R-'+data.in_recall_where+'">'+'R-'+data.in_recall_where+'</a></b> ';
			set_alert( 'danger', message );
			remove_all_tags();
		}
		
		// If box contains destroyed files // Warning
		if (data.error_message == 'Box Contains Destroyed Files') {
			
			var link_str = '';
			data.destroyed_files.forEach( function(file) {
				link_str += '<b><a href="'+ subfolder + '/wp-admin/admin.php?pid=boxsearch&page=filedetails&id=' + file +'">'+file+'</a></b> ' 
							+ icon_ua_destruction +', ';
			});
			link_str = link_str.slice(0, -2); 
			
			var destroyed_file_num = data.destroyed_files.length;
			
			message = data.type+': '+ item_link +' contains ' + destroyed_file_num + ' unauthorized destroyed file(s): ' + link_str;
			set_alert( 'warning', message );
		}
		
				
		// If Containing Box Status for Folder File is not recallable
		if (data.error == 'Containing Box Status Not Recallable' ) {
			
			message = data.type+ ': ' +item_link +' containing Box '+containing_box_link+' status is '+data.box_status_name+'. '+data.type+' cannot be Recalled.';
			set_alert( 'danger', message );
			remove_all_tags();
		}
		
		// If Folder/File is flagged as unauthorized destruction // Cannot Recall
		if (data.error_message == 'Folder/File Unauthorized Destruction') {
			
			message = data.type+ ': ' +item_link +' flagged as Unauthorized Destruction. '+data.type+' cannot be Recalled. '+ icon_ua_destruction;
			set_alert( 'danger', message );
			remove_all_tags();	
			
		}

		// If Folder/File is in a Recalled Box // Cannot Recall
		if (data.error_message == 'Folder/File in Recalled Box') {
			
			message = data.type+': '+ item_link +' is inside Recalled Box ' + containing_box_link + '. Recall # <b><a href="'+ subfolder 
						+'/wp-admin/admin.php?page=recalldetails&id='+'R-'+ data.in_recall_where+'">'+'R-'+ data.in_recall_where+ '</b>';
			set_alert( 'danger', message );
			remove_all_tags();	
		}
		
		// If item is in a Return (Declined) // Cannot Recall
		if (data.error == 'Item in Return') { // keep as 'Return' here since this is an error term propigating from return-search?
	
// 			message = data.error_message+' <b><a href="'+ subfolder +'/wp-admin/admin.php?page=returndetails&id='
			message = data.error_message+' <b><a href="'+ subfolder +'/wp-admin/admin.php?page=declinedetails&id='
											+ 'D-' + data.return_id+'">' + 'D-' + data.return_id + '</b>';
//											+ 'RTN-' + data.return_id+'">' + 'RTN-' + data.return_id + '</b>';											
			set_alert( 'danger', message );
			remove_all_tags();					
			
		}
		
		

		
		
		
		
		
		
		
	
	} // END function update_page
	
	
	
	
	function reset_recall_form() {
		
		remove_all_tags();
		//remove_search_tags();
		reset_all_fields();
		//location.href='admin.php?page=returncreate';
		
	}
	
	// removes all tags in input, and redraws DataTable.
	function remove_all_tags() {
		let searchIDs = jQuery('#searchByID').val().split(',');
		searchIDs.forEach( function(tag) {
			jQuery('#searchByID').removeTag(tag);
		});
		
		//dataTable.state.clear();
		dataTable.draw();
	}
	
	// Clears all user input fields. 
	function reset_all_fields() {
		jQuery('textarea[name="recall_comment"]').val('');
	}
	
	
	
	
	
	
	// Search for Doc Folder File ID
	function wppatt_search_id() {
		check_color('found_item_id');
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
							update_recall_box_folder_file(response);
						}
					} else {
						console.log("not a valid search ID");
						search_failed( 'notfound' );
					}
				}
			
			});
		}
	}
	
	// Works for old version of recall with search button
	function search_failed( failure_type ) {
		var error_str = '';
		if( failure_type == 'blank' ) {
			error_str = 'Search Field Blank';
			jQuery('input#found_item_id').attr('style', style_danger);	
			jQuery('input#wppatt_search_id_box').attr('style', style_normal);	
		} else if (failure_type == 'notfound' ) {
			error_str = 'Box/Folder/File <b>"'+search_id+'"</b> Not Found';
			jQuery('input#found_item_id').attr('style', style_danger);	
		}
		
		jQuery('#alert_status').html('<span class=" alert alert-danger">'+error_str+'</span>'); //badge badge-danger
		jQuery('#alert_status').addClass('alert_spacing');	
		
		jQuery('#bff_id').html('<b>No</b> Box/Folder/File ID'+required_html+': '); 
		
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
	
	
	// OLD sets values for submittal and displays errors for OLD click search form. Superceded by function update_page	
	function update_recall_box_folder_file(response) {
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
		
		// If Box Status is not recallable
		if (response.error == 'Box Status Not Recallable' ) {
			
			jQuery('#found_item_id').val('');
			jQuery('#alert_status').html('<span class=" alert alert-danger"> '+response.type+ ': ' +item_link +' status is '+response.box_status_name+' - Cannot be Recalled. </span>'); 
			
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
		
				
		// If Containing Box Status for Folder File is not recallable
		if (response.error == 'Containing Box Status Not Recallable' ) {
			
			jQuery('#found_item_id').val('');
			jQuery('#alert_status').html('<span class=" alert alert-danger"> '+response.type+ ': ' +item_link +' containing Box '+containing_box_link+' status is '+response.box_status_name+' - Cannot be Recalled. </span>'); 
			
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
		
		// If item is in a Return (Declined) // Cannot Recall
		if (response.error == 'Item in Return') { // Keep as 'Return' here since this is an error propogating from return_search(?)
			//console.log('inside recalled box');
			jQuery('#found_item_id').val('');
			jQuery('#alert_status').html('<span class=" alert alert-danger">'+response.error_message+' <b><a href="'+ subfolder 
// 								+'/wp-admin/admin.php?page=returndetails&id='+'D-'+ 	
								+'/wp-admin/admin.php?page=declinedetails&id='+'D-'+ 											
								response.return_id+'">'+'D-'+ response.return_id+ '</b> </span>'); 
			
		}
		
		
	}
	
	jQuery(document).ready(function () {

		if (jQuery('.wpsc_drop_down,.wpsc_checkbox,.wpsc_radio_btn,.wpsc_category,.wpsc_priority').val != '') {
			wpsc_reset_visibility();
		}

		jQuery('.wpsc_drop_down,.wpsc_checkbox,.wpsc_radio_btn,.wpsc_category,.wpsc_priority').change(function () {
			wpsc_reset_visibility();
		});
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
	function wppatt_submit_recall() {
		//alert("submitting");
		var validation = true;
		//let validation_error_item = [];
		let validation_error = [];
/*
		var style_normal = 'background-color: #ffffff !important';
		var style_danger = 'background-color: #E8C6C6 !important';
*/
		
		var new_requestors = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
		console.log('new requestors:');
		console.log(new_requestors);	
		
		//If requestors have not been added
		jQuery('input#requestor_input').attr('style', style_normal);						
		if (!Array.isArray(new_requestors) || !new_requestors.length) {
		  	console.log('you\'r in false territory ');
			validation = false;
			//validation_error_item = jQuery(this).find('input');
			validation_error.push('Requestor Username');
			jQuery('input#requestor_input').attr('style', style_danger);			
		}
		
		/*
			Required fields Validation
		*/
		jQuery('.visible.wpsc_required').each(function (e) {
			var field_type = jQuery(this).data('fieldtype');
			
			console.log('in validation');
			console.log('field_type: '+field_type);
			console.log(this);
			switch (field_type) {

				case 'text':
					//console.log('in text');
					//console.log(!jQuery(this).find('input').val() == '');
					jQuery('input#wppatt_search_id_box').attr('style', style_normal);						
					if (jQuery(this).find('input').val() == '') {
						validation = false;
						//validation_error_item.push( jQuery(this).find('input'));
						validation_error.push( 'Search by ID');
						jQuery('input#wppatt_search_id_box').attr('style', style_danger);						
					}
					break;
				case 'textarea':
					//console.log('in textarea');
					//console.log(!jQuery(this).find('textarea').val() == '');
					jQuery('textarea[name="recall_comment"]').attr('style', style_normal);
					if (jQuery(this).find('textarea').val() == '') {
						validation = false;
						//validation_error_item.push( jQuery(this).find('textarea'));
						validation_error.push('Comment');
						jQuery('textarea[name="recall_comment"]').attr('style', style_danger);
					}
					break;
				case 'email':
					//console.log('in email');
					//console.log(!jQuery(this).find('input').val() == '');
/*
					jQuery('input#wppatt_search_id_box').attr('style', style_normal);					
					if (jQuery(this).find('input').val() == '') {
						validation = false;
						validation_error_item.push( jQuery(this).find('input'));
						validation_error.push( 'Email');
					}
*/
					break;
				case 'number':
				case 'date':
				case 'search-results':
					//console.log('in search-results');
					//console.log(!jQuery(this).find('input').val() == '');
					jQuery('input#found_item_id').attr('style', style_normal);					
					if (jQuery(this).find('input').val() == '') {
						validation = false;
						//validation_error_item.push( jQuery(this).find('input'));
						let error = jQuery('#bff_id').html();
						error = error.replace('<span style="color:red;">*</span>: ', '');
						error = error.replace('<b>', '');
						error = error.replace('</b>', '');						
						validation_error.push( error );
						jQuery('input#found_item_id').attr('style', style_danger);
					}
					break;

				case 'textarea':
/*
					jQuery('input#wppatt_search_id_box').attr('style', style_normal);				
					if (jQuery(this).find('textarea').val() == '') {
						validation = false;
						validation_error_item.push( jQuery(this).find('input'));
						validation_error.push( 'Comment 2');
					}
					break;
*/
			}

			if (!validation) return;
		});
		
		// If Not Valid
		if (!validation) {
			
// 			alert("<?php _e('Required fields cannot be empty.','supportcandy')?>");
			let listed_error = '';
			const num_of_errors = validation_error.length;
			let i = 0;
			validation_error.forEach(function(x) {
				i++;
				console.log(validation_error.length);
				if( i == num_of_errors ) {
					if( num_of_errors == 1 ) {
						listed_error += x;
					} else {
						listed_error = listed_error.slice(0, -2); 
						listed_error += ' & ' + x ;
					}
				} else {
					listed_error += x + ', ';
				}
				
			});
//			listed_error = listed_error.slice(0, -2); 
			if( num_of_errors == 1 ) {
				listed_error += ' is empty.';
			} else {
				listed_error += ' are empty.';				
			}

			alert('Required fields cannot be empty. '+listed_error);
			//console.log('Validation Error: '+listed_error);
			//console.log(validation_error_item);
			
			jQuery('#alert_status').html('<span class="alert alert-danger">Required fields cannot be empty. '+listed_error+'</span>');
			jQuery('#alert_status').addClass('alert_spacing');
			
			return false;
		}
		
		<?php do_action('wpsc_create_ticket_validation'); ?>

		
		if (validation) {

			var dataform = new FormData(jQuery('#wppatt_frm_create_recall')[0]);
			
			var myobj_array = [];
			var assigned_agent_id_array = [];
			
			// Display the key/value pairs
			for(var pair of dataform.entries()) {
			   console.log(pair[0]+ ', '+ pair[1]); 
			   myobj_array[pair[0]] = pair[1];
			   
			   if( pair[0] == 'assigned_agent[]' ) {
				   assigned_agent_id_array.push(pair[1]);
				   console.log('adding assigned_agent: '+pair[1]);
			   }
			}
			
			console.log('assigned_agent array: ');
			console.log(assigned_agent_id_array);
		
			console.log("dataform entries:");			
			console.log(dataform);
			
			console.log("new array: ");
			console.log(myobj_array);

			console.log('customer name: ');
			console.log( myobj_array['customer_name'] );
			
			console.log('record_schedule : ');
			console.log( myobj_array['record_schedule'] );
			
			console.log('program_office: ');
			console.log( myobj_array['program_office'] );
			
			console.log('box_fk : ');
			console.log( myobj_array['box_fk'] );
			
			console.log('folderdoc_fk: ');
			console.log( myobj_array['folderdoc_fk'] );
				
			var data = {
			    action: 'wppatt_recall_submit',
			    title: '',
			    customer_name: myobj_array['customer_name'],
			    customer_email: myobj_array['customer_email'],
			    recall_comment: myobj_array['recall_comment'],
			    item_id: myobj_array['item_id'],
			    record_schedule: myobj_array['record_schedule'],	
			    program_office: myobj_array['program_office'],	
			    box_fk: myobj_array['box_fk'],
			    folderdoc_fk: myobj_array['folderdoc_fk'],	    
			    assigned_agent_ids : assigned_agent_id_array,		    
			};
			
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			    console.log("Submit Post reponse_str: ");
				console.log(response_str);
			    
			    var response = JSON.parse(response_str);
			    console.log(response.customer_name);
			    console.log(response.title);	
			    console.log(response.date);
			    console.log('recall id: '+response.recall_id);	  
			    console.log('error: '+response.error);  
			    
			    //window.location.reload();
			    if( response.recall_id == 0 ) {
				    var failed_str = "Recall Failed. Please ensure this item is recallable. " +response.recall_id;
				    alert(failed_str);
				    location.href='admin.php?page=recallcreate&success=false';
				    

			    } else {
					var success_str = "Recall "+response.recall_id+" successfully created.";   
					alert(success_str);
					location.href='admin.php?page=recallcreate&success=true&id='+response.recall_id; 
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
		//jQuery('#alert_status').html('<span class="alert alert-success">Recall <b><a href="'+subfolder+'/wp-admin/admin.php?page=recalldetails&id='+allVars['id']+'">'+allVars['id']+'</a></b> Created</span>');
		//jQuery('#alert_status').addClass('alert_spacing');
		
		let message = 'Recall <b><a href="' + subfolder + '/wp-admin/admin.php?page=recalldetails&id=' + allVars['id'] + '">' + allVars['id'] + '</a></b> Created';
		set_alert( 'success', message );
		
	} else if ( submit_success == 'false' ) {
		jQuery('#alert_status').html('<span class="alert alert-danger"><b>ERROR</b> - Recall Not Created</span>');
		jQuery('#alert_status').addClass('alert_spacing');
	}
	
	
	

// 	<?php do_action('wpsc_print_ext_js_create_ticket'); ?>
</script>


<?php if (!$wpsc_recaptcha_type && $wpsc_captcha): ?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<?php endif; ?>
<?php
endif;
?>









	 
	 

	 
	 