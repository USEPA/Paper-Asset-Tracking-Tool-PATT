<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$blog_id = get_current_blog_id();

$general_appearance = get_option('wpsc_appearance_general_settings');

$create_ticket_btn_css       = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';
$action_default_btn_css      = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';
$logout_btn_css              = 'background-color:'.$general_appearance['wpsc_sign_out_bg_color'].' !important;color:'.$general_appearance['wpsc_sign_out_text_color'].' !important;';
$wpsc_show_and_hide_filters  = get_option('wpsc_show_and_hide_filters');
$wpsc_appearance_ticket_list = get_option('wpsc_appearance_ticket_list');

$wpsc_on_and_off_auto_refresh = get_option('wpsc_on_and_off_auto_refresh');
$agent_permissions = $wpscfunction->get_current_agent_permissions();
//include WPSC_ABSPATH.'includes/admin/tickets/ticket_list/filters/get_label_count.php';

include WPPATT_ABSPATH.'/includes/admin/pages/scripts/request_cleanup.php';

?>
<style>

a:link, a:visited {
  color:#107799;
}

div.dataTables_wrapper {
        width: 100%;
        margin: 0;
    }

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

.update-plugins {
    display: inline-block;
    vertical-align: top;
    box-sizing: border-box;
    margin: 1px 0 -1px 2px;
    padding: 0 5px;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    background-color: #ca4a1f;
    color: #fff;
    font-size: 11px;
    line-height: 1.6;
    text-align: center;
    z-index: 26;
}
.remove-user {
    padding-left:5px;
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

.large-tooltip .tooltip-inner {
    max-width: 325px !important;
}

</style>
<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  <div class="col-sm-12">
  	        <button type="button" id="wpsc_load_new_create_ticket_btn" onclick="wpsc_get_create_ticket();" class="btn btn-sm wpsc_create_ticket_btn" style="<?php echo $create_ticket_btn_css?>"><i class="fa fa-plus"></i> <?php _e('New Ticket','supportcandy')?></button>
        <button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=wpsc-tickets';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?> <a href="#" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-request-list-button'); ?>" aria-label="Request Help"><i class="far fa-question-circle"></i></a></button>
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" style="<?php echo $action_default_btn_css?> margin-right: 30px !important;"><i class="fas fa-retweet"></i> <?php _e('Reset Filters','supportcandy')?></button>
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
<button type="button" class="btn btn-sm wpsc_btn_bulk_action wpsc_action_btn checkbox_depend" id="btn_delete_tickets" style="<?php echo $action_default_btn_css?>"><i class="fa fa-trash"></i> <?php _e('Delete Requests','supportcandy')?></button>
<?php
}
?>		
  </div>
</div>

<div class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">

	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">

							<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					      <h4 class="widget_header"><i class="fa fa-filter"></i> Filters <a href="#" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-filters'); ?>" aria-label="Help Filters"><i class="far fa-question-circle"></i></a>
								</h4>
								<hr class="widget_divider">

	                            <div class="wpsp_sidebar_labels">
Enter one or more Request IDs:<br />
         <input type='text' id='searchByRequestID' class="form-control" data-role="tagsinput" aria-label="Search by Request ID">
<br />
        <select id='searchByStatus' aria-label="Search by Status">
           <option value=''>-- Select Status --</option>
			<option value="3">New</option>
			<option value="4">Initial Review Complete</option>
			<option value="670">Initial Review Rejected</option>
			<option value="5">Shipped</option>
			<option value="63">Received</option>
			<option value="69">Cancelled</option>
         </select>
<br /><br />
        <select id='searchByPriority' aria-label="Search by Priority">
           <option value=''>-- Select Priority --</option>
			<option value="621">Not Assigned</option>
			<option value="7">Normal</option>
			<option value="8">High</option>
			<option value="9">Critical</option>
         </select>
<br /><br />
        <select id='searchByDigitizationCenter' aria-label="Search by Digitization Center">
           <option value=''>-- Select Digitization Center --</option>
           <option value='East'>East</option>
           <option value='East CUI'>East CUI</option>
           <option value='West'>West</option>
           <option value='West CUI'>West CUI</option>
           <option value='Not Assigned'>Not Assigned</option>
         </select>
<br /><br />

<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
				<select id='searchByUser' aria-label="Search by User">
					<option value=''>-- Select User --</option>
					<option value='mine'>Mine</option>
					<option value='not assigned'>All Requests</option>
					<option value='search for user'>Search for User</option>
				</select>

	<br /><br />				
				<form id="frm_get_ticket_assign_agent">
					<div id="assigned_agent">
						<div class="form-group wpsc_display_assign_agent ">
						    <input class="form-control  wpsc_assign_agents ui-autocomplete-input "  aria-label="Search digitization staff" name="assigned_agent"  type="text" autocomplete="off" placeholder="<?php _e('Search agent ...','supportcandy')?>" />
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
<!-- 													<div class="flex-container searched-user " style="padding:5px;font-size:1.0em;">  -->
													<div class="flex-container searched-user staff-badge" style=""> 
														<?php echo htmlentities($agent_name); ?><span class="remove-user staff-close"><i class="fa fa-times"></i></span>
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
				</form>	

<br /><?php		

$get_pending_delete_count = $wpdb->get_row(
"SELECT count(id) as count
FROM wpqa_wpsc_ticket
WHERE active = 0 AND id <> -99999"
			);

$pending_delete_count = $get_pending_delete_count->count;

?>
<h4 class="widget_header"><i class="far fa-trash-alt"></i> <a href="admin.php?page=request_delete">Recycle Bin</a> <?php if ($pending_delete_count > 0) { ?><span class="update-plugins count-<?php echo $pending_delete_count ?>"><span class="update-count"><?php echo $pending_delete_count ?></span></span><?php }?></span>  
<div class="large-tooltip" style="display:inline; padding-left:5px; width: 325px; position: absolute;"><a href="#" id="recycletooltip" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-recycle-bin'); ?>" aria-label="Recycle Help"><i class="far fa-question-circle"></i></a></div>

<hr class="widget_divider">
<?php		
} else {
?>
<input type="hidden" id="searchByUser" name="searchByUser" value="mine">
<?php		
}
?>	
<input type="hidden" id="current_user" name="current_user" value="<?php wp_get_current_user(); echo $current_user->display_name; ?>">
<input type="hidden" id="user_search" name="user_search" value="">
	                            </div>
			    		</div>
	
	</div>

	
  <div class="col-sm-8 col-md-9 wpsc_it_body">
<div class="table-responsive" style="overflow-x:auto;">
<input type="text" id="searchGeneric" class="form-control" name="custom_filter[s]" value="" autocomplete="off" placeholder="Search..." aria-label="Search">
<i class="fa fa-search wpsc_search_btn wpsc_search_btn_sarch"></i>
<br /><br />
<table id="tbl_templates_requests" class="display nowrap" cellspacing="5" cellpadding="5" width="100%">
        <thead>
            <tr>
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
                <th class="datatable_header"></th>
<?php
}
?>
                <th class="datatable_header">Request ID</th>
                <th class="datatable_header">Priority</th>
                <th class="datatable_header">Status</th>
                <!--<th class="datatable_header">Status <a href="#" data-toggle="tooltip" data-placement="right" data-html="true" aria-label="Box Status Help" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-box-status'); ?>"><i class="far fa-question-circle" style="color:#FFFFFF"></i></a></th>-->
                <th class="datatable_header">Name</th></th>
                <th class="datatable_header">Location</th>
                <th class="datatable_header">Last Updated</th>
            </tr>
        </thead>
    </table>
<br /><br />
</div>


<script>

jQuery(document).ready(function(){
  jQuery('[data-toggle="tooltip"]').tooltip();
  var dataTable = jQuery('#tbl_templates_requests').DataTable({
    'autoWidth': false,
    'drawCallback': function( settings ) {

jQuery('[data-toggle="tooltip"]').tooltip();

},
    'processing': true,
    'serverSide': true,
    'stateSave': true,
    'scrollX': true,
    'paging' : true,
    'stateSaveParams': function(settings, data) {
      data.ss = jQuery('#searchByStatus').val();
      data.sp = jQuery('#searchByPriority').val();
      data.sg = jQuery('#searchGeneric').val();
      data.rid = jQuery('#searchByRequestID').val();
      data.po = jQuery('#searchByProgramOffice').val();
      data.dc = jQuery('#searchByDigitizationCenter').val();
      data.sbu = jQuery('#searchByUser').val(); 
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
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
			jQuery('#searchByUser').val(data.sbu);
			jQuery('#user_search').val(data.aaName);
			
			// If data values aren't defined then set them as blank arrays.
			if( typeof data.aaVal == 'undefined' ) {
				//console.log('undefined!!!');
				data.aaVal = [];
				data.aaName = [];				
				//console.log(data);
			}
			
			data.aaVal.forEach( function(val, key) {
				let html_str = get_display_user_html(data.aaName[key], val); 
				jQuery('#assigned_agents').append(html_str);
			});
<?php } ?>
		},
    'serverMethod': 'post',
    'searching': false, // Remove default Search Control
    'ajax': {
       'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/request_processing.php',
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
          
          console.log('Names:');
          console.log(aaName);
          //console.log('Val:');
          //console.log(aaVal);
          
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
    'fixedColumns': true,
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
    	    'columnDefs': [	
         {	
            'width' : 5,
            'targets': 0,	
            'checkboxes': {	
               'selectRow': true	
            },	
         },
         { 'width': 100, 'targets': 4 },
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
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager'))
{
?>
       { data: 'request_id' }, 
<?php
}
?>
       { data: 'request_id_flag' },
       { data: 'ticket_priority' },
       { data: 'ticket_status' },
       { data: 'customer_name' },
       { data: 'location' },
       //{ data: 'ticket_priority' },
       { data: 'date_updated' },
    ]
  });
    
  jQuery( window ).unload(function() {
  dataTable.column(0).checkboxes.deselectAll();
});

  jQuery(document).on('keypress',function(e) {
    if(e.which == 13) {
        //prevents page redirect on enter
        e.preventDefault();
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

//jQuery('#searchGeneric').on('input keyup paste', function () {
//            dataTable.state.save();
//            dataTable.draw();
//});


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

//delete button
jQuery('#btn_delete_tickets').on('click', function(e){
     var form = this;
     var rows_selected = dataTable.column(0).checkboxes.selected();
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/delete_request.php',{
postvarsrequest_id : rows_selected.join(",")
}, 
   function (response) {
      //if(!alert(response)){
      
       wpsc_modal_open('Delete Request');
		  var data = {
		    action: 'wpsc_delete_request',
		    response_data: response
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  }); 
		  
          wpsc_get_ticket_list();
          dataTable.column(0).checkboxes.deselectAll();
      //}
   });
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
	jQuery('#tbl_templates_requests tbody').on('click', 'input', function () {        
	// 	console.log('checked');
		setTimeout(toggle_button_display, 1); //delay otherwise 
	});
	
	jQuery('.dt-checkboxes-select-all').on('click', 'input', function () {        
	 	console.log('checked');
		setTimeout(toggle_button_display, 1); //delay otherwise 
	});
	
	jQuery('#btn_delete_tickets').attr('disabled', 'disabled');
	
	function toggle_button_display() {
	//	var form = this;
		var rows_selected = dataTable.column(0).checkboxes.selected();
		if(rows_selected.count() > 0) {
			jQuery('#btn_delete_tickets').removeAttr('disabled');		
	  	} else {
	    	jQuery('#btn_delete_tickets').attr('disabled', 'disabled');    	
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
// 						+'<div class="flex-container searched-user" style="padding:5px;font-size:1.0em;">'
						+'<div class="flex-container searched-user staff-badge" style="">'
							+user_name
							+'<span  class="remove-user staff-close" ><i class="fa fa-times"></i></span>' 
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
</script>