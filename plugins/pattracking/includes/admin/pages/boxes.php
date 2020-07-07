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

?>


<div class="bootstrap-iso">
  
  <h3>Box Search</h3>
  
 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
  <div class="col-sm-12">
    	<button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=wpsc-tickets';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-retweet"></i> <?php _e('Reset Filters','supportcandy')?></button>
<?php		
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
?>
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_box_destruction_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-ban"></i> Destruction Completed</button></button>
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_label_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-tags"></i> Reprint Box Labels</button></button>
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
Enter one or more Box IDs:<br />
         <input type='text' id='searchByBoxID' class="form-control" data-role="tagsinput">
<br />

         <?php
  /*$po_array = Patt_Custom_Func::fetch_program_office_array(); ?>
<select id='searchByProgramOffice'>
     <option value=''>-- Select Program Office --</option>
     <?php foreach($po_array as $key => $value) { ?>
      <option value='<?php echo $value; ?>'><?php echo preg_replace("/\([^)]+\)/","",$value); ?></option>
     <?php } ?></select>*/
     
   $po_array = Patt_Custom_Func::fetch_program_office_array(); ?>
    <input type="search" list="searchByProgramOfficeList" placeholder='Enter program office' id='searchByProgramOffice' autocomplete='off'/>
    <datalist id='searchByProgramOfficeList'>
     <?php foreach($po_array as $key => $value) { ?>
        <option data-value='<?php echo $value; ?>' value='<?php echo preg_replace("/\([^)]+\)/","",$value); ?>'></option>
     <?php } ?>
     </datalist>
     
<br /><br />
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
<table id="tbl_templates_boxes" class="table table-striped table-bordered" cellspacing="5" cellpadding="5" width="100%">
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
                <th class="datatable_header">Request ID</th>
                <th class="datatable_header">Digitization Center</th>
                <th class="datatable_header">Program Office</th>
                <th class="datatable_header">Validation</th>
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

  var dataTable = jQuery('#tbl_templates_boxes').DataTable({
    'processing': true,
    'serverSide': true,
    'stateSave': true,
    'stateSaveParams': function(settings, data) {
      data.sg = jQuery('#searchGeneric').val();
      data.bid = jQuery('#searchByBoxID').val();
      data.po = jQuery('#searchByProgramOffice').val();
      data.dc = jQuery('#searchByDigitizationCenter').val();
    },
    'stateLoadParams': function(settings, data) {
      jQuery('#searchGeneric').val(data.sg);
      jQuery('#searchByBoxID').val(data.bid);
      jQuery('#searchByProgramOffice').val(data.po);
      jQuery('#searchByDigitizationCenter').val(data.dc);
    },
    'serverMethod': 'post',
    'searching': false, // Remove default Search Control
    'ajax': {
       'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/box_processing.php',
       'data': function(data){
          // Read values
          var po_value = jQuery('#searchByProgramOffice').val();
          var po = jQuery('#searchByProgramOfficeList [value="' + po_value + '"]').data('value');
          var sg = jQuery('#searchGeneric').val();
          var boxid = jQuery('#searchByBoxID').val();
          var dc = jQuery('#searchByDigitizationCenter').val();
          // Append to data
          data.searchGeneric = sg;
          data.searchByBoxID = boxid;
          data.searchByProgramOffice = po;
          data.searchByDigitizationCenter = dc;
       }
    },
    'lengthMenu': [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
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
       { data: 'request_id' },
       { data: 'location' },
       { data: 'acronym' },
       { data: 'validation' },
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

  jQuery("#searchByProgramOffice").change(function(){
    dataTable.state.save();
    dataTable.draw();
});

  jQuery("#searchByDigitizationCenter").change(function(){
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


jQuery("#searchByBoxID").tagsInput({
   'defaultText':'',
   'onAddTag': onAddTag,
   'onRemoveTag': onRemoveTag,
   'width':'100%'
});

jQuery("#searchByBoxID_tag").on('paste',function(e){
    var element=this;
    setTimeout(function () {
        var text = jQuery(element).val();
        var target=jQuery("#searchByBoxID");
        var tags = (text).split(/[ ,]+/);
        for (var i = 0, z = tags.length; i<z; i++) {
              var tag = jQuery.trim(tags[i]);
              if (!target.tagExist(tag)) {
                    target.addTag(tag);
              }
              else
              {
                  jQuery("#searchByBoxID_tag").val('');
              }
                
         }
    }, 0);
});

jQuery('#wpsc_individual_refresh_btn').on('click', function(e){
    jQuery('#searchGeneric').val('');
    jQuery('#searchByProgramOffice').val('');
    jQuery('#searchByDigitizationCenter').val('');
    jQuery('#searchByBoxID').importTags('');
    dataTable.column(0).checkboxes.deselectAll();
	dataTable.state.clear();
	dataTable.destroy();
	location.reload();
});

<?php	
// BEGIN ADMIN BUTTONS
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
?>

jQuery('#wpsc_individual_label_btn').on('click', function(e){
     var form = this;
     var rows_selected = dataTable.column(0).checkboxes.selected();
     var rows_string = rows_selected.join(",");
     
     jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/boxlabels_processing.php',{
postvarsboxids : rows_selected.join(",")
}, 
   function (response) {
       
       var boxidinfo = response.split('|')[1];
       var substring_false = "false";
       var substring_warn = "warn";
       var substring_true = "true";

        
       if(response.indexOf(substring_false) >= 0) {
       alert('Cannot print box labels for destroyed boxes.');
       }
       
       if(response.indexOf(substring_warn) >= 0) {
       alert('One or more boxes that you selected are destroyed and it\'s label will not generate.');
       window.open("<?php echo WPPATT_PLUGIN_URL; ?>includes/ajax/pdf/box_label.php?id="+boxidinfo, "_blank");
       }
       
       if(response.indexOf(substring_true) >= 0) {
       //alert('Success! All labels available.');
       window.open("<?php echo WPPATT_PLUGIN_URL; ?>includes/ajax/pdf/box_label.php?id="+boxidinfo, "_blank");
       }
      
   });

});

jQuery('#wpsc_box_destruction_btn').on('click', function(e){
     var form = this;
     var rows_selected = dataTable.column(0).checkboxes.selected();
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_destruction.php',{
postvarsboxid : rows_selected.join(",")
}, 
   function (response) {
      //if(!alert(response)){
      
      wpsc_modal_open('Destruction Completed');
		  var data = {
		    action: 'wpsc_get_destruction_completed_b',
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

<?php
}
// END ADMIN BUTTONS
?>
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
