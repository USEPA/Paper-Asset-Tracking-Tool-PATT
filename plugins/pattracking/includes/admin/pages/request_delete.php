<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$GLOBALS['id'] = $_GET['id'];

$agent_permissions = $wpscfunction->get_current_agent_permissions();

//include_once WPPATT_ABSPATH . 'includes/class-wppatt-functions.php';
//$load_styles = new wppatt_Functions();
//$load_styles->addStyles();

$general_appearance = get_option('wpsc_appearance_general_settings');

$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';
$create_ticket_btn_css = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
?>

<style>
.bootstrap-iso label {
    margin-top: 5px;
}
.datatable_header {
background-color: rgb(66, 73, 73) !important; 
color: rgb(255, 255, 255) !important; 
}

.bootstrap-tagsinput {
   width: 100%;
  }

#searchGeneric {
    padding: 0 30px !important;
}

.remove-user {
    padding-left:5px;
}
</style>

<div class="bootstrap-iso">
  
  <h3>Recycle Bin</h3>
  
 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
  <div class="col-sm-12">
         <button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=wpsc-tickets';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
		 <button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-retweet"></i> <?php _e('Reset Filters','supportcandy')?></button>
         <button type="button" class="btn btn-sm wpsc_btn_bulk_action wpsc_action_btn" id="btn_restore_tickets" style="<?php echo $action_default_btn_css?>"><i class="fa fa-window-restore"></i> <?php _e('Restore Ticket','supportcandy')?></button>
<?php
if (($agent_permissions['label'] == 'Administrator'))
{
?>
         <button type="button" class="btn btn-sm wpsc_btn_bulk_action wpsc_action_btn" id="btn_delete_permanently_bulk_ticket" style="<?php echo $action_default_btn_css?>"><i class="fa fa-trash"></i> <?php _e('Delete Tickets Permanently','supportcandy')?></button>
<?php
}
?>	
  </div>

</div>

<div class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">

	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">

							<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					      <h4 class="widget_header"><i class="fa fa-filter"></i> Filters
								</h4>
								<hr class="widget_divider">

	                            <div class="wpsp_sidebar_labels">
Enter one or more Request IDs:<br />
         <input type='text' id='searchByRequestID' class="form-control" data-role="tagsinput">
<br />
        <select id='searchByStatus'>
           <option value=''>-- Select Status --</option>
			<option value="3">New</option>
			<option value="4">Initial Review Complete</option>
			<option value="670">Initial Review Rejected</option>
			<option value="5">Shipped</option>
			<option value="63">Received</option>
			<option value="69">Cancelled</option>
         </select>
<br /><br />
        <select id='searchByPriority'>
           <option value=''>-- Select Priority --</option>
			<option value="621">Not Assigned</option>
			<option value="7">Normal</option>
			<option value="8">High</option>
			<option value="9">Critical</option>
         </select>
<br /><br />
        <select id='searchByDigitizationCenter'>
           <option value=''>-- Select Digitization Center --</option>
           <option value='East'>East</option>
           <option value='East CUI'>East CUI</option>
           <option value='West'>West</option>
           <option value='West CUI'>West CUI</option>
           <option value='Not Assigned'>Not Assigned</option>
         </select>
<br /><br />
				<select id='searchByUser'>
					<option value=''>-- Select User --</option>
					<option value='mine'>Mine</option>
					<option value='not assigned'>All Requests</option>
					<option value='search for user'>Search for User</option>
				</select>

	<br /><br />				
				<form id="frm_get_ticket_assign_agent">
					<div id="assigned_agent">
						<div class="form-group wpsc_display_assign_agent ">
						    <input class="form-control  wpsc_assign_agents ui-autocomplete-input " id="assigned_agent" name="assigned_agent"  type="text" autocomplete="off" placeholder="<?php _e('Search agent ...','supportcandy')?>" />
							<ui class="wpsp_filter_display_container"></ui>
						</div>
					</div>
					<div id="assigned_agents" class="form-group col-md-12 ">
						<?php
						    if($is_single_item) {
							    foreach ( $assigned_agents as $agent ) {
									$agent_name = get_term_meta( $agent, 'label', true);
									 	
										if($agent && $agent_name):
						?>
												<div class="form-group wpsp_filter_display_element wpsc_assign_agents ">
													<div class="flex-container searched-user" style="padding:5px;font-size:1.0em;">
														<?php echo htmlentities($agent_name)?><span class="remove-user"><i class="fa fa-times"></i></span>
														  <input type="hidden" name="assigned_agent[]" value="<?php echo htmlentities($agent) ?>" />
					<!-- 									  <input type="hidden" name="new_requestor" value="<?php echo htmlentities($agent) ?>" /> -->
													</div>
												</div>
						<?php
										endif;
								}
							}
						?>
				  </div>
						<input type="hidden" name="action" value="wpsc_tickets" />
						<input type="hidden" name="setting_action" value="set_change_assign_agent" />
						<input type="hidden" id="current_user" name="current_user" value="<?php wp_get_current_user(); echo $current_user->nickname; ?>">
                        <input type="hidden" id="user_search" name="user_search" value="">
				</form>	
<input type="hidden" id="current_user" name="current_user" value="<?php wp_get_current_user(); echo $current_user->nickname; ?>">

	                            </div>
			    		</div>
	
	</div>


	
  <div class="col-sm-8 col-md-9 wpsc_it_body">

<style>
.datatable_header {
background-color: rgb(66, 73, 73) !important; 
color: rgb(255, 255, 255) !important; 
}

.bootstrap-tagsinput {
   width: 100%;
  }

#searchGeneric {
    padding: 0 30px !important;
}
</style>

<div class="table-responsive" style="overflow-x:auto;">
<input type="text" id="searchGeneric" class="form-control" name="custom_filter[s]" value="" autocomplete="off" placeholder="Search...">
<i class="fa fa-search wpsc_search_btn wpsc_search_btn_sarch"></i>
<br /><br />
<table id="tbl_templates_requests_delete" class="table table-striped table-bordered" cellspacing="5" cellpadding="5" width="100%">
        <thead>
            <tr>
                <th class="datatable_header"></th>
                <th class="datatable_header">Request ID</th>
                <th class="datatable_header">Status</th>
                <th class="datatable_header">Name</th></th>
                <th class="datatable_header">Location</th>
                <th class="datatable_header">Priority</th>
                <th class="datatable_header">Last Updated</th>
            </tr>
        </thead>
    </table>
<br /><br />
<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.css" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.js" crossorigin="anonymous"></script>

<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>

 
<script>

jQuery(document).ready(function(){

  var dataTable = jQuery('#tbl_templates_requests_delete').DataTable({
    'autoWidth': false,
    'processing': true,
    'serverSide': true,
    'stateSave': true,
    'stateSaveParams': function(settings, data) {
      data.ss = jQuery('#searchByStatus').val();
      data.sp = jQuery('#searchByPriority').val();
      data.sg = jQuery('#searchGeneric').val();
      data.rid = jQuery('#searchByRequestID').val();
      data.po = jQuery('#searchByProgramOffice').val();
      data.dc = jQuery('#searchByDigitizationCenter').val();
	  data.aaVal = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();     
	  data.aaName = jQuery(".searched-user").map(function(){return jQuery(this).text();}).get(); 
    },
    'stateLoadParams': function(settings, data) {
      jQuery('#searchByStatus').val(data.ss);
      jQuery('#searchByPriority').val(data.sp);
      jQuery('#searchGeneric').val(data.sg);
      jQuery('#searchByRequestID').val(data.rid);
      jQuery('#searchByProgramOffice').val(data.po);
      jQuery('#searchByDigitizationCenter').val(data.dc);
      jQuery('#searchByUser').val(data.sbu);
			jQuery('#user_search').val(data.aaName);

			data.aaVal.forEach( function(val, key) {
				let html_str = get_display_user_html(data.aaName[key], val); 
				jQuery('#assigned_agents').append(html_str);
			});
    },
    'serverMethod': 'post',
    'searching': false, // Remove default Search Control
    'ajax': {
       'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/request_delete_processing.php',
       'data': function(data){
          // Read values
		  var sbu = jQuery('#searchByUser').val();  
		  var aaVal = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();     
		  var aaName = jQuery("#user_search").val();	 
          var rs_user = jQuery('#current_user').val();
          var ss = jQuery('#searchByStatus').val();
          var sp = jQuery('#searchByPriority').val();
          var sg = jQuery('#searchGeneric').val();
          var requestid = jQuery('#searchByRequestID').val();
          var dc = jQuery('#searchByDigitizationCenter').val();
          // Append to data
          data.searchGeneric = sg;
          data.searchByRequestID = requestid;
          data.searchByStatus = ss;
          data.searchByPriority = sp;
          data.searchByDigitizationCenter = dc;
          data.currentUser = rs_user;
          data.searchByUser = sbu;
		  data.searchByUserAAVal = aaVal;
		  data.searchByUserAAName = aaName;
       }
    },
    'lengthMenu': [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
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
    'columns': [
       { data: 'request_id' }, 
       { data: 'request_id_flag' },
       { data: 'ticket_status' },
       { data: 'customer_name' },
       { data: 'location' },
       { data: 'ticket_priority' },
       { data: 'date_updated' },
    ]
  });
    
  jQuery( window ).unload(function() {
  dataTable.column(0).checkboxes.deselectAll();
});

  jQuery(document).on('keypress',function(e) {
    if(e.which == 13) {
        dataTable.state.save();
        dataTable.draw();
    }
});

	jQuery("#searchByUser").change(function(){
		dataTable.state.save();
		dataTable.draw();
	});

jQuery("#searchByStatus").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

  jQuery("#searchByPriority").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

  jQuery("#searchByDigitizationCenter").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

jQuery('#searchGeneric').on('input keyup paste', function () {
            dataTable.state.save();
            dataTable.draw();
});


function onAddTag(tag) {
    dataTable.state.save();
    dataTable.draw();

    var target = jQuery("#searchByRequestID");
    var tags = (tag).match(/id=(\d+)/);

    if (tags != null) {
        if (!target.tagExist(tags[1])) {
            target.addTag(tags[1]);
            target.removeTag(tag);

        }
    }
}

function onRemoveTag(tag) {
    dataTable.state.save();
    dataTable.draw();
}

jQuery("#searchByRequestID").tagsInput({
   'defaultText':'',
   'onAddTag': onAddTag,
   'onRemoveTag': onRemoveTag,
   'width':'100%'
});

jQuery("#searchByRequestID_tag").on('paste',function(e){
    var element=this;
    setTimeout(function () {
        var text = jQuery(element).val();
        var target=jQuery("#searchByRequestID");
        var tags = (text).split(/[ ,]+/);
        for (var i = 0, z = tags.length; i<z; i++) {
              var tag = jQuery.trim(tags[i]);
              if (!target.tagExist(tag)) {
                    target.addTag(tag);
              }
              else
              {
                  jQuery("#searchByRequestID_tag").val('');
              }
                
         }
    }, 0);
});


jQuery('#wpsc_individual_refresh_btn').on('click', function(e){
    jQuery('#searchByUser').val('');
    jQuery('#searchByStatus').val('');
    jQuery('#searchByPriority').val('');
    jQuery('#searchGeneric').val('');
    jQuery('#searchByProgramOffice').val('');
    jQuery('#searchByDigitizationCenter').val('');
    jQuery('#searchByBoxID').importTags('');
    dataTable.column(0).checkboxes.deselectAll();
	dataTable.state.clear();
	dataTable.destroy();
	location.reload();
});

function wpsc_get_restore_bulk_ticket_1(){
  wpsc_modal_open('Restore Request');
   var form = this;
      var rows_selected = dataTable.column(0).checkboxes.selected();

        var ticket_id=String(rows_selected.join(","));

      var data = {
          action: 'wpsc_tickets',
          setting_action : 'get_bulk_restore_ticket',
          ticket_id: ticket_id
      }

      jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
       var response = JSON.parse(response_str);
       jQuery('#wpsc_popup_body').html(response.body);
       jQuery('#wpsc_popup_footer').html(response.footer);
     });
  }
  
function wpsc_get_delete_permanently_bulk_ticket_1(){
  wpsc_modal_open('Delete Request Permanently');
  var form = this;
      var rows_selected = dataTable.column(0).checkboxes.selected();

        var ticket_id=String(rows_selected.join(","));

      var data = {
          action: 'wpsc_tickets',
          setting_action : 'get_delete_permanently_bulk_ticket',
          ticket_id: ticket_id
      }

      jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
       var response = JSON.parse(response_str);
       jQuery('#wpsc_popup_body').html(response.body);
       jQuery('#wpsc_popup_footer').html(response.footer);
     });
  }

   jQuery('#btn_restore_tickets').on('click', function(e){
wpsc_get_restore_bulk_ticket_1();
  
      });
   jQuery('#btn_delete_permanently_bulk_ticket').on('click', function(e){
wpsc_get_delete_permanently_bulk_ticket_1();
  
      });
      
   jQuery('#btn_delete_tickets').on('click', function(e){
    wpsc_get_delete_bulk_ticket_1();
      });

	// User Search
	jQuery('#frm_get_ticket_assign_agent').hide();
	
	jQuery('#searchByUser').change( function() {
		if(jQuery(this).val() == 'search for user') {
			jQuery('#frm_get_ticket_assign_agent').show();
		} else {
			jQuery('#frm_get_ticket_assign_agent').hide();
		}
	});
	
	// Show search box on page load - from save state
	if( jQuery('#searchByUser').val() == 'search for user' ) {
		jQuery('#frm_get_ticket_assign_agent').show();
	}

	// Autocomplete for user search
	jQuery( ".wpsc_assign_agents" ).autocomplete({
		minLength: 0,
		appendTo: jQuery('.wpsc_assign_agents').parent(),
		source: function( request, response ) {
			var term = request.term;
			console.log('term: ');
			console.log(term);
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
			console.log('label: '+ui.item.label+' flag_val: '+ui.item.flag_val); 							
			html_str = get_display_user_html(ui.item.label, ui.item.flag_val);
// 			jQuery('#assigned_agents').append(html_str);	
			
			// when adding new item, event listener functon must be added. 
			jQuery('#assigned_agents').append(html_str).on('click','.remove-user',function(){	
				console.log('This click worked.');
				wpsc_remove_filter(this);
				jQuery('#user_search').val(jQuery(".searched-user").map(function(){return jQuery(this).text();}).get());
				dataTable.state.save();
				dataTable.draw();
			});
		    jQuery('#user_search').val(jQuery(".searched-user").map(function(){return jQuery(this).text();}).get());
			dataTable.state.save();
			dataTable.draw();
			// ADD CODE to go through every status and make sure that there is at least one name per, and if so, show SAVE.
			
			jQuery("#button_agent_submit").show();
		    jQuery(this).val(''); return false;
		}
	}).focus(function() {
			jQuery(this).autocomplete("search", "");
	});
	
	


	jQuery('.searched-user').on('click','.remove-user', function(e){
		console.log('Removed a user 1');
		wpsc_remove_filter(this);
		jQuery('#user_search').val(jQuery(".searched-user").map(function(){return jQuery(this).text();}).get());
		dataTable.state.save();
		dataTable.draw();
	});
	
// Code block for toggling edit buttons on/off when checkboxes are set
	jQuery('#tbl_templates_requests_delete tbody').on('click', 'input', function () {        
	// 	console.log('checked');
		setTimeout(toggle_button_display, 1); //delay otherwise 
	});
	
	jQuery('.dt-checkboxes-select-all').on('click', 'input', function () {        
	 	console.log('checked');
		setTimeout(toggle_button_display, 1); //delay otherwise 
	});
	
	jQuery('#btn_restore_tickets').attr('disabled', 'disabled');
	jQuery('#btn_delete_permanently_bulk_ticket').attr('disabled', 'disabled');
	
	function toggle_button_display() {
	//	var form = this;
		var rows_selected = dataTable.column(0).checkboxes.selected();
		if(rows_selected.count() > 0) {
			jQuery('#btn_restore_tickets').removeAttr('disabled');
			jQuery('#btn_delete_permanently_bulk_ticket').removeAttr('disabled');
	  	} else {
	    	jQuery('#btn_restore_tickets').attr('disabled', 'disabled');    	
	    	jQuery('#btn_delete_permanently_bulk_ticket').attr('disabled', 'disabled'); 
	  	}
	}
});

function get_display_user_html(user_name, termmeta_user_val) {
	//console.log("in display_user");
// 	var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
	var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
	
	if( requestor_list.indexOf(termmeta_user_val.toString()) >= 0 ) {
		console.log('termmeta_user_val: '+termmeta_user_val+' is already listed');
		html_str = '';
	} else {

		var html_str = '<div class="form-group wpsp_filter_display_element wpsc_assign_agents ">'
						+'<div class="flex-container searched-user" style="padding:5px;font-size:1.0em;">'
							+user_name
							+'<span  class="remove-user" ><i class="fa fa-times"></i></span>'
						+'<input type="hidden" name="assigned_agent[]" value="'+termmeta_user_val+'" />'
						+'</div>'
					+'</div>';	

	}
			
	return html_str;		

}


function wpsc_remove_filterX(x) {
	setTimeout(wpsc_remove_filter(x), 10);
}


function remove_user() {
	//if zero users remove save
	//if more than 1 user show save
	var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
	let is_single_item = <?php echo json_encode($is_single_item); ?>;
	//console.log('Remove user');
	console.log(requestor_list);
	console.log('length: '+requestor_list.length);
	console.log('single item? '+is_single_item);
	
	if( is_single_item ) {
		console.log('doing single item stuff');
		if( requestor_list.length > 0 ) {
			jQuery("#button_agent_submit").show();
		} else {
			jQuery("#button_agent_submit").hide();
		}
	}
}

	 jQuery('#toplevel_page_wpsc-tickets').removeClass('wp-not-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-has-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-menu-open'); 
	 jQuery('#toplevel_page_wpsc-tickets a:first').removeClass('wp-not-current-submenu');
	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-has-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-menu-open');
	 jQuery('#menu-dashboard').removeClass('current');
	 jQuery('#menu-dashboard a:first').removeClass('current');
	 jQuery('.wp-submenu li:nth-child(2)').addClass('current');
</script>


  </div>
 


</div>
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
<?php
}
?>