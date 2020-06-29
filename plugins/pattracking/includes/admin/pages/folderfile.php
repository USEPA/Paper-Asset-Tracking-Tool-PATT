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
  
  <h3>Folder/File Search</h3>
  
 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
  <div class="col-sm-12">
    	<button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=wpsc-tickets';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="window.location.reload();" style="<?php echo $action_default_btn_css?>"><i class="fas fa-retweet"></i> <?php _e('Reset Filters','supportcandy')?></button>
        
        <?php		
        if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
        {
        ?>
            <button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_validation_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-check-circle"></i> Validate</button></button>
    		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_destruction_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-flag"></i> Unauthorize Destruction</button></button>
    		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_freeze_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-snowflake"></i> Freeze</button></button>
    		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_label_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-tags"></i> Reprint Labels</button></button>
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
Enter one or more Document IDs:<br />
         <input type='text' id='searchByDocID' class="form-control" data-role="tagsinput">
<br />
         <?php
    $po_array = Patt_Custom_Func::fetch_program_office_array(); ?>
    <input type="text" list="searchByProgramOfficeList" name="program_office" placeholder='Enter program office' id="searchByProgramOffice"/>
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
                <th class="datatable_header">Document/File ID</th>
                <th class="datatable_header">Request ID</th>
                <th class="datatable_header">Digitization Center</th>
                <th class="datatable_header">Program Office</th>
                <th class="datatable_header">Validation</th>
            </tr>
        </thead>
    </table>
<br /><br />

<?php
$convert_box_id = $wpdb->get_row(
"SELECT wpqa_wpsc_epa_boxinfo.id
FROM wpqa_wpsc_epa_folderdocinfo, wpqa_wpsc_epa_boxinfo
WHERE wpqa_wpsc_epa_boxinfo.box = '" .  $GLOBALS['id'] . "'");

$box_id = $convert_box_id->id;
?>
<!--reuse box_id in update_validate and update_unauthorize_destruction-->
<input type='hidden' id='box_id' value='<?php echo $box_id; ?>' />
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

  var dataTable = jQuery('#tbl_templates_boxes').DataTable({
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'searching': false, // Remove default Search Control
    'ajax': {
       'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/document_processing.php',
       'data': function(data){
          // Read values
          var po_value = jQuery('#searchByProgramOffice').val();
          var po = jQuery('#searchByProgramOfficeList [value="' + po_value + '"]').data('value');
          var sg = jQuery('#searchGeneric').val();
          var docid = jQuery('#searchByDocID').val();
          var dc = jQuery('#searchByDigitizationCenter').val();
          
          var boxid = jQuery('#box_id').val();
          var page = jQuery('#page').val();
          var pid = jQuery('#p_id').val();
          // Append to data
          data.searchGeneric = sg;
          data.searchByDocID = docid;
          data.searchByProgramOffice = po;
          data.searchByDigitizationCenter = dc;
          
          data.BoxID = boxid;
          data.PID = pid;
          data.page = page;
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
       { data: 'folderdocinfo_id' },
       <?php
        }
        ?>
       { data: 'folderdocinfo_id_flag' }, 
       { data: 'request_id' },
       { data: 'location' },
       { data: 'acronym' },
       { data: 'validation' },
    ]
  });

  jQuery(document).on('keypress',function(e) {
    if(e.which == 13) {
        dataTable.draw();
    }
});

  jQuery("#searchByProgramOffice").change(function(){
    dataTable.draw();
});

  jQuery("#searchByDigitizationCenter").change(function(){
    dataTable.draw();
});

jQuery('#searchGeneric').on('input keyup paste', function () {
    var hasValue = jQuery.trim(this.value).length;
    if(hasValue == 0) {
            dataTable.draw();
        }
});


		function onAddTag(tag) {
			dataTable.draw();
		}
		function onRemoveTag(tag) {
			dataTable.draw();
		}

<?php		
// BEGIN ADMIN BUTTONS
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
?>
//reprint labels button		
jQuery('#wpsc_individual_label_btn').on('click', function(e){
     var form = this;
     var rows_selected = dataTable.column(0).checkboxes.selected();
     var arr = {};
    // Loop through array
    [].forEach.call(rows_selected, function(inst){
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
      if(!alert(response)){dataTable.ajax.reload( null, false );}
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
      if(!alert(response)){
       var substring = "removed";
       dataTable.ajax.reload( null, false );
       
       if(response.indexOf(substring) !== -1) {
       jQuery('#ud_alert').hide();
       } else {
       jQuery('#ud_alert').show(); 
       }
       
      }
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
      if(!alert(response)){
       var substring = "removed";
       dataTable.ajax.reload( null, false );
       
       if(response.indexOf(substring) !== -1) {
       jQuery('#ud_alert').hide();
       } else {
       jQuery('#ud_alert').show(); 
       }
       
      }
   });
});
<?php
}
// END ADMIN BUTTONS
?>

jQuery("#searchByDocID").tagsInput({
   'defaultText':'',
   'onAddTag': onAddTag,
   'onRemoveTag': onRemoveTag,
   'width':'100%'
});

jQuery("#searchByDocID_tag").on('paste',function(e){
    var element=this;
    setTimeout(function () {
        var text = jQuery(element).val();
        var target=jQuery("#searchByDocID");
        var tags = (text).split(/[ ,]+/);
        for (var i = 0, z = tags.length; i<z; i++) {
              var tag = jQuery.trim(tags[i]);
              if (!target.tagExist(tag)) {
                    target.addTag(tag);
              }
              else
              {
                  jQuery("#searchByDocID_tag").val('');
              }
                
         }
    }, 0);
});

});

</script>


  </div>
 


</div>
</div>
</div>
