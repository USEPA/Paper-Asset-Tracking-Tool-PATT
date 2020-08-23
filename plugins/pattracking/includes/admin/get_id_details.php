<?php
// Code to add ID lookup
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly

}

global $current_user, $wpscfunction, $wpdb;

$subfolder_path = site_url( '', 'relative');

$GLOBALS['id'] = $_GET['id'];

$id = $GLOBALS['id'];
$dash_count = substr_count($id, '-');

wp_enqueue_script('jquery');

wp_register_script('dataTables-js', 'https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js', '', '', true);
wp_register_script('dataTables-responsive-js', 'https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js', '', '', true);
wp_register_script('customScriptDatatables', plugins_url('js/customScriptDatatables.js', __FILE__, '', true));


wp_enqueue_script('dataTables-js');
wp_enqueue_script('dataTables-responsive-js');
wp_enqueue_script('customScriptDatatables');
wp_enqueue_style('wpsc-fa-css', WPSC_PLUGIN_URL.'asset/lib/font-awesome/css/all.css?version='.WPSC_VERSION );

echo '<link rel="stylesheet" type="text/css" href="' . WPSC_PLUGIN_URL . 'asset/lib/DataTables/datatables.min.css"/>';
echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css"/>';

$ticket_id_val = substr($id, 0, 7);

$program_office_array_id = array();

$boxlist_get_po = $wpdb->get_results(
	"SELECT DISTINCT wpqa_wpsc_epa_program_office.office_acronym as program_office
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_program_office ON wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.office_code
WHERE wpqa_wpsc_epa_boxinfo.ticket_id = " . $ticket_id_val
);

foreach ($boxlist_get_po as $item) {
	array_push($program_office_array_id, $item->program_office);
	}
	
$boxlist_po = join(", ", $program_office_array_id);

if (preg_match("/^[0-9]{7}$/", $id) || preg_match("/^[0-9]{7}-[0-9]{1,3}$/", $id) || preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $id)) {
	switch ($dash_count) {
		case 0:
			$request_info = $wpdb->get_row(
				"SELECT wpqa_wpsc_ticket.id as id, wpqa_wpsc_ticket.customer_name as customer_name, wpqa_wpsc_ticket.customer_email as customer_email, status.name as ticket_status, status.term_id as ticket_status_id, wpqa_terms.name as ticket_location, priority.name as ticket_priority, priority.term_id as ticket_priority_id, wpqa_wpsc_ticket.date_created as date_created
FROM wpqa_wpsc_ticket

    INNER JOIN wpqa_terms AS status ON (
        wpqa_wpsc_ticket.ticket_status = status.term_id
    )
INNER JOIN wpqa_wpsc_epa_boxinfo ON wpqa_wpsc_epa_boxinfo.ticket_id = wpqa_wpsc_ticket.id
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
INNER JOIN wpqa_wpsc_epa_location_status ON wpqa_wpsc_epa_boxinfo.location_status_id = wpqa_wpsc_epa_location_status.id
INNER JOIN wpqa_terms ON  wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center

    INNER JOIN wpqa_terms AS priority ON (
        wpqa_wpsc_ticket.ticket_priority = priority.term_id
    )  

WHERE wpqa_wpsc_ticket.request_id = " . $id
			);
			
			$status_color = get_term_meta($request_info->ticket_status_id,'wpsc_status_background_color',true);
            $priority_color = get_term_meta($request_info->ticket_priority_id,'wpsc_priority_background_color',true);
			echo "<h3>Request</h3>";
			echo "<strong>Request ID:</strong> " . $id . "<br />";
			echo "<strong>Requestor Name:</strong> " . $request_info->customer_name . "<br />";
			echo "<strong>Requestor Email:</strong> " . $request_info->customer_email . "<br />";
			echo "<strong>Request Status:</strong> " . $request_info->ticket_status . "  <span style='color: ".$status_color." ;margin: 0px;'><i class='fas fa-circle'></i></span>
<br />";
			echo "<strong>Priority:</strong> " . $request_info->ticket_priority . "  <span style='color: ".$priority_color." ;margin: 0px;'><i class='fas fa-asterisk'></i></span><br />";
			echo "<strong>Date Created:</strong> " . date("m-d-Y", strtotime($request_info->date_created));

			$box_details = $wpdb->get_results(
				"
SELECT wpqa_wpsc_epa_boxinfo.id as id, wpqa_wpsc_epa_boxinfo.id as box_data_id, wpqa_wpsc_epa_boxinfo.box_id as box_id, 
wpqa_terms.name as digitization_center,
wpqa_wpsc_epa_storage_location.aisle as aisle, wpqa_wpsc_epa_storage_location.bay as bay, wpqa_wpsc_epa_storage_location.shelf as shelf, wpqa_wpsc_epa_storage_location.position as position, wpqa_wpsc_epa_location_status.locations as physical_location,
(SELECT wpqa_terms.name FROM wpqa_wpsc_epa_boxinfo, wpqa_terms WHERE wpqa_wpsc_epa_boxinfo.box_status = wpqa_terms.term_id AND wpqa_wpsc_epa_boxinfo.id = box_data_id) as status,
wpqa_wpsc_epa_program_office.office_acronym as program_office,
wpqa_epa_record_schedule.Record_Schedule_Number as record_schedule
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
INNER JOIN wpqa_wpsc_epa_location_status ON wpqa_wpsc_epa_boxinfo.location_status_id = wpqa_wpsc_epa_location_status.id
INNER JOIN wpqa_terms ON  wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center
INNER JOIN wpqa_wpsc_epa_program_office ON wpqa_wpsc_epa_program_office.office_code = wpqa_wpsc_epa_boxinfo.program_office_id
INNER JOIN wpqa_epa_record_schedule ON wpqa_epa_record_schedule.id = wpqa_wpsc_epa_boxinfo.record_schedule_id

WHERE wpqa_wpsc_epa_boxinfo.ticket_id = " . $request_info->id
			);

			$tbl = '<br /><br /><strong>Boxes associated with this request:</strong>
<style>
#datatablearea input[type="search"] {padding: 0px; margin-bottom:10px; }
#datatablearea { font-size:16px;}
</style>

<span id="datatablearea">
<table id="dataTable">
<thead>
  <tr>
    <th></th>
    <th>ID</th>
    <th>Digitization Center</th>
    <th class="desktop">Physical Location</th>
    <th class="desktop">Shelf Location</th>
    <th class="desktop">Box Status</th>
    <th class="desktop">Program Office</th>
    <th class="desktop">Record Schedule</th>
  </tr>
 </thead><tbody>
';

			foreach ($box_details as $info) {
				$boxlist_id = $info->box_id;
				$boxlist_location = $info->digitization_center;
				if ($boxlist_location == 'East') {
					$boxlist_location_val = "E";
				} else {
					$boxlist_location_val = "W";
				}

			    if (($info->aisle == '0') || ($info->bay == '0') || ($info->shelf == '0') || ($info->position == '0')) {
				$boxlist_shelf_location = 'Currently Unassigned';
				} else {
                $boxlist_shelf_location = $info->aisle . 'A_' .$info->bay .'B_' . $info->shelf . 'S_' . $info->position .'P_'.$boxlist_location_val;
				}
				
				$boxlist_box_status = $info->status;
                $boxlist_physical_location = $info->physical_location;
                $boxlist_program_office = $info->program_office;
                $boxlist_record_schedule = $info->record_schedule;
                
				$tbl .= '
    <tr>
            <td></td>
            <td><a href="'.$subfolder_path.'/index.php/data?id=' . $boxlist_id . '">' . $boxlist_id . '</a></td>
            <td>' . $boxlist_location . '</td>
            <td>' . $boxlist_physical_location . '</td>
            <td>' . $boxlist_shelf_location . '</td>
            <td>' . $boxlist_box_status . '</td>
            <td>' . $boxlist_program_office . '</td>
            <td>' . $boxlist_record_schedule . '</td>
            </tr>
            ';
			}
			$tbl .= '</tbody></table></span>';

			echo $tbl;
			break;

		case 1:
			$box_details = $wpdb->get_row(
				"
SELECT 
wpqa_wpsc_epa_boxinfo.id as pk, 
wpqa_wpsc_epa_boxinfo.id as box_data_id, 
wpqa_wpsc_ticket.request_id as ticket,
wpqa_wpsc_epa_boxinfo.box_id as id, 
wpqa_wpsc_epa_folderdocinfo.index_level as index_level,
wpqa_terms.name as location, 
wpqa_wpsc_epa_storage_location.aisle as aisle, 
wpqa_wpsc_epa_storage_location.bay as bay, 
wpqa_wpsc_epa_storage_location.shelf as shelf, 
wpqa_wpsc_epa_storage_location.position as position, 
wpqa_wpsc_epa_location_status.locations as physical_location,
wpqa_epa_record_schedule.Record_Schedule_Number as rsnum,
(SELECT wpqa_terms.name as box_status FROM wpqa_terms, wpqa_wpsc_epa_boxinfo WHERE wpqa_wpsc_epa_boxinfo.box_status = wpqa_terms.term_id AND wpqa_wpsc_epa_boxinfo.id = box_data_id) as box_status
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_folderdocinfo ON wpqa_wpsc_epa_boxinfo.id = wpqa_wpsc_epa_folderdocinfo.box_id
INNER JOIN wpqa_wpsc_ticket ON wpqa_wpsc_epa_boxinfo.ticket_id = wpqa_wpsc_ticket.id 
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
INNER JOIN wpqa_wpsc_epa_location_status ON wpqa_wpsc_epa_boxinfo.location_status_id = wpqa_wpsc_epa_location_status.id
INNER JOIN wpqa_terms ON  wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center
INNER JOIN wpqa_epa_record_schedule ON wpqa_wpsc_epa_boxinfo.record_schedule_id = wpqa_epa_record_schedule.id

WHERE wpqa_wpsc_epa_boxinfo.box_id = '" . $id . "'"

			);

			$box_details_id = $box_details->pk;

			$box_content = $wpdb->get_results(
				"SELECT wpqa_wpsc_epa_folderdocinfo.folderdocinfo_id as id, wpqa_wpsc_epa_folderdocinfo.index_level as index_level, wpqa_wpsc_epa_folderdocinfo.title as title, wpqa_wpsc_epa_folderdocinfo.date as date, wpqa_wpsc_epa_folderdocinfo.site_name as site, wpqa_wpsc_epa_folderdocinfo.epa_contact_email as contact, wpqa_wpsc_epa_folderdocinfo.source_format as source_format
FROM wpqa_wpsc_epa_folderdocinfo
INNER JOIN wpqa_wpsc_epa_boxinfo ON wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id
WHERE wpqa_wpsc_epa_folderdocinfo.box_id = '" . $box_details_id . "'"
			);


			$request_id = $box_details->ticket;
                
				$box_details_location = $box_details->location;
				if ($box_details_location == 'East') {
					$box_details_location_val = "E";
				} else {
					$box_details_location_val = "W";
				}
				
			    if (($box_details->aisle == '0') || ($box_details->bay == '0') || ($box_details->shelf == '0') || ($box_details->position == '0')) {
				$box_details_shelf_location = 'Currently Unassigned';
				} else {
                $box_details_shelf_location = $box_details->aisle . 'A_' .$box_details->bay .'B_' . $box_details->shelf . 'S_' . $box_details->position .'P_'.$box_details_location_val;
				}
				
			echo "<h3>Box</h3>";
			echo "<strong>Box ID:</strong> " . $id . "<br />";
			echo "<strong>Program Office:</strong> " . $boxlist_po . "<br />";
			echo "<strong>Record Schedule:</strong> " . $box_details->rsnum . "<br />";
			echo "<strong>Digitization Center:</strong> " . $box_details->location . "</strong><br />";
			echo "<strong>Physical Location:</strong> " . $box_details->physical_location . "<br />";
			echo "<strong>Shelf Location:</strong> " . $box_details_shelf_location . "<br />";
			echo "<strong>Box Status:</strong> " . $box_details->box_status . "<br />";

			$tbl = '<br /><br /><strong>Box Contents:</strong>
<style>
#datatablearea input[type="search"] {padding: 0px; margin-bottom:10px; }
#datatablearea { font-size:16px;}
</style>

<span id="datatablearea">
<table id="dataTable">
<thead>
  <tr>
    <th></th>
    <th>ID</th>
    <th>Title</th>
    <th>Index Level</th>
    <th class="desktop">Date</th>
    <th class="desktop">Contact</th>
  </tr>
 </thead><tbody>
';

			foreach ($box_content as $info) {
				$boxcontent_id = $info->id;
			$boxcontent_il = $info->index_level;
			$boxcontent_il_val = '';
			if ($boxcontent_il == 1) {
				$boxcontent_il_val = "Folder";
			} else {
				$boxcontent_il_val = "File";
			}
			
				$boxcontent_title = $info->title;
				$boxcontent_title_truncated = (strlen($boxcontent_title) > 20) ? substr($boxcontent_title, 0, 20) . '...' : $boxcontent_title;
				$boxcontent_date = $info->date;
				$boxcontent_site = $info->site;
				$boxcontent_contact = $info->contact;
				$boxcontent_sf = $info->source_format;
				$tbl .= '
    <tr>
            <td></td>
            <td><a href="'.$subfolder_path.'/index.php/data?id=' . $boxcontent_id . '">' . $boxcontent_id . '</a></td>
            <td>' . $boxcontent_title_truncated . '</td>
            <td>'. $boxcontent_il_val .'</td>
            <td>' . $boxcontent_date . '</td>
            <td>' . $boxcontent_contact . '</td>
            </tr>
            ';
			}
			$tbl .= '</tbody></table></span>';

			echo $tbl;
			echo "<a href='" . $subfolder_path . "/index.php/data?id=" . $request_id . "'>< Back to Request</a>";
			break;

		case 3:
			$folderfile_details = $wpdb->get_row(
				"SELECT *
            FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '" . $id . "'"
			);

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
			$folderfile_file_location = $folderfile_details->file_location;
			$folderfile_file_name = $folderfile_details->file_name;
			$folderfile_folderid = $folderfile_details->folder_identifier;
			$folderfile_essential_record = $folderfile_details->essential_record;
			$box_details = $wpdb->get_row(
"SELECT wpqa_wpsc_epa_boxinfo.id, 
wpqa_wpsc_epa_boxinfo.box_id as box_id, 
wpqa_wpsc_epa_folderdocinfo.index_level as index_level, 

wpqa_terms.name as location, 
wpqa_wpsc_epa_storage_location.aisle as aisle, 
wpqa_wpsc_epa_storage_location.bay as bay, 
wpqa_wpsc_epa_storage_location.shelf as shelf, 
wpqa_wpsc_epa_storage_location.position as position, 
wpqa_wpsc_epa_location_status.locations as physical_location,

wpqa_epa_record_schedule.Record_Schedule_Number as rsnum, 
wpqa_wpsc_epa_program_office.office_acronym as program_office
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_folderdocinfo ON wpqa_wpsc_epa_boxinfo.id = wpqa_wpsc_epa_folderdocinfo.box_id
INNER JOIN wpqa_epa_record_schedule ON wpqa_wpsc_epa_boxinfo.record_schedule_id = wpqa_epa_record_schedule.id
INNER JOIN wpqa_wpsc_epa_program_office ON wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.office_code
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
INNER JOIN wpqa_terms ON  wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center
INNER JOIN wpqa_wpsc_epa_location_status ON wpqa_wpsc_epa_location_status.id =  wpqa_wpsc_epa_boxinfo.location_status_id
WHERE wpqa_wpsc_epa_boxinfo.id = '" . $folderfile_boxid . "'"
			);

			$box_boxid = $box_details->box_id;
			$box_rs = $box_details->rsnum;
			$box_po = $box_details->program_office;
			$request_id = substr($box_boxid, 0, 7);
			$box_il = $box_details->index_level;
			$box_location = $box_details->location;
			$box_physical_location = $box_details->physical_location;
			
				if ($box_location == 'East') {
					$box_location_val = "E";
				} 
				elseif ($box_location == 'East CUI') {
				    $box_location_val = "ECUI";
				}
				elseif ($box_location == 'West CUI') {
				    $box_location_val = "WCUI";
				}
				else {
					$box_location_val = "W";
				}

			    if (($box_details->aisle == '0') || ($box_details->bay == '0') || ($box_details->shelf == '0') || ($box_details->position == '0')) {
				$box_details_shelf_location = 'Currently Unassigned';
				} else {
                $box_details_shelf_location = $box_details->aisle . 'A_' .$box_details->bay .'B_' . $box_details->shelf . 'S_' . $box_details->position .'P_'.$box_location_val;
				}

			$box_il_val = '';
			if ($box_il == 1) {
				echo "<h3>Folder Information</h3>";
				echo "<strong>Folder ID:</strong> " . $id . "<br />";
			} else {
				echo "<h3>File Information</h3>";
				echo "<strong>File ID:</strong> " . $id . "<br />";
			}

			echo "<strong>Program Office:</strong> " . $box_po . "<br />";
			
			echo "<strong>Record Schedule:</strong> " . $box_rs ."<br />";
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
			if (!empty($folderfile_folderid)) {
				echo "<strong>Folder Identifier:</strong> " . $folderfile_folderid . "<br />";
			}
			
			if($folderfile_essential_record == 1) {
			    echo "<strong>Essential Record:</strong> Yes <br />";
			}
			
			echo "<h4>Location Information</h4>";

			if ($box_il == 1) {
				echo "<strong>This folder is located in the following box:</strong><br />";
			} else {
				echo "<strong>This file is located in the following box:</strong><br />";
			}
			if (!empty($box_boxid)) {
				echo "<strong>Box ID:</strong> <a href='" . $subfolder_path . "/index.php/data?id=" . $box_boxid . "'>" . $box_boxid . "</a><br />";
			}
			if (!empty($box_location)) {
				echo "<strong>Digitization Center:</strong> " . $box_location . "<br />";
			}
			
			if (!empty($box_physical_location)) {
				echo "<strong>Physical Location:</strong> " . $box_physical_location . "<br />";
			}
			
			if (!empty($box_details_shelf_location)) {
				echo "<strong>Shelf Location:</strong> " . $box_details_shelf_location . "<br />";
			}
			if (!empty($folderfile_file_location) || !empty($folderfile_file_name)) {
				echo '<strong>Link to File:</strong> <a href="' . $folderfile_file_location . '" target="_blank">' . $folderfile_file_name . '</a><br />';
			}
			echo "<a href='" . $subfolder_path . "/index.php/data?id=" . $request_id . "'>< Back to Request</a>";
			break;
            //default:
            //echo "Please enter a valid PATT ID";

	}
} else {
	echo "Please enter a valid PATT ID";
}