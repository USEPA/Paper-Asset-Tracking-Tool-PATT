<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;



//$GLOBALS['id'] = $_GET['id'];
$GLOBALS['id'] = '0000001-1';
$GLOBALS['pid'] = 'boxsearch';
$GLOBALS['page'] = 'boxdetails';


//include_once WPPATT_ABSPATH . 'includes/class-wppatt-functions.php';
//$load_styles = new wppatt_Functions();
//$load_styles->addStyles();

$general_appearance = get_option('wpsc_appearance_general_settings');



$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$create_return_btn_css       = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';

//$create_return_btn_css = $action_default_btn_css;

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';

$action_admin_btn_css = 'background-color:#5cbdea !important;color:#FFFFFF !important;';

$agent_permissions = $wpscfunction->get_current_agent_permissions();

?>


<div class="bootstrap-iso">
  
  <h3>Decline Dashboard</h3>
  
 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
  <div class="col-sm-12">
<!--       <button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=returncreate';" class="btn btn-sm wpsc_action_btn" style="<?php echo $create_return_btn_css?>"><i class="fa fa-plus"></i> New Decline</button> -->
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager') )
{
?>  
      <button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=declinecreate';" class="btn btn-sm wpsc_action_btn" style="<?php echo $create_return_btn_css?>"><i class="fa fa-plus"></i> New Decline</button>
<?php
}
?>       

<button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=decline';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> Decline List</button>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" style="<?php echo $action_default_btn_css?> margin-right: 30px !important;"><i class="fas fa-retweet"></i> <?php _e('Reset Filters','supportcandy')?></button>
  
      
<!--       <button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=boxdetails';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-cloud-download-alt"></i> Export</button> -->
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager') )
{
?>        
      <button type="button" id="wppatt_change_shipping_btn"  class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-truck"></i> Change Shipping Tracking Number <a href="#" aria-label="Change shipping tracking number" data-toggle="tooltip"  data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-change-shipping'); ?>"><i class="far fa-question-circle"></i></a></button>
<?php
}
?>      
<!--       <button type="button" id="wppatt_change_status_btn" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-retweet"></i> Change Status</button>       -->
      
<!--       <button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=returndetails&id=RTN-0000001';" class="btn btn-sm wpsc_action_btn" style="<?php echo $create_return_btn_css?>"><i class="fas fa-vial"></i> Return Details: RTN-0000001 </button> -->
      
<!--       <button type="button" id="wppatt_return_btn"  class="btn btn-sm wpsc_action_btn" style="<?php echo $create_return_btn_css?>"><i class="fas fa-truck-loading"></i> Return </button> -->
      
      
  </div>

</div>

<div class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">

	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">

    	<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
			<h4 class="widget_header"><i class="fa fa-filter"></i> Filters <a href="#" aria-label="Filter" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-filters'); ?>"><i class="far fa-question-circle"></i></a></h4>
            <hr class="widget_divider">
			<div class="wpsp_sidebar_labels">Enter one or more Decline IDs:<br>
				<input type='text' id='searchByReturnID' class="form-control" data-role="tagsinput"><br>
				<?php $po_array = Patt_Custom_Func::fetch_program_office_array(); ?>
				<input type="search" list="searchByProgramOfficeList" placeholder='Enter program office' id='searchByProgramOffice' autocomplete='off'/>
				<datalist id='searchByProgramOfficeList'>
					<?php foreach($po_array as $key => $value) { ?>
					<option data-value='<?php echo $value; ?>' value='<?php echo preg_replace("/\([^)]+\)/","",$value); ?>'></option>
					<?php } ?>
				</datalist>
<!--
				<select id='searchByProgramOffice'>
					<option value=''>-- Select Program Office --</option>
					<?php foreach($po_array as $key => $value) { ?>
						<option value='<?php echo $value; ?>'><?php echo $value; ?></option>
				<?php } ?></select>
-->
				<br><br>
				<select id='searchByDigitizationCenter'>
					<option value=''>-- Select Digitization Center --</option>
					<option value='East'>East</option>
					<option value='East CUI'>East CUI</option>
					<option value='West'>West</option>
					<option value='West CUI'>West CUI</option>
					<option value='Not Assigned'>Not Assigned</option>
				</select>

			</div>
		</div>
	</div>
  
	<div class="col-sm-8 col-md-9 wpsc_it_body">

<style>

div.dataTables_wrapper {
        width: 100%;
        margin: 0;
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

#searchByProgramOffice {
	width: 83%;
}
</style>
    

<div class="table-responsive" style="overflow-x:auto;">
<input type="text" id="searchGeneric" class="form-control" name="custom_filter[s]" value="" autocomplete="off" placeholder="Search...">
<i class="fa fa-search wpsc_search_btn wpsc_search_btn_sarch"></i>
<br /><br />
<form id="frm-example" method="POST">
<table id="tbl_templates_return" class="display nowrap" cellspacing="5" cellpadding="5">
        <thead>
            <tr>
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager') )
{
?>	            
                <th class="datatable_header"></th>      
<?php
}
?>	                         
	  			<th class="datatable_header">Decline ID</th>
	  			<th class="datatable_header">Status</th>
	  			<th class="datatable_header">Date Update</th>
	  			<th class="datatable_header">Request Date</th>
	  			<th class="datatable_header">Returned Date</th>
	  			<th class="datatable_header">Shipping Tracking Number</th>	  			
            </tr>
        </thead>
    </table>
</div>
<br><br>

<?php
$convert_box_id = $wpdb->get_row(
"SELECT id
FROM wpqa_wpsc_epa_boxinfo
WHERE box_id = '" .  $GLOBALS['id'] . "'");

$box_id = $convert_box_id->id;
//echo 'new box id: '.$box_id; //can above be removed?
?>
<input type='hidden' id='box_id' value='<?php echo $box_id; ?>' />
<input type='hidden' id='page' value='<?php echo $GLOBALS['page']; ?>' />
<input type='hidden' id='p_id' value='<?php echo $GLOBALS['pid']; ?>' />
</form>
<div id="test_test"></div>


<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.css" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.js" crossorigin="anonymous"></script>
  
<script>


jQuery(document).ready(function(){

    jQuery('[data-toggle="tooltip"]').tooltip(); 

	var agent_permission_label = '<?php echo $agent_permissions["label"] ?>';
	var is_requester = false;
	if( agent_permission_label == 'Requester' ) {
		is_requester = true;
	}

  //NEW: dataTable for return
    var dataTable = jQuery('#tbl_templates_return').DataTable({
	    'autoWidth': false,
	    'processing': true,
// 	    'order': [[1, 'desc']],
// 		'ordering': false,
		'stateSave': true,
		'scrollX' : true,
		'paging' : true,
			'stateSaveParams': function(settings, data) {
			data.sg = jQuery('#searchGeneric').val();
			data.bid = jQuery('#searchByReturnID').val();
			data.po = jQuery('#searchByProgramOffice').val();
			data.dc = jQuery('#searchByDigitizationCenter').val();
			data.page = jQuery('tbl_templates_boxes_length').val();
		},
		'stateLoadParams': function(settings, data) {
			jQuery('#searchGeneric').val(data.sg);
			jQuery('#searchByReturnID').val(data.bid);
			jQuery('#searchByProgramOffice').val(data.po);
			jQuery('#searchByDigitizationCenter').val(data.dc);
			jQuery('tbl_templates_boxes_length').val(data.page);
		},
	    'serverSide': true,
	    'serverMethod': 'post',
	    'searching': false, // Remove default Search Control
	    'ajax': {
	       'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/return_processing.php',
	       'data': function(data){
	          // Read values
	          var po_value = jQuery('#searchByProgramOffice').val();
	          var po = jQuery('#searchByProgramOfficeList [value="' + po_value + '"]').data('value');
	          var sg = jQuery('#searchGeneric').val();
	          var boxid = jQuery('#searchByReturnID').val();
	          var dc = jQuery('#searchByDigitizationCenter').val();   
	          
// 	          let rtnid = boxid.replace(/RTN-/g, ''); 
  	          let rtnid = boxid.replace(/D-/g, '');       
	          
	          // Append to data
	          data.searchGeneric = sg;
	          data.searchByReturnID = rtnid;
//	          data.searchByProgramOffice = po_value;
	          data.searchByProgramOffice = po;	          
// 	          data.searchByProgramOfficeID = po;
	          data.searchByDigitizationCenter = dc;
	          data.is_requester = is_requester;
	       }
	    },
	    'drawCallback': function (settings) { 
	        // Here the response
	        var response = settings.json;
	        console.log(response);
    	},
	       	     
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager') )
{
?>			    
	    'columnDefs': [	
	         {
		     width: '5px',
	            'targets': 0,
	            'checkboxes': {
	               'selectRow': true
	            }
	         },
	          	         	         
	      { width: '60px', targets: 1 },
	      { width: '70px', targets: 2 },
	      { width: '50px', targets: 3 },
	      { width: '40px', targets: 4 },
	      { width: '30px', targets: 5 },
	      { width: '100px', targets: 6 }	      
// 	      { width: '5px', targets: 7 },
// 	      { width: '5px', targets: 7 }
	      ],		        
	      'select': {
	         'style': 'multi'
	      },
<?php
}
?>	      
	      'order': [[3, 'desc']], 	
	      	           
	    'columns': [
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent') || ($agent_permissions['label'] == 'Manager') )
{
?>	    
	       { data: 'return_id_flag' },   
<?php
}
?>       
	       { data: 'return_id' },
       		       	       
	       { data: 'status' }, 
	       { data: 'updated_date' },
	       { data: 'request_date' },	
	       { data: 'return_receipt_date' },
	       { data: 'tracking_number' }

	    ]
	});
  
  
	jQuery('#toplevel_page_wpsc-tickets').removeClass('wp-not-current-submenu'); 
	jQuery('#toplevel_page_wpsc-tickets').addClass('wp-has-current-submenu'); 
	jQuery('#toplevel_page_wpsc-tickets').addClass('wp-menu-open'); 
	jQuery('#toplevel_page_wpsc-tickets a:first').removeClass('wp-not-current-submenu');
	jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-has-current-submenu'); 
	jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-menu-open');
	jQuery('#menu-dashboard').removeClass('current');
	jQuery('#menu-dashboard a:first').removeClass('current');
  

	//
	// Code block for toggling edit buttons on/off when checkboxes are set
	//
	jQuery('#tbl_templates_return tbody').on('click', 'input', function () {        
	// 	console.log('checked');
		setTimeout(toggle_button_display, 1); //delay otherwise 
	});
	
	// removes checkboxes when page is reloaded
	jQuery( window ).unload(function() {
		dataTable.column(0).checkboxes.deselectAll();
	});
	
	// allows the 'select all' checkbox to toggle the buttons	
	jQuery('.dt-checkboxes-select-all').on('click', 'input', function () {        
	 	console.log('checked');
		setTimeout(toggle_button_display, 1); //delay otherwise 
	});
	
	/* //Old code works with predefine table
	jQuery('.dt-checkboxes').on('click', function(e){
		console.log('checked');
		setTimeout(toggle_button_display, 10); //delay otherwise 
	});
	*/
	
	jQuery('#wppatt_change_status_btn').attr('disabled', 'disabled');
	jQuery('#wppatt_change_shipping_btn').attr('disabled', 'disabled');
	jQuery('#wppatt_return_btn').attr('disabled', 'disabled');
	
	function toggle_button_display() {
	//	var form = this;
		var rows_selected = dataTable.column(0).checkboxes.selected();
		if(rows_selected.count() > 0) {
	    	//console.log('boxes checked '+rows_selected.count());
			jQuery('#wppatt_change_status_btn').removeAttr('disabled');
			jQuery('#wppatt_change_shipping_btn').removeAttr('disabled');
			jQuery('#wppatt_return_btn').removeAttr('disabled');		
	  	} else {
	    	//console.log('no checks boxed '+rows_selected.count());
	    	jQuery('#wppatt_change_status_btn').attr('disabled', 'disabled');
	    	jQuery('#wppatt_change_shipping_btn').attr('disabled', 'disabled');    	
	    	jQuery('#wppatt_return_btn').attr('disabled', 'disabled');    	    	
	  	}
	}
	
	
	// function wppatt_get_status_editor() {
	jQuery('#wppatt_change_status_btn').click( function() {		
		
		var rows_selected = dataTable.column(0).checkboxes.selected();
	    var arr = [];
	
	    // Loop through array
	    [].forEach.call(rows_selected, function(inst){
	        console.log('the inst: '+inst);
	        arr.push(inst);
	    });
	
	    
	    console.log('arr: '+arr);
	    console.log(arr);
		
		wpsc_modal_open('Edit Status Details');
		
		var data = {
		    action: 'wppatt_return_status_change',
		    return_ids: arr
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
	// 		    jQuery('#wpsc_popup_body').html(response_str);		    
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		    //window.location.reload();
		}); 
	});
	
	
	
	jQuery('#wppatt_change_shipping_btn').click( function() {	
	
		var rows_selected = dataTable.column(0).checkboxes.selected();
	    var arr = [];
	
	    // Loop through array
	    [].forEach.call(rows_selected, function(inst){
	        console.log('the inst: '+inst);
	        arr.push(inst);
	    });
	    
	    console.log('arr: '+arr);
	    console.log(arr);
		
		wpsc_modal_open('Edit Shipping Details');
		
		var data = {
		    action: 'wppatt_recall_shipping_change',
		    recall_ids: arr,
		    return_ids: arr,
		    from_page: 'return-dashboard',
		    category: 'return'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
	// 		    jQuery('#wpsc_popup_body').html(response_str);		    
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		});
		
		//dataTable.ajax.reload( null, false );
		
		 
	});
	
/*
	jQuery('.wpsc_popup_close').click( function() {	
		console.log('This button worked!');
	});
*/
	
	jQuery('#wppatt_return_btn').click( function() {	
	
		var rows_selected = dataTable.column(0).checkboxes.selected();
	    var arr = [];
	
	    // Loop through array
	    [].forEach.call(rows_selected, function(inst){
	        console.log('the inst: '+inst);
	        arr.push(inst);
	    });
	    
	    console.log('arr: '+arr);
	    console.log(arr);
		
		wpsc_modal_open('Initiate Decline');
		
		var data = {
		    action: 'wppatt_initiate_return',
		    return_ids: arr
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
	// 		    jQuery('#wpsc_popup_body').html(response_str);		    
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		}); 		

});










	jQuery(document).on('keypress',function(e) {
	    if(e.which == 13) {
	        dataTable.draw();
	    }
	});
	
	jQuery("#searchByProgramOffice").change(function(){
	    dataTable.state.save();
	    dataTable.draw();
	});
	
	jQuery("#searchByDigitizationCenter").change(function(){
	    dataTable.draw();
	});
	
	//jQuery('#searchGeneric').on('input keyup paste', function () {
	//            dataTable.state.save();
	//            dataTable.draw();
	//});


    function onAddTag(tag) {
      dataTable.draw();
    }
    function onRemoveTag(tag) {
      dataTable.draw();
    }


	jQuery("#searchByReturnID").tagsInput({
	   'defaultText':'',
	   'onAddTag': onAddTag,
	   'onRemoveTag': onRemoveTag,
	   'width':'100%'
	});
	
	jQuery("#searchByReturnID_tag").on('paste',function(e){
	    var element=this;
	    setTimeout(function () {
	        var text = jQuery(element).val();
	        var target=jQuery("#searchByReturnID");
	        var tags = (text).split(/[ ,]+/);
	        for (var i = 0, z = tags.length; i<z; i++) {
	              var tag = jQuery.trim(tags[i]);
	              if (!target.tagExist(tag)) {
	                    target.addTag(tag);
	              }
	              else
	              {
	                  jQuery("#searchByReturnID_tag").val('');
	              }
	                
	         }
	    }, 0);
	});


	jQuery('#wpsc_individual_refresh_btn').on('click', function(e){
	    jQuery('#searchGeneric').val('');
	    jQuery('#searchByProgramOffice').val('');
	    jQuery('#searchByDigitizationCenter').val('');
	    jQuery('#searchByReturnID').importTags('');
	    jQuery('tbl_templates_boxes_length').val('10');
	    dataTable.column(0).checkboxes.deselectAll();
		dataTable.state.clear();
		//dataTable.draw(); // Not in Boxes.php. Try without.
		dataTable.destroy(); // NEW from Boxes.php
		location.reload(); // NEW from Boxes.php
		//return false; // Not in Boxes.php. Try without.
	});
  
		
		


	// Handle form submission event 
	jQuery('#frm-example').on('submit', function(e){
      var form = this;
      console.log('this is never used, right - frm-example submit');
      var rows_selected = dataTable.column(0).checkboxes.selected();
      // Iterate over all selected checkboxes
      jQuery.each(rows_selected, function(index, rowId){
         // Create a hidden element 
         jQuery(form).append(
             jQuery('<input>')
                .attr('type', 'hidden')
                .attr('name', 'id[]')
                .val(rowId)
         );
         
        wpsc_modal_open('Edit Box Information');
      var data = {
        action: 'wpsc_get_rfid_box_editor',
        box_id : rows_selected.join(",")
      };
      jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
        var response = JSON.parse(response_str);
        jQuery('#wpsc_popup_body').html(response.body);
        jQuery('#wpsc_popup_footer').html(response.footer);
        jQuery('#wpsc_cat_name').focus();
      });  
      
      });
      // FOR DEMONSTRATION ONLY
      // The code below is not needed in production
      
      // Output form data to a console     
      //jQuery('#example-console-rows').text(rows_selected.join(","));
      
      // Output form data to a console     
      //jQuery('#example-console-form').text(jQuery(form).serialize());
       
      // Remove added elements
      //jQuery('input[name="id\[\]"]', form).remove();
       
      // Prevent actual form submission
      e.preventDefault();
   });   




  
});


function wppatt_return_editor() {
    console.log('return');
	wpsc_modal_open('Initiate Decline');
	var data = {
	    action: 'wppatt_initiate_return',
	    //ticket_ids: ticket_ids
	};
	jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
	    var response = JSON.parse(response_str);
// 		    jQuery('#wpsc_popup_body').html(response_str);		    
	    jQuery('#wpsc_popup_body').html(response.body);
	    jQuery('#wpsc_popup_footer').html(response.footer);
	    jQuery('#wpsc_cat_name').focus();
	}); 	
}

</script>
  </div>
 

 

</div>
</div>
</div>



<!-- Pop-up snippet start from RFID.php-->
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