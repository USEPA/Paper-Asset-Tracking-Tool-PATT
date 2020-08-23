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


<div id="create_ticket_body" class="row"
	style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">
<div id='alert_status' class=''></div> 

	<form id="wppatt_frm_create_recall" onsubmit="return wppatt_submit_recall();" method="post">
<!-- 		<form id="wppatt_frm_create_recall" onsubmit="wppatt_submit_recall();" method="post"> -->
<!--
		<div class="row create_ticket_fields_container">
			<?php 
			foreach ($fields as $field) {

				//do_action('print_recall_form_block', $field);

				$form_field->print_field($field);
			}
			?>
		</div>
-->
		
		
		
		
		
		
		
		<div class="row create_ticket_fields_container">
<!-- 			<div  data-fieldtype="text" data-visibility="" class="col-sm-6 visible wpsc_required form-group wpsc_form_field field_222"> .visible.wpsc_required -->
			<div  data-fieldtype="text" data-visibility="" class="col-sm-6 form-group wpsc_form_field field_222"> 
				<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
					Requestor Username <?php echo $required_html?>
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
		
		
		<script>
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
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		<div class="row create_ticket_fields_container">
			<div  data-fieldtype="text" data-visibility="" class="col-sm-6 <?php echo $form_field->visibility? 'hidden':'visible'?> <?php echo $form_field->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
				<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
					Search By ID <?php echo $required_html ?>
				</label>
				<input id="wppatt_search_id_box" type="search" class="form-control" onkeypress="check_color(this.id)" />
				<button type="button" id="wppatt_search_id_button" onclick="wppatt_search_id();" class="btn"
				style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_border_color']?> !important;"> Search</button>
				
				
			</div>
		
		
			<div  data-fieldtype="textarea" data-visibility="" class="col-sm-6 <?php echo $form_field->visibility? 'hidden':'visible'?> <?php echo $form_field->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
				<label class="wpsc_ct_field_label" for="<?php echo $form_field->slug;?>">
					Comment <?php echo $required_html ?>
				</label>
				
				<textarea name="recall_comment" rows="3" cols="30" class="form-control " style="height: auto !important;" onkeypress="check_color(this.id)" ></textarea>
				 
				
				
			</div>
		
		</div>
		
		<div class="row create_ticket_fields_container">
<!-- 			<div  data-fieldtype="text" data-visibility="" class="<?php echo $form_field->col_class?> <?php echo $form_field->visibility? 'hidden':'visible'?> <?php echo $form_field->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>"> -->
			<div data-fieldtype="search-results" data-visibility="" class="wpsc_required visible">
				<div id="search_results">				<!-- <div id="search_details"> -->
					<div id="search_status"></div>
<!-- 					<label class="wpsc_ct_field_label">Box/Folder/File ID: </label><span id="found_item_id" class="wpsc_required visible" ></span><br> -->
					<div id="search_details">
						<label id = "bff_id" class="wpsc_ct_field_label ">Box/Folder/File ID<?php echo $required_html ?>: </label>
<!-- 						<label id = "bff_id" class="wpsc_ct_field_label ">Box/Folder/File ID</label><?php echo $required_html ?>:  -->
							<input id="found_item_id" class=" " name="item_id" readonly /><br>
						<label class="wpsc_ct_field_label">Title: </label><span id="title_from_id"></span><br>
						<label class="wpsc_ct_field_label">Record Schedule: </label><span id="record_schedule_name"></span><br>
						<label class="wpsc_ct_field_label">Program Office: </label><span id="program_office_name"></span><br>
					</div>
				</div>
			</div>
		</div>
		
		
		
		
		
		
		
		
		

		<?php if($wpsc_captcha) {
			if($wpsc_recaptcha_type){?>
		<div class="row create_ticket_fields_container">
			<div class="col-md-6 captcha_container"
				style="margin-bottom:10px;margin-right:15px; display:flex; background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_text_color']?> !important;">
				<div style="width:25px;">
					<input type="checkbox" onchange="get_captcha_code(this);" class="wpsc_checkbox" value="1">
					<img id="captcha_wait" style="width:16px;display:none;"
						src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" alt="">
				</div>
				<div style="padding-top:3px;"><?php _e("I'm not a robot",'supportcandy')?></div>
			</div>
		</div>
		<?php  
			}
			else {
				?>
		<div class="row create_ticket_fields_container">
			<div class="col-sm-12" style="margin-bottom:10px;margin-right:15px; display:flex">
				<div style="width:25px;">
					<div class="g-recaptcha" data-sitekey=<?php echo $wpsc_get_site_key ?>></div>
				</div>
			</div>
		</div>
		<?php  
			}
		}
		?>

		<?php if($wpsc_set_in_gdpr) {?>
		<div class="row create_ticket_fields_container">
			<div class="col-sm-12" style="margin-bottom:10px; display:flex;">
				<div style="width:25px;">
					<input type="checkbox" name="wpsc_gdpr" id="wpsc_gdpr" value="1">
				</div>
				<div style="padding-top:3px;">
					<?php echo stripcslashes(html_entity_decode($wpsc_gdpr_html))?>
				</div>
			</div>
		</div>
		<?php  
		   }
			?>

		<?php 
		if($wpsc_terms_and_conditions) {?>

		<div class="row create_ticket_fields_container">
			<div class="col-sm-12" style="margin-bottom:10px; display:flex;">
				<div style="width:25px;">
					<input type="checkbox" name="terms" id="terms" value="1">
				</div>
				<div style="padding-top:3px;">
					<?php 
						echo stripcslashes(html_entity_decode($wpsc_terms_and_conditions_html));						
						 ?>
				</div>
			</div>
		</div>
		<?php  
		  }
		?>

		<?php
		$wpsc_notify = get_option('wpsc_do_not_notify_setting');
		$wpsc_notify_checkbox = get_option('wpsc_default_do_not_notify_option');
		if($current_user->has_cap('wpsc_agent') && $wpsc_notify) {?>

<!--
		<div class="row create_ticket_fields_container">
			<div class="col-sm-6" style="margin-bottom:10px; display:flex;">
				<div style="width:25px;">
					<?php $checked = ($wpsc_notify_checkbox == 1) ? 'checked="checked"' : '';?>
					<input <?php echo $checked ?> type="checkbox" name="notify_owner" id="notify_owner" value="1">
				</div>
				<div class="wpsc_notify_owner" style="padding-top:3px;">
					<?php echo __("Don't notify owner",'supportcandy'); ?>
				</div>
			</div>
		</div>
-->
		<?php  
		  }
		?>

		<div class="row create_ticket_frm_submit">
			<button type="submit" id="wpsc_create_recall_submit" class="btn"
				style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_border_color']?> !important;"> Submit Recall</button>
			<button type="button" id="wpsc_create_ticket_reset" onclick="location.href='admin.php?page=recallcreate';" class="btn"
				style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_border_color']?> !important;"><?php _e('Reset Form','supportcandy')?></button>
			<?php do_action('wpsc_after_create_ticket_frm_btn');?>
		</div>

		<input type="file" id="attachment_upload" class="hidden" onchange="">
		<input type="hidden" id="wpsc_nonce" value="<?php echo wp_create_nonce()?>">

		<input type="hidden" name="action" value="wppatt_recall_submit">
		<input type="hidden" name="setting_action" value="submit_recall">
		
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
	margin: 25px 0px 5px 35px;
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
			    title: 'this is real',
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
		jQuery('#alert_status').html('<span class="alert alert-success">Recall <b><a href="'+subfolder+'/wp-admin/admin.php?page=recalldetails&id='+allVars['id']+'">'+allVars['id']+'</a></b> Created</span>');
		jQuery('#alert_status').addClass('alert_spacing');
	} else if ( submit_success == 'false' ) {
		jQuery('#alert_status').html('<span class="alert alert-danger"><b>ERROR</b> - Recall Not Created Created</span>');
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









	 
	 

	 
	 