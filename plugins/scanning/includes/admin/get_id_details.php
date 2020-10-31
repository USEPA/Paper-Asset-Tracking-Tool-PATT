<?php
// Code to add ID lookup
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly

}

global $current_user, $wpscfunction, $wpdb;

$GLOBALS['id'] = $_GET['id'];

$id = $GLOBALS['id'];
$dash_count = substr_count($id, '-');

wp_enqueue_script('jquery');

wp_register_script('dataTables-js', 'https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js', '', '', true);
wp_register_script('customScriptDatatables', plugins_url('js/customScriptDatatables.js', __FILE__, '', true));

wp_enqueue_script('dataTables-js');
wp_enqueue_script('customScriptDatatables');

echo '<link rel="stylesheet" type="text/css" href="' . WPSC_PLUGIN_URL . 'asset/lib/DataTables/datatables.min.css"/>';

$ticket_id_val = substr($id, 0, 7);

$boxlist_get_po = $wpdb->get_row(
	"SELECT DISTINCT wpqa_wpsc_epa_program_office.acronym as program_office
FROM wpqa_wpsc_ticket
INNER JOIN wpqa_wpsc_epa_program_office ON wpqa_wpsc_ticket.program_office_id = wpqa_wpsc_epa_program_office.id
WHERE wpqa_wpsc_ticket.request_id = " . $ticket_id_val
);
$boxlist_po = $boxlist_get_po->program_office;

if (preg_match("/^[0-9]{7}$/", $id) || preg_match("/^[0-9]{7}-[0-9]{1,3}$/", $id) || preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $id)) {
	switch ($dash_count) {
		case 0:
			$request_info = $wpdb->get_row(
				"SELECT wpqa_wpsc_ticket.id as id, wpqa_wpsc_ticket.customer_name as customer_name, wpqa_wpsc_ticket.customer_email as customer_email, status.name as ticket_status, location.name as ticket_location, priority.name as ticket_priority, wpqa_wpsc_ticket.date_created as date_created
FROM wpqa_wpsc_ticket

    INNER JOIN wpqa_terms AS status ON (
        wpqa_wpsc_ticket.ticket_status = status.term_id
    )

    INNER JOIN wpqa_terms AS location ON (
        wpqa_wpsc_ticket.ticket_category = location.term_id
    )
    
    INNER JOIN wpqa_terms AS priority ON (
        wpqa_wpsc_ticket.ticket_priority = priority.term_id
    )    

WHERE wpqa_wpsc_ticket.request_id = " . $id
			);

			echo "<h3>Request</h3>";
			echo "<strong>Request ID:</strong> " . $id . "<br />";
			echo "<strong>Program Office: </strong> " . $boxlist_po . "<br />";
			echo "<strong>Request Handled by Digitization Center " . $request_info->ticket_location . "</strong><br />";
			echo "<strong>Requestor Name:</strong> " . $request_info->customer_name . "<br />";
			echo "<strong>Requestor Email:</strong> " . $request_info->customer_email . "<br />";
			echo "<strong>Status:</strong> " . $request_info->ticket_status . "<br />";
			echo "<strong>Priority:</strong> " . $request_info->ticket_priority . "<br />";
			echo "<strong>Date Created:</strong> " . date("m-d-Y", strtotime($request_info->date_created));

			$box_details = $wpdb->get_results(
				"SELECT wpqa_wpsc_epa_boxinfo.box_id as id, wpqa_wpsc_epa_boxinfo.index_level as index_level, wpqa_wpsc_epa_boxinfo.location as location, wpqa_wpsc_epa_boxinfo.bay as bay, wpqa_wpsc_epa_boxinfo.shelf as shelf
FROM wpqa_wpsc_epa_boxinfo
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
    <th>ID</th>
    <th>Facility</th>
    <th>Bay</th>
    <th>Shelf</th>
    <th>Index Level</th>    
  </tr>
 </thead><tbody>
';

			foreach ($box_details as $info) {
				$boxlist_id = $info->id;
				$boxlist_location = $info->location;
				$boxlist_bay = $info->bay;
				$boxlist_shelf = $info->shelf;
				$boxlist_il = $info->index_level;
				$boxlist_il_val = '';
				if ($boxlist_il == 1) {
					$boxlist_il_val = "Folder";
				} else {
					$boxlist_il_val = "File";
				}

				$tbl .= '
    <tr>
            <td><a href="/wordpress3/data?id=' . $boxlist_id . '">' . $boxlist_id . '</a></td>
            <td>' . $boxlist_location . '</td>
            <td>' . $boxlist_bay . '</td>
            <td>' . $boxlist_shelf . '</td>
            <td>' . $boxlist_il_val . '</td>
            </tr>
            ';
			}
			$tbl .= '</tbody></table></span>';

			echo $tbl;
			break;

		case 1:
			$box_details = $wpdb->get_row(
				"SELECT wpqa_wpsc_epa_boxinfo.id as pk, wpqa_wpsc_epa_boxinfo.ticket_id as ticket, wpqa_wpsc_epa_boxinfo.box_id as id, wpqa_wpsc_epa_boxinfo.index_level as index_level, wpqa_wpsc_epa_boxinfo.location as location, wpqa_wpsc_epa_boxinfo.bay as bay, wpqa_wpsc_epa_boxinfo.shelf as shelf
FROM wpqa_wpsc_epa_boxinfo
WHERE wpqa_wpsc_epa_boxinfo.box_id = '" . $id . "'"
			);

			$box_details_id = $box_details->pk;

			$rs_array_id = array();
			$record_schedules_id = $wpdb->get_results(
				"SELECT DISTINCT wpqa_epa_record_schedule.Record_Schedule_Number as rsnum
            FROM wpqa_wpsc_epa_folderdocinfo
            INNER JOIN wpqa_epa_record_schedule ON wpqa_wpsc_epa_folderdocinfo.record_schedule_id = wpqa_epa_record_schedule.id
            WHERE box_id  = '" . $box_details_id . "'"
			);

			foreach ($record_schedules_id as $item) {
				array_push($rs_array_id, $item->rsnum);
			}

			$rs = join(", ", $rs_array_id);

			$box_content = $wpdb->get_results(
				"SELECT wpqa_wpsc_epa_folderdocinfo.folderdocinfo_id as id, wpqa_wpsc_epa_folderdocinfo.title as title, wpqa_wpsc_epa_folderdocinfo.date as date, wpqa_wpsc_epa_folderdocinfo.site_name as site, wpqa_wpsc_epa_folderdocinfo.epa_contact_email as contact, wpqa_wpsc_epa_folderdocinfo.source_format as source_format, wpqa_epa_record_schedule.Record_Schedule_Number as rsnum
FROM wpqa_wpsc_epa_folderdocinfo
INNER JOIN wpqa_wpsc_epa_boxinfo ON wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id
INNER JOIN wpqa_epa_record_schedule ON wpqa_wpsc_epa_folderdocinfo.record_schedule_id = wpqa_epa_record_schedule.id
WHERE wpqa_wpsc_epa_folderdocinfo.box_id = '" . $box_details_id . "'"
			);

			$str_length = 7;
			$request_id = substr("000000{$box_details->ticket}", -$str_length);

			$boxlist_il = $box_details->index_level;
			$boxlist_il_val = '';
			if ($boxlist_il == 1) {
				$boxlist_il_val = "Folder";
			} else {
				$boxlist_il_val = "File";
			}
			echo "<h3>Box</h3>";
			echo "<strong>Box ID:</strong> " . $id . "<br />";
			echo "<strong>Program Office:</strong> " . $boxlist_po . "<br />";
			echo "<strong>Digitization Center Location:</strong> " . $box_details->location . "</strong><br />";
			echo "<strong>Bay:</strong> " . $box_details->bay . "<br />";
			echo "<strong>Shelf:</strong> " . $box_details->shelf . "<br />";
			echo "<strong>Record Schedule(s):</strong> " . $rs . "<br />";
			echo "<strong>Index Level:</strong>  " . $boxlist_il_val;

			$tbl = '<br /><br /><strong>Box Contents:</strong>
<style>
#datatablearea input[type="search"] {padding: 0px; margin-bottom:10px; }
#datatablearea { font-size:16px;}
</style>

<span id="datatablearea">
<table id="dataTable">
<thead>
  <tr>
    <th>ID</th>
    <th>Title</th>
    <th>Date</th>
    <th>Contact</th>
    <th>Record Schedule #</th>    
  </tr>
 </thead><tbody>
';

			foreach ($box_content as $info) {
				$boxcontent_id = $info->id;
				$boxcontent_title = $info->title;
				$boxcontent_title_truncated = (strlen($boxcontent_title) > 20) ? substr($boxcontent_title, 0, 20) . '...' : $boxcontent_title;
				$boxcontent_date = $info->date;
				$boxcontent_site = $info->site;
				$boxcontent_contact = $info->contact;
				$boxcontent_sf = $info->source_format;
				$boxcontent_rsnum = $info->rsnum;
				$tbl .= '
    <tr>
            <td><a href="/wordpress3/data?id=' . $boxcontent_id . '">' . $boxcontent_id . '</a></td>
            <td>' . $boxcontent_title_truncated . '</td>
            <td>' . $boxcontent_date . '</td>
            <td>' . $boxcontent_contact . '</td>
            <td>' . $boxcontent_rsnum . '</td>
            </tr>
            ';
			}
			$tbl .= '</tbody></table></span>';

			echo $tbl;
			echo "<a href='/wordpress3/data?id=" . $request_id . "'>< Back to Request</a>";
			break;

		case 3:
			$folderfile_details = $wpdb->get_row(
				"SELECT 
            wpqa_wpsc_epa_folderdocinfo.box_id,
            wpqa_wpsc_epa_folderdocinfo.title, 
            wpqa_wpsc_epa_folderdocinfo.date, 
            wpqa_wpsc_epa_folderdocinfo.author, 
            wpqa_wpsc_epa_folderdocinfo.record_type, 
            wpqa_epa_record_schedule.Record_Schedule_Number,
            wpqa_wpsc_epa_folderdocinfo.site_name, 
            wpqa_wpsc_epa_folderdocinfo.site_id, 
            wpqa_wpsc_epa_folderdocinfo.close_date,
            wpqa_wpsc_epa_folderdocinfo.epa_contact_email,
            wpqa_wpsc_epa_folderdocinfo.access_type,
            wpqa_wpsc_epa_folderdocinfo.source_format,
            wpqa_wpsc_epa_folderdocinfo.rights, 
            wpqa_wpsc_epa_folderdocinfo.contract_number,  
            wpqa_wpsc_epa_folderdocinfo.grant_number,
            wpqa_wpsc_epa_folderdocinfo.file_location,
            wpqa_wpsc_epa_folderdocinfo.file_name
            FROM wpqa_wpsc_epa_folderdocinfo
INNER JOIN wpqa_epa_record_schedule ON wpqa_wpsc_epa_folderdocinfo.record_schedule_id = wpqa_epa_record_schedule.id WHERE folderdocinfo_id = '" . $id . "'"
			);

			$folderfile_boxid = $folderfile_details->box_id;
			$folderfile_title = $folderfile_details->title;
			$folderfile_date = $folderfile_details->date;
			$folderfile_author = $folderfile_details->author;
			$folderfile_record_type = $folderfile_details->record_type;
			$folderfile_rsnum = $folderfile_details->Record_Schedule_Number;
			$folderfile_site_name = $folderfile_details->site_name;
			$folderfile_site_id = $folderfile_details->site_id;
			$folderfile_close_date = $folderfile_details->close_date;
			$folderfile_epa_contact_email = $folderfile_details->epa_contact_email;
			$folderfile_access_type = $folderfile_details->access_type;
			$folderfile_source_format = $folderfile_details->source_format;
			$folderfile_rights = $folderfile_details->rights;
			$folderfile_contract_number = $folderfile_details->contract_number;
			$folderfile_grant_number = $folderfile_details->grant_number;
			$folderfile_file_location = $folderfile_details->file_location;
			$folderfile_file_name = $folderfile_details->file_name;
			$box_details = $wpdb->get_row(
				"SELECT id, box_id, index_level, location, bay, shelf
FROM wpqa_wpsc_epa_boxinfo
WHERE id = '" . $folderfile_boxid . "'"
			);

			$box_boxid = $box_details->box_id;
			$request_id = substr($box_boxid, 0, 7);
			$box_il = $box_details->index_level;
			$box_location = $box_details->location;
			$box_bay = $box_details->bay;
			$box_shelf = $box_details->shelf;

			$box_il_val = '';
			if ($box_il == 1) {
				echo "<h3>Folder Information</h3>";
				echo "<strong>Folder ID:</strong> " . $id . "<br />";
			} else {
				echo "<h3>File Information</h3>";
				echo "<strong>File ID:</strong> " . $id . "<br />";
			}

			echo "<strong>Program Office:</strong> " . $boxlist_po . "<br />";
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
			if (!empty($folderfile_contract_number)) {
				echo "<strong>Contract #:</strong> " . $folderfile_contract_number . "<br />";
			}
			if (!empty($folderfile_grant_number)) {
				echo "<strong>Grant #:</strong> " . $folderfile_grant_number;
			}
			echo "<h4>Location Information</h4>";

			if ($box_il == 1) {
				echo "<strong>This folder is located in the following box:</strong><br />";
			} else {
				echo "<strong>This file is located in the following box:</strong><br />";
			}
			if (!empty($box_boxid)) {
				echo "<strong>Box ID:</strong> <a href='/wordpress3/data?id=" . $box_boxid . "'>" . $box_boxid . "</a><br />";
			}
			if (!empty($box_location)) {
				echo "<strong>Digitization Center Location:</strong> " . $box_location . "<br />";
			}
			if (!empty($box_bay)) {
				echo "<strong>Bay:</strong> " . $box_bay . "<br />";
			}
			if (!empty($box_shelf)) {
				echo "<strong>Shelf:</strong> " . $box_shelf . "<br />";
			}
			if (!empty($folderfile_file_location) || !empty($folderfile_file_name)) {
				echo '<strong>Link to File:</strong> <a href="' . $folderfile_file_location . '" target="_blank">' . $folderfile_file_name . '</a><br />';
			}
			echo "<a href='/wordpress3/data?id=" . $request_id . "'>< Back to Request</a>";
			break;
            //default:
            //echo "Please enter a valid PATT ID";

	}
} else {
	echo "Please enter a valid PATT ID";
}