<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$GLOBALS['id'] = $_GET['id'];
$GLOBALS['pid'] = $_GET['pid'];
$GLOBALS['page'] = $_GET['page'];

$agent_permissions = $wpscfunction->get_current_agent_permissions();

//include_once WPPATT_ABSPATH . 'includes/class-wppatt-functions.php';
//$load_styles = new wppatt_Functions();
//$load_styles->addStyles();

$general_appearance = get_option('wpsc_appearance_general_settings');

$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';

?>


<div class="bootstrap-iso">
  
  <h3>Shipping Status Editor</h3>
  
 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
  <div class="col-sm-12">
    	<button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=wpsc-tickets';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-retweet"></i> <?php _e('Reset Filters','supportcandy')?></button>
        
        <?php		
        if (($agent_permissions['label'] == 'Administrator'))
        {
        ?>
            <button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_shipped_btn" style="background-color:#FF5733 !important;color:#FFFFFF !important;"><i class="fas fa-check-circle"></i> Shipped</button></button>
            <button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_delivered_btn" style="background-color:#FF5733 !important;color:#FFFFFF !important;"><i class="fas fa-truck-loading"></i> Delivered</button></button>
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
Enter one or more Tracking Numbers:<br />
         <input type='text' id='searchByTN' class="form-control" data-role="tagsinput">
<br />
        <select id='searchByShipped'>
           <option value=''>-- Shipped --</option>
           <option value='1'>Yes</option>
           <option value='0'>No</option>
         </select>
<br /><br />
        <select id='searchByDelivered'>
           <option value=''>-- Delivered --</option>
           <option value='1'>Yes</option>
           <option value='0'>No</option>
         </select>
<br /><br />

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
<table id="tbl_templates_folderfile" class="table table-striped table-bordered" cellspacing="5" cellpadding="5" width="100%">
        <thead>
            <tr>
                <?php		
                if (($agent_permissions['label'] == 'Administrator'))
                {
                ?>
                <th class="datatable_header"></th>
                <?php
                }
                ?>
                <th class="datatable_header">Tracking Number</th>
                <th class="datatable_header">Shipping Company</th>
                <th class="datatable_header">Status</th>
                <th class="datatable_header">Shipped</th>
                <th class="datatable_header">Delivered</th>
            </tr>
        </thead>
    </table>
<br /><br />

<input type='hidden' id='page' value='<?php echo $GLOBALS['page']; ?>' />
<input type='hidden' id='p_id' value='<?php echo $GLOBALS['pid']; ?>' />
</form>

<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.css" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.js" crossorigin="anonymous"></script>
  
  <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
  <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
  
<script>

jQuery(document).ready(function(){

  var dataTable = jQuery('#tbl_templates_folderfile').DataTable({
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'stateSave': true,
    'stateSaveParams': function(settings, data) {
      data.sg = jQuery('#searchGeneric').val();
      data.tn = jQuery('#searchByTN').val();
      data.sh = jQuery('#searchByShipped').val();
      data.de = jQuery('#searchByDelivered').val();
    },
    'stateLoadParams': function(settings, data) {
      jQuery('#searchGeneric').val(data.sg);
      jQuery('#searchByTN').val(data.tn);
      jQuery('#searchByShipped').val(data.sh);
      jQuery('#searchByDelivered').val(data.de);
    },
    'searching': false, // Remove default Search Control
    'ajax': {
       'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/shipping_processing.php',
       'data': function(data){
          // Read values
          var sg = jQuery('#searchGeneric').val();
          var tn = jQuery('#searchByTN').val();
          var sh = jQuery('#searchByShipped').val();
          var de = jQuery('#searchByDelivered').val();
          // Append to data
          data.searchGeneric = sg;
          data.searchByTN = tn;
          data.searchByShipped = sh;
          data.searchByDelivered = de;
       }
    },
    'lengthMenu': [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
    <?php		
    if (($agent_permissions['label'] == 'Administrator'))
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
        if (($agent_permissions['label'] == 'Administrator'))
        {
        ?>
       { data: 'id' },
       <?php
        }
        ?>
       { data: 'tracking_number' }, 
       { data: 'company_name' },
       { data: 'status' },
       { data: 'shipped' },
       { data: 'delivered' },
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

  jQuery("#searchByShipped").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

  jQuery("#searchByDelivered").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

jQuery('#searchGeneric').on('input keyup paste', function () {
    var hasValue = jQuery.trim(this.value).length;
    if(hasValue == 0) {
        dataTable.state.save();
        dataTable.draw();
        }
});


		function onAddTag(tag) {
		    dataTable.state.save();
			dataTable.draw();
		}
		function onRemoveTag(tag) {
		    dataTable.state.save();
			dataTable.draw();
		}

jQuery('#wpsc_individual_refresh_btn').on('click', function(e){
    jQuery('#searchGeneric').val('');
    jQuery('#searchByShipped').val('');
    jQuery('#searchByDelivered').val('');
    jQuery('#searchByTN').importTags('');
    dataTable.column(0).checkboxes.deselectAll();
	dataTable.state.clear();
	dataTable.destroy();
	location.reload();
});

<?php		
// BEGIN ADMIN BUTTONS
if (($agent_permissions['label'] == 'Administrator'))
{
?>
//reprint labels button		

jQuery('#wpsc_individual_label_btn').on('click', function(e){
     var form = this;
     var rows_selected = dataTable.column(0).checkboxes.selected();
     var arr = {};
     
     jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/documentlabels_processing.php',{
postvarsfolderdocid : rows_selected.join(",")
}, 
   function (response) {
       
       var folderdocinfo = response.split('|')[1];
       var folderdocinfo_array = folderdocinfo.split(',');
       var substring_false = "false";
       var substring_warn = "warn";
       var substring_true = "true";

       if(response.indexOf(substring_false) >= 0) {
       alert('Cannot print folder/file labels for documents that are not assigned to a location.');
       }
       
       if(response.indexOf(substring_warn) >= 0) {
       alert('One or more documents that you selected do not have an assigned location and it\'s label will not generate.');
           // Loop through array
    [].forEach.call(folderdocinfo_array, function(inst){
        var x = inst.split("-")[2].substr(1);
        // Check if arr already has an index x, if yes then push
        if(arr.hasOwnProperty(x)) 
            arr[x].push(inst);
        // Or else create a new one with inst as the first element.
        else 
            arr[x] = [inst];
    });
if(Array.isArray(arr[1]) || Array.isArray(arr[2]) ) {
if (Array.isArray(arr[1]) && arr[1].length) {
window.open("<?php echo WPPATT_PLUGIN_URL; ?>includes/ajax/pdf/folder_separator_sheet.php?id="+arr[1].toString(), "_blank");
}
if (Array.isArray(arr[2]) && arr[2].length) {
window.open("<?php echo WPPATT_PLUGIN_URL; ?>includes/ajax/pdf/file_separator_sheet.php?id="+arr[2].toString(), "_blank");
}
} else {
alert('Please select a folder/file.');
}
       }
       
       if(response.indexOf(substring_true) >= 0) {
       //alert('Success! All labels available.');
           // Loop through array
    [].forEach.call(folderdocinfo_array, function(inst){
        var x = inst.split("-")[2].substr(1);
        // Check if arr already has an index x, if yes then push
        if(arr.hasOwnProperty(x)) 
            arr[x].push(inst);
        // Or else create a new one with inst as the first element.
        else 
            arr[x] = [inst];
    });
if(Array.isArray(arr[1]) || Array.isArray(arr[2]) ) {
if (Array.isArray(arr[1]) && arr[1].length) {
window.open("<?php echo WPPATT_PLUGIN_URL; ?>includes/ajax/pdf/folder_separator_sheet.php?id="+arr[1].toString(), "_blank");
}
if (Array.isArray(arr[2]) && arr[2].length) {
window.open("<?php echo WPPATT_PLUGIN_URL; ?>includes/ajax/pdf/file_separator_sheet.php?id="+arr[2].toString(), "_blank");
}
} else {
alert('Please select a folder/file.');
}
       }
      
   });

});

//validation button
jQuery('#wpsc_individual_validation_btn').on('click', function(e){
     var form = this;
     var rows_selected = dataTable.column(0).checkboxes.selected();
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_validate.php',{
postvarsfolderdocid : rows_selected.join(","),
postvarsuserid : <?php $user_ID = get_current_user_id(); echo $user_ID; ?>,
postvarpage : jQuery('#page').val()
}, 
   function (response) {
      //if(!alert(response)){
      
       wpsc_modal_open('Validation');
		  var data = {
		    action: 'wpsc_get_validate_ff',
		    response_data: response
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  }); 
		  
          dataTable.ajax.reload( null, false );
      //}
   });
});

//re-scan button
jQuery('#wpsc_individual_rescan_btn').on('click', function(e){
     var form = this;
     var rows_selected = dataTable.column(0).checkboxes.selected();
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_rescan.php',{
postvarsfolderdocid : rows_selected.join(","),
postvarpage : jQuery('#page').val()
}, 
   function (response) {
      //if(!alert(response)){
      
       wpsc_modal_open('Re-scan');
		  var data = {
		    action: 'wpsc_get_rescan_ff',
		    response_data: response
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  }); 
		  
          dataTable.ajax.reload( null, false );
      //}
   });
});

//freeze button
jQuery('#wpsc_individual_freeze_btn').on('click', function(e){
     var form = this;
     var rows_selected = dataTable.column(0).checkboxes.selected();
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_freeze.php',{
postvarsfolderdocid : rows_selected.join(","),
postvarpage : jQuery('#page').val(),
boxid : jQuery('#box_id').val()
}, 
   function (response) {
      //if(!alert(response)){
      
             wpsc_modal_open('Freeze');
		  var data = {
		    action: 'wpsc_get_freeze_ff',
		    response_data: response
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  }); 
		  
       var substring = "removed";
       dataTable.ajax.reload( null, false );
       
       if(response.indexOf(substring) !== -1) {
       jQuery('#ud_alert').hide();
       } else {
       jQuery('#ud_alert').show(); 
       }
       
      //}
   });
});

//unauthorize destruction button
jQuery('#wpsc_individual_destruction_btn').on('click', function(e){
     var form = this;
     var rows_selected = dataTable.column(0).checkboxes.selected();
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_unauthorize_destruction.php',{
postvarsfolderdocid : rows_selected.join(","),
postvarpage : jQuery('#page').val(),
boxid : jQuery('#box_id').val()
}, 
   function (response) {
      //if(!alert(response)){
      
       wpsc_modal_open('Unauthorized Destruction');
		  var data = {
		    action: 'wpsc_unauthorized_destruction_ff',
		    response_data: response
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  }); 
		  
       var substring = "removed";
       dataTable.ajax.reload( null, false );
       
       if(response.indexOf(substring) !== -1) {
       jQuery('#ud_alert').hide();
       } else {
       jQuery('#ud_alert').show(); 
       }
       
      //}
   });
});
<?php
}
// END ADMIN BUTTONS
?>

jQuery("#searchByTN").tagsInput({
   'defaultText':'',
   'onAddTag': onAddTag,
   'onRemoveTag': onRemoveTag,
   'width':'100%'
});

jQuery("#searchByTN_tag").on('paste',function(e){
    var element=this;
    setTimeout(function () {
        var text = jQuery(element).val();
        var target=jQuery("#searchByTN");
        var tags = (text).split(/[ ,]+/);
        for (var i = 0, z = tags.length; i<z; i++) {
              var tag = jQuery.trim(tags[i]);
              if (!target.tagExist(tag)) {
                    target.addTag(tag);
              }
              else
              {
                  jQuery("#searchByTN_tag").val('');
              }
                
         }
    }, 0);
});

// Code block for toggling edit buttons on/off when checkboxes are set
	jQuery('#tbl_templates_folderfile tbody').on('click', 'input', function () {        
	// 	console.log('checked');
		setTimeout(toggle_button_display, 1); //delay otherwise 
	});
	
	jQuery('.dt-checkboxes-select-all').on('click', 'input', function () {        
	 	console.log('checked');
		setTimeout(toggle_button_display, 1); //delay otherwise 
	});
	
	jQuery('#wpsc_individual_shipped_btn').attr('disabled', 'disabled');
	jQuery('#wpsc_individual_delivered_btn').attr('disabled', 'disabled');
	
	function toggle_button_display() {
	//	var form = this;
		var rows_selected = dataTable.column(0).checkboxes.selected();
		if(rows_selected.count() > 0) {
			jQuery('#wpsc_individual_shipped_btn').removeAttr('disabled');
			jQuery('#wpsc_individual_delivered_btn').removeAttr('disabled');
	  	} else {
	    	jQuery('#wpsc_individual_shipped_btn').attr('disabled', 'disabled');  
	    	jQuery('#wpsc_individual_delivered_btn').attr('disabled', 'disabled');
	  	}
	}

});

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
