<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$subfolder_path = site_url( '', 'relative');

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
<?php
			$folderfile_details = $wpdb->get_row(
				"SELECT *
            FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '" . $GLOBALS['id'] . "'"
			);

            $folderfile_id = $folderfile_details->id;
            $folderfile_index_level = $folderfile_details->index_level;
			$folderfile_boxid = $folderfile_details->box_id;
			$folderfile_title = $folderfile_details->title;
			$folderfile_date = $folderfile_details->date;
			$folderfile_author = $folderfile_details->author;
			$folderfile_record_type = $folderfile_details->record_type;
			$folderfile_site_name = $folderfile_details->site_name;
			$folderfile_site_id = $folderfile_details->site_id;
			$folderfile_close_date = $folderfile_details->close_date;
			$folderfile_epa_contact_email = $folderfile_details->epa_contact_email;
			$folderfile_access_type = $folderfile_details->access_type;
			$folderfile_source_format = $folderfile_details->source_format;
			$folderfile_rights = $folderfile_details->rights;
			//$folderfile_contract_number = $folderfile_details->contract_number;
			//$folderfile_grant_number = $folderfile_details->grant_number;
			$folderfile_file_location = $folderfile_details->file_location;
			$folderfile_file_name = $folderfile_details->file_name;
			$folderfile_folderdocinfo_id = $folderfile_details->folderdocinfo_id;
			
			$folderfile_essential_record = $folderfile_details->essential_record;
			$folderfile_validation = $folderfile_details->validation;
			$folderfile_validation_user = $folderfile_details->validation_user_id;				
		    $folderfile_destruction = $folderfile_details->unauthorized_destruction;
		    $folderfile_identifier = $folderfile_details->folder_identifier;
		    $folderfile_freeze = $folderfile_details->freeze;

            $user = get_user_by( 'id', $folderfile_validation_user);
            
		    $box_details = $wpdb->get_row("SELECT wpqa_wpsc_epa_boxinfo.id, wpqa_wpsc_epa_boxinfo.box_destroyed, wpqa_wpsc_ticket.request_id as request_id, wpqa_wpsc_epa_boxinfo.box_id as box_id, wpqa_wpsc_epa_boxinfo.ticket_id as ticket_id
FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_folderdocinfo, wpqa_wpsc_ticket
WHERE wpqa_wpsc_ticket.id = wpqa_wpsc_epa_boxinfo.ticket_id AND wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id AND wpqa_wpsc_epa_boxinfo.id = '" . $folderfile_boxid . "'");
            $box_boxid = $box_details->box_id;
			$box_ticketid = $box_details->ticket_id;
			$box_requestid = $box_details->request_id;
			$box_destruction = $box_details->box_destroyed;
			$request_id = substr($box_boxid, 0, 7);
            
            //record schedule
            $box_record_schedule = $wpdb->get_row("SELECT wpqa_epa_record_schedule.Record_Schedule_Number as rsnum FROM wpqa_wpsc_epa_boxinfo, wpqa_epa_record_schedule WHERE wpqa_epa_record_schedule.id = wpqa_wpsc_epa_boxinfo.record_schedule_id AND wpqa_wpsc_epa_boxinfo.id = '" . $folderfile_boxid . "'");
            $box_rs = $box_record_schedule->rsnum;
            
            //program office
            $box_program_office = $wpdb->get_row("SELECT wpqa_wpsc_epa_program_office.office_acronym as program_office FROM wpqa_wpsc_epa_program_office, wpqa_wpsc_epa_boxinfo WHERE wpqa_wpsc_epa_program_office.office_code = wpqa_wpsc_epa_boxinfo.program_office_id AND wpqa_wpsc_epa_boxinfo.id = '" . $folderfile_boxid . "'");
            $box_po = $box_program_office->program_office;
            
            //box location
            $location = $wpdb->get_row("SELECT wpqa_terms.name as location, wpqa_wpsc_epa_storage_location.aisle as aisle, wpqa_wpsc_epa_storage_location.bay as bay, wpqa_wpsc_epa_storage_location.shelf as shelf, wpqa_wpsc_epa_storage_location.position as position
FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_folderdocinfo, wpqa_wpsc_epa_storage_location, wpqa_terms
WHERE wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center AND wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id AND wpqa_wpsc_epa_storage_location.id = wpqa_wpsc_epa_boxinfo.storage_location_id AND wpqa_wpsc_epa_boxinfo.id = '" . $folderfile_boxid . "'");
            $box_location = $location->location;
			$box_aisle = $location->aisle;
			$box_bay = $location->bay;
			$box_shelf = $location->shelf;
			$box_position = $location->position;
?>
<style>
.bootstrap-iso .alert {
    padding: 8px;
}
</style>
<?php
			if ($folderfile_index_level == '1') {
?>
  <h3>Folder Details</h3>
<?php
			} else {
?>
  <h3>File Details</h3>
<?php
			}
?>

 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
	<div class="col-sm-12">
    	<button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=wpsc-tickets';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
    	
    	<?php		
        if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
        {
        ?>
        <button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_validation_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-check-circle"></i> Validate</button></button>
    	<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_destruction_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-flag"></i> Unauthorize Destruction</button></button>
    	<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_freeze_btn" style="<?php echo $action_default_btn_css?>"><i class="fas fa-snowflake"></i> Freeze</button></button>
        <?php
        }
        ?>	
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'requestdetails') {
?>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="location.href='admin.php?page=boxdetails&pid=requestdetails&id=<?php echo $box_boxid ?>';" style="<?php echo $action_default_btn_css?>"><i class="fas fa-chevron-circle-left"></"></i> Back to Box Details</button>
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'boxsearch') {
?>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="location.href='admin.php?page=boxdetails&pid=requestdetails&id=<?php echo $box_boxid ?>';" style="<?php echo $action_default_btn_css?>"><i class="fas fa-chevron-circle-left"></"></i> Back to Box Details</button>
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'docsearch') {
?>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="location.href='admin.php?page=folderfile';" style="<?php echo $action_default_btn_css?>"><i class="fas fa-chevron-circle-left"></"></i> Back to Folder/File Dashboard</button>
<?php
}
?>   	
  </div>
	
</div>

<div class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">

<!--only appears if document is marked as unauthorized destruction-->
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id'])) {
?>
  <div class="col-sm-8 col-md-9 wpsc_it_body">
    <div class="row wpsc_it_subject_widget">
<?php
if($folderfile_destruction > 0){
?>
<div class="alert alert-danger" role="alert">
<span style="font-size: 1em; color: #8b0000;"><i class="fas fa-flag" title="Unauthorized Destruction"></i></span> This 
<?php if ($folderfile_index_level == '1') {?>folder <?php }else{ ?>file <?php } ?>
is flagged as unauthorized destruction.
</div>
<?php
}
?>

<!--only appears if document is marked as frozen-->
<?php
if($folderfile_freeze > 0){
?>
<div class="alert alert-info" role="alert">
<span style="font-size: 1em; color: #009ACD;"><i class="fas fa-snowflake" title="Freeze"></i></span> This 
<?php if ($folderfile_index_level == '1') {?>folder <?php }else{ ?>file <?php } ?>
is marked as frozen.
</div>
<?php
}
?>

<?php
if($folderfile_validation > 0){
echo '
<div class="alert alert-success" role="alert">
<span style="font-size: 1.3em; color: #008000;"><i class="fas fa-check-circle" title="Validated"></i></span>';
if ($folderfile_index_level == '1') { echo' Folder validated ('.$user->user_login.').'; }else{ echo' File validated ('.$user->user_login.').'; }
echo '</div>';
} else {
echo '
<div class="alert alert-danger" role="alert">
<span style="font-size: 1.3em; color: #8b0000;"><i class="fas fa-times-circle" title="not validated"></i></span>';
if ($folderfile_index_level == '1') { echo' Folder not validated.'; }else{ echo' File not validated.'; }
echo '</div>';
}
?>
        
      <h3>
	 	 <?php if(apply_filters('wpsc_show_hide_ticket_subject',true)){?>
	 	 <?php if($box_destruction > 0 && $folderfile_freeze == 0){?>
	 	 <span style="color: #FF0000 !important; text-decoration: line-through;">
	 	 <?php } ?>
        	<?php if ($folderfile_index_level == '1') {?>[Folder ID #<?php }else{ ?>[File ID #<?php } ?> <?php
            echo $GLOBALS['id'];
            ?>]<?php if($box_destruction > 0 && $folderfile_freeze == 0){?></span> <span style="font-size: .8em; color: #FF0000;"><i class="fas fa-ban" title="Box Destroyed"></i></span><?php } ?>
		  <?php } ?>		
		  
		  <?php 
		  $agent_permissions = $wpscfunction->get_current_agent_permissions();
          $agent_permissions['label'];
		  if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
			         echo '<a href="#" onclick="wpsc_get_folderfile_editor(' . $folderfile_id . ')"><i class="fas fa-edit fa-xs"></i></a>';
			    }
		  ?>
      </h3>

    </div>

<?php
			if(!empty($box_po)) {
			    echo "<strong>Program Office:</strong> " . $box_po . "<br />";
			}
			else {
			    echo "<strong style='color:red'>Program Office: REASSIGN IMMEDIATELY</strong> <br />";
			}
            
            if(!empty($box_rs)) {
                echo "<strong>Record Schedule:</strong> " . $box_rs ."<br />";
            }
            else {
			    echo "<strong style='color:red'>Record Schedule: REASSIGN IMMEDIATELY</strong> <br />";
			}
  
  			if (!empty($folderfile_title)) {
				echo "<strong>Title:</strong> " . $folderfile_title . "<br />";
			}

			if (!empty($folderfile_date)) {
				echo "<strong>Date:</strong> " . $folderfile_date . "<br />";
			}
			if (!empty($folderfile_author)) {
				echo "<strong>Author:</strong> " . $folderfile_author . "<br />";
			}
			if (!empty($folderfile_record_type)) {
				echo "<strong>Record Type:</strong> " . $folderfile_record_type . "<br />";
			}
			if (!empty($folderfile_site_name)) {
				echo "<strong>Site Name:</strong> " . $folderfile_site_name . "<br />";
			}
			if (!empty($folderfile_site_id)) {
				echo "<strong>Site ID #:</strong> " . $folderfile_site_id . "<br />";
			}
			if (!empty($folderfile_close_date)) {
				echo "<strong>Close Date:</strong> " . $folderfile_close_date . "<br />";
			}
			if (!empty($folderfile_epa_contact_email)) {
				echo "<strong>Contact Email:</strong> " . $folderfile_epa_contact_email . "<br />";
			}
			if (!empty($folderfile_access_type)) {
				echo "<strong>Access Type:</strong> " . $folderfile_access_type . "<br />";
			}
			if (!empty($folderfile_source_format)) {
				echo "<strong>Source Format:</strong> " . $folderfile_source_format . "<br />";
			}
			if (!empty($folderfile_rights)) {
				echo "<strong>Rights:</strong> " . $folderfile_rights . "<br />";
			}
			/*if (!empty($folderfile_contract_number)) {
				echo "<strong>Contract #:</strong> " . $folderfile_contract_number . "<br />";
			}
			if (!empty($folderfile_grant_number)) {
				echo "<strong>Grant #:</strong> " . $folderfile_grant_number . "<br />";
			}*/
			
			if(!empty($folderfile_identifier)) {
			    echo "<strong>Folder Identifier:</strong> " . $folderfile_identifier . "<br />";
			}
			
			if($folderfile_essential_record == 1) {
			    echo "<strong>Essential Record:</strong> Yes <br />";
			}
			
			if (!empty($folderfile_file_location) || !empty($folderfile_file_name)) {
				echo '<strong>Link to File:</strong> <a href="' . $folderfile_file_location . '" target="_blank">' . $folderfile_file_name . '</a><br />';
			}
			
?>
<form>
<input type='hidden' id='doc_id' value='<?php echo $GLOBALS['id']; ?>' />
<input type='hidden' id='page' value='<?php echo $GLOBALS['page']; ?>' />
<input type='hidden' id='p_id' value='<?php echo $GLOBALS['p_id']; ?>' />
</form>
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

<br />

<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>
<script>
 jQuery(document).ready(function() {

<?php
// BEGIN ADMIN BUTTONS
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
?>
jQuery('#wpsc_individual_validation_btn').on('click', function(e){
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_validate.php',{
postvarsfolderdocid : jQuery('#doc_id').val(),
postvarsuserid : <?php $user_ID = get_current_user_id(); echo $user_ID; ?>,
postvarpage : jQuery('#page').val()
}, 
   function (response) {
      //if(!alert(response)){window.location.reload();}
      wpsc_modal_open('Validation');
		  var data = {
		    action: 'wpsc_get_validate_ffd',
		    response_data: response,
		    response_page: '<?php echo $GLOBALS['page']; ?>'
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  }); 
   });
});

jQuery('#wpsc_individual_destruction_btn').on('click', function(e){
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_unauthorize_destruction.php',{
postvarsfolderdocid : jQuery('#doc_id').val(),
postvarpage : jQuery('#page').val()
}, 
   function (response) {
       wpsc_modal_open('Unauthorized Destruction');
		  var data = {
		    action: 'wpsc_unauthorized_destruction_ffd',
		    response_data: response,
		    response_page: '<?php echo $GLOBALS['page']; ?>'
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  }); 
   });
});

jQuery('#wpsc_individual_freeze_btn').on('click', function(e){
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_freeze.php',{
postvarsfolderdocid : jQuery('#doc_id').val(),
postvarpage : jQuery('#page').val()
}, 
   function (response) {
      //if(!alert(response)){window.location.reload();}
      //window.location.replace("<?php echo $subfolder_path; ?>/wp-admin/admin.php?pid=<?php echo $GLOBALS['pid']; ?>&page=filedetails&id=<?php echo $GLOBALS['id']; ?>");
      wpsc_modal_open('Freeze');
		  var data = {
		    action: 'wpsc_get_freeze_ffd',
		    response_data: response,
		    response_page: '<?php echo $GLOBALS['page']; ?>'
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  }); 
   });
});
<?php
}
// END ADMIN BUTTONS
?>	

	 jQuery('#toplevel_page_wpsc-tickets').removeClass('wp-not-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-has-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-menu-open'); 
	 jQuery('#toplevel_page_wpsc-tickets a:first').removeClass('wp-not-current-submenu');
	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-has-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-menu-open');
	 jQuery('#menu-dashboard').removeClass('current');
	 jQuery('#menu-dashboard a:first').removeClass('current');

<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'requestdetails') {
?>
	 jQuery('.wp-first-item').addClass('current'); 
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'boxsearch') {
?>
	 jQuery('.wp-submenu li:nth-child(3)').addClass('current');
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'docsearch') {
?>
	 jQuery('.wp-submenu li:nth-child(4)').addClass('current');
<?php
}
?>
} );

		function wpsc_get_folderfile_editor(doc_id){
<?php
			$box_il_val = '';
			if ($box_il == '1') {
?>
		  wpsc_modal_open('Edit Folder Metadata');
<?php
			} else {
?>
		  wpsc_modal_open('Edit File Metadata');
<?php
			}
?>

		  var data = {
		    action: 'wpsc_get_folderfile_editor',
		    doc_id: doc_id
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
 
	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">

							<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					      <h4 class="widget_header"><i class="fa fa-arrow-circle-right"></i> Location
					      </h4>
								<hr class="widget_divider">
								<div class="wpsp_sidebar_labels"><strong>Request ID:</strong> 
	                            <?php 
	                                echo "<a href='admin.php?page=wpsc-tickets&id=" . $box_requestid . "'>" . $box_requestid . "</a>";
	                            ?>
	                            </div>
	                            <div class="wpsp_sidebar_labels"><strong>Box ID:</strong> 
	                            <?php 
	                            if (!empty($box_boxid)) {
	                                if ($GLOBALS['pid'] == 'requestdetails') {
	                                echo "<a href='admin.php?pid=requestdetails&page=boxdetails&id=" . $box_boxid . "'>" . $box_boxid . "</a>";
	                                }
	                                if ($GLOBALS['pid'] == 'boxsearch') {
	                                echo "<a href='admin.php?pid=boxsearch&page=boxdetails&id=" . $box_boxid . "'>" . $box_boxid . "</a>";
	                                }
	                                if ($GLOBALS['pid'] == 'docsearch') {
	                                echo "<a href='admin.php?pid=docsearch&page=boxdetails&id=" . $box_boxid . "'>" . $box_boxid . "</a>";
	                                }
	                                } ?>
	                            </div>
	                            <?php
	                            //if digitization_center field is empty, will not display location on front end
	                            if(!empty($box_location)) {
	                            echo '<div class="wpsp_sidebar_labels"><strong>Digitization Center: </strong>';
	                            echo $box_location . "<br />";
	                                //if aisle/bay/shelf/position <= 0, does not display location on front end
    	                            if(!($box_aisle <= 0 && $box_bay <= 0 && $box_shelf <= 0 && $box_position <= 0))
    								{
        								echo '<div class="wpsp_sidebar_labels"><strong>Aisle: </strong>';
        	                            echo $box_aisle . "<br />";
        	                            echo '</div>';
        								echo '<div class="wpsp_sidebar_labels"><strong>Bay: </strong>';
        	                            echo $box_bay . "<br />";
        	                            echo '</div>';
        								echo '<div class="wpsp_sidebar_labels"><strong>Shelf: </strong>';
        	                            echo $box_shelf . "<br />";
        								echo '</div>';
        								echo '<div class="wpsp_sidebar_labels"><strong>Position: </strong>';
        	                            echo $box_position . "<br />";
        	                            echo '</div>';
    								}
	                            }
	                            ?> 
	                            </div>
			    		</div>
	
	</div>
<?php
} else {

echo '<span style="padding-left: 10px">Please pass a valid Folder/File ID</span>';

}
?>
</div>
</div>
</div>
