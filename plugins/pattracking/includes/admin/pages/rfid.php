<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$GLOBALS['id'] = $_GET['id'];

//include_once WPPATT_ABSPATH . 'includes/class-wppatt-functions.php';
//$load_styles = new wppatt_Functions();
//$load_styles->addStyles();

$general_appearance = get_option('wpsc_appearance_general_settings');

$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';

			$rfid_count = $wpdb->get_row(
				"SELECT count(id) as count
            FROM wpqa_wpsc_epa_rfid_data"
			);

    $rfid_count_num = $rfid_count->count;
?>


<div class="bootstrap-iso">
   
  <h3>RFID Dashboard</h3>
  
 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
  <div class="col-sm-12">
    	<button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=wpsc-tickets';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="window.location.reload();" style="<?php echo $action_default_btn_css?> margin-right: 30px !important;"><i class="fas fa-retweet"></i> <?php _e('Reset Filters','supportcandy')?></button>
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_clear_rfid_btn" onclick="wpsc_clear_rfid();" style="<?php echo $action_default_btn_css?>"><i class="fas fa-eraser"></i> Clear by RFID Reader ID</button>
        <button type="button" class="btn btn-sm wpsc_action_btn" id="editselectedbox"><i class="fas fa-edit"></i> Edit Selected Boxes</button>
  </div>

</div>

<div class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">

	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">

							<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					      <h4 class="widget_header"><i class="fa fa-filter"></i> Filters <a href="#" aria-label="Filter" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo Patt_Custom_Func::helptext_tooltip('help-filters'); ?>"><i class="far fa-question-circle"></i></a>
								</h4>
								<hr class="widget_divider">

	                            <div class="wpsp_sidebar_labels">
Enter one or more Box IDs:<br />
         <input type='text' id='searchByBoxID' class="form-control" data-role="tagsinput">
<br />

         <?php
 $rfid_details = $wpdb->get_results("
SELECT DISTINCT
Reader_Name
FROM wpqa_wpsc_epa_rfid_data
");

			$rfid_readerid_array = array();
			
			foreach ($rfid_details as $info) {
				$readerid = $info->Reader_Name;
				array_push($rfid_readerid_array, $readerid);
			}
			
			?>
<select id='searchByReaderID'>
     <option value=''>-- Select RFID Reader --</option>
     <?php foreach($rfid_readerid_array as $key => $value) { ?>
      <option value='<?php echo $value; ?>'><?php echo $value; ?></option>
     <?php } ?></select>

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
</style>

<div class="table-responsive" style="overflow-x:auto;">
<input type="text" id="searchGeneric" class="form-control" name="custom_filter[s]" value="" autocomplete="off" placeholder="Search...">
<i class="fa fa-search wpsc_search_btn wpsc_search_btn_sarch"></i>
<br /><br />
<form id="frm-example" method="POST">
<table id="tbl_templates_boxes" class="display nowrap" cellspacing="5" cellpadding="5" width="100%">
        <thead>
            <tr>
                <th class="datatable_header"></th>
                <th class="datatable_header">Reader ID</th>
                <th class="datatable_header">Box ID</th>
                <th class="datatable_header">Request ID</th>
                <th class="datatable_header">EPC</th>
                <th class="datatable_header">Date Added</th>
            </tr>
        </thead>
    </table>
<br /><br />
</form>
<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>

<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.css" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.js" crossorigin="anonymous"></script>
  
  
<script>

jQuery(document).ready(function(){
    jQuery('[data-toggle="tooltip"]').tooltip(); 
  var dataTable = jQuery('#tbl_templates_boxes').DataTable({
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'scrollX' : true,
    'paging' : true,
    'searching': false, // Remove default Search Control
    'ajax': {
       'url':'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/rfid_processing.php',
       'data': function(data){
          // Read values
          var sg = jQuery('#searchGeneric').val();
          var boxid = jQuery('#searchByBoxID').val();
          var readerid = jQuery('#searchByReaderID').val();
          // Append to data
          data.searchGeneric = sg;
          data.searchByBoxID = boxid;
          data.searchByReaderID = readerid;
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
       { data: 'box_id' },
       { data: 'Reader_Name' }, 
       { data: 'box_id' },
       { data: 'request_id' },
       { data: 'epc' },
       { data: 'DateAdded' },
    ]
  });
  
  setInterval( function () {
    dataTable.ajax.reload( null, false ); // user paging is not reset on reload

if(dataTable.data().length !== 0) {
show_rfid_clear();
} else {
hide_rfid_clear();
}

var check = jQuery('#tbl_templates_boxes').find('input[type=checkbox]:checked').length;
if (check>0) {
enable_rfid_button();
}else{
disable_rfid_button();
}

}, 1000 );

jQuery('#tbl_templates_boxes_processing').remove();

  jQuery(document).on('keypress',function(e) {
    if(e.which == 13) {
        dataTable.draw();
    }
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


// Handle form submission event 
   jQuery('#editselectedbox').on('click', function(e){
      var form = this;
      
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
		    postvarsboxid : rows_selected.join(",")
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
        jQuery('#wpsc_clear_rfid_btn').hide();
        
		function show_rfid_clear(){
        jQuery('#wpsc_clear_rfid_btn').show();
		}
		
		function hide_rfid_clear(){
		jQuery('#wpsc_clear_rfid_btn').hide();
		}
		
		jQuery('#editselectedbox').attr('disabled', 'disabled');
        
		function enable_rfid_button(){
        jQuery('#editselectedbox').removeAttr('disabled');
		}
		
		function disable_rfid_button(){
		jQuery('#editselectedbox').attr('disabled', 'disabled');
		}
		
		function wpsc_clear_rfid(){

		  wpsc_modal_open('Clear Scanned Boxes by RFID Reader ID');
		  var data = {
		    action: 'wpsc_get_clear_rfid'
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