<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsil']) ||
!empty($_POST['postvarstitle']) ||
!empty($_POST['postvarsdate']) ||
!empty($_POST['postvarsauthor']) ||
!empty($_POST['postvarsrt']) ||
!empty($_POST['postvarssn']) ||
!empty($_POST['postvarssid']) ||
!empty($_POST['postvarscd']) ||
!empty($_POST['postvarsce']) ||
!empty($_POST['postvarsat']) ||
!empty($_POST['postvarssf']) ||
//!empty($_POST['postvarsrights']) ||
//!empty($_POST['postvarscn']) ||
//!empty($_POST['postvarsgn']) ||
!empty($_POST['postvarser']) ||
!empty($_POST['postvarsfi']) ||
!empty($_POST['postvarsaddressee'])
){
   $folderfileid = $_POST['postvarsffid'];
   $pattdocid = $_POST['postvarspdid'];
   $il = $_POST['postvarsil'];
   $title = $_POST['postvarstitle'];
   $date = $_POST['postvarsdate'];  
   $author = $_POST['postvarsauthor']; 
   $record_type = $_POST['postvarsrt']; 
   $site_name = $_POST['postvarssn']; 
   $site_id = $_POST['postvarssid']; 
   $close_date = $_POST['postvarscd']; 
   $contact_email = $_POST['postvarsce']; 
   $access_type = $_POST['postvarsat']; 
   $source_format = $_POST['postvarssf'];
   //$rights = $_POST['postvarsrights']; 
   //$contract_number = $_POST['postvarscn']; 
   //$grant_number = $_POST['postvarsgn'];
   $essential_record = $_POST['postvarser'];
   $folder_identifier = $_POST['postvarsfi'];
   $addressee = $_POST['postvarsaddressee'];

$request_id = substr($pattdocid, 0, 7);
$get_ticket_id = $wpdb->get_row("
SELECT id
FROM wpqa_wpsc_ticket
WHERE
request_id = '" . $request_id . "'
");

$ticket_id = $get_ticket_id->id;

$get_folderdocinfo = $wpdb->get_row("
SELECT *
FROM wpqa_wpsc_epa_folderdocinfo
WHERE
folderdocinfo_id = '" . $pattdocid . "'
");

   $old_ilval = $get_folderdocinfo->index_level;
   if ($get_folderdocinfo->title == '' ) { $old_title = 'None'; } else { $old_title = $get_folderdocinfo->title; }
   if ($get_folderdocinfo->date == '' ) { $old_date = 'None'; } else { $old_date = $get_folderdocinfo->date; }
   if ($get_folderdocinfo->author == '' ) { $old_author = 'None'; } else { $old_author = $get_folderdocinfo->author; }
   if ($get_folderdocinfo->record_type == '' ) { $old_record_type = 'None'; } else { $old_record_type = $get_folderdocinfo->record_type; }
   if ($get_folderdocinfo->site_name == '' ) { $old_site_name = 'None'; } else { $old_site_name = $get_folderdocinfo->site_name; }
   if ($get_folderdocinfo->site_id == '' ) { $old_site_id = 'None'; } else { $old_site_id = $get_folderdocinfo->site_id; }
   if ($get_folderdocinfo->close_date == '' ) { $old_close_date = 'None'; } else { $old_close_date = $get_folderdocinfo->close_date; }
   if ($get_folderdocinfo->epa_contact_email == '' ) { $old_contact_email = 'None'; } else { $old_contact_email = $get_folderdocinfo->epa_contact_email; }
   if ($get_folderdocinfo->access_type == '' ) { $old_access_type = 'None'; } else { $old_access_type = $get_folderdocinfo->access_type; }
   if ($get_folderdocinfo->source_format == '' ) { $old_source_format = 'None'; } else { $old_source_format = $get_folderdocinfo->source_format; }
   if ($get_folderdocinfo->rights == '' ) { $old_rights = 'None'; } else { $old_rights = $get_folderdocinfo->rights; }
   //if ($get_folderdocinfo->contract_number == '' ) { $old_contract_number = 'None'; } else { $old_contract_number = $get_folderdocinfo->contract_number; }
   //if ($get_folderdocinfo->grant_number == '' ) { $old_grant_number = 'None'; } else { $old_grant_number = $get_folderdocinfo->grant_number; }
   if ($get_folderdocinfo->folder_identifier == '' ) { $old_folder_identifier = 'None'; } else { $old_folder_identifier = $get_folderdocinfo->folder_identifier; }
   if ($get_folderdocinfo->addressee == '' ) { $old_addressee = 'None'; } else { $old_addressee = $get_folderdocinfo->addressee; }
   $old_erval = $get_folderdocinfo->essential_record;
   
$metadata_array = array();

$table_name = 'wpqa_wpsc_epa_folderdocinfo';

//update index level on document
//if index level is updated, folderdocinfo_id has to be updated
if ($il != $old_ilval) {
        //updates index_level to either folder/file
        $data_update_il = array('index_level' => $il);
        //pattdocid = folderdocinfo_id
        $pattdocid_split = explode('-', $pattdocid);
        //rewrite the folderdocinfo_id
        $il_val = '';
        if($il == '1') {
            $il_val = 'Folder';
            $pattdocid_new = $pattdocid_split[0] . '-' . $pattdocid_split[1] . '-' . '01' . '-' . $pattdocid_split[3];
        } 
        elseif($il == '2') {
            $il_val = 'File';
            $pattdocid_new = $pattdocid_split[0] . '-' . $pattdocid_split[1] . '-' . '02' . '-' . $pattdocid_split[3];
        }
        //assign new index level to folderdocinfo_id
        $data_update_folderdocinfo_id = array('folderdocinfo_id' => $pattdocid_new);
        $data_where = array('id' => $folderfileid);
        
        array_push($metadata_array,'Index Level: '.$il_val);
        
        //update index_level
        $wpdb->update($table_name, $data_update_il, $data_where);
        //update folderdocinfo_id
        $wpdb->update($table_name, $data_update_folderdocinfo_id, $data_where);

}

if($essential_record != $old_erval) {
$data_update = array('essential_record' => $essential_record);
$data_where = array('id' => $folderfileid);

$er_val = '';
        if($essential_record == '1') {
            $er_val = 'Yes';
        } 
        elseif($essential_record == '0') {
            $er_val = 'No';
        }

array_push($metadata_array,'Essential Record: '.$er_val);

$wpdb->update($table_name, $data_update, $data_where);
}

//updates fields in folder-file-details modal window
if(!empty($title)) {
$data_update = array('title' => $title);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Title: '.$old_title.' > '.$title);
$wpdb->update($table_name , $data_update, $data_where);
}

if(!empty($date)) {
$data_update = array('date' => $date);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Date: '.$old_date.' > '.$date);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($author)) {
$data_update = array('author' => $author);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Author: '.$old_author.' > '.$author);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($record_type)) {
$data_update = array('record_type' => $record_type);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Record Type: '.$old_record_type.' > '.$record_type);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($site_name)) {
$data_update = array('site_name' => $site_name);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Site Name: '.$old_site_name.' > '.$site_name);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($site_id)) {
$data_update = array('site_id' => $site_id);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Site ID: '.$old_site_id.' > '.$site_id);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($close_date)) {
$data_update = array('close_date' => $close_date);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Close Date: '.$old_close_date.' > '.$close_date);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($contact_email)) {
$data_update = array('epa_contact_email' => $contact_email);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Contact Email: '.$old_contact_email.' > '.$contact_email);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($access_type)) {
$data_update = array('access_type' => $access_type);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Access Type: '.$old_access_type.' > '.$access_type);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($source_format)) {
$data_update = array('source_format' => $source_format);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Source Format: '.$old_source_format.' > '.$source_format);
$wpdb->update($table_name, $data_update, $data_where);
}

/*if(!empty($rights)) {
$data_update = array('rights' => $rights);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Rights: '.$old_rights.' > '.$rights);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($contract_number)) {
$data_update = array('contract_number' => $contract_number);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Contract Number: '.$old_contract_number.' > '.$contract_number);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($grant_number)) {
$data_update = array('grant_number' => $grant_number);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Grant Number: '.$old_grant_number.' > '.$grant_number);
$wpdb->update($table_name, $data_update, $data_where);
}*/

if(!empty($folder_identifier)) {
$data_update = array('folder_identifier' => $folder_identifier);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Folder Identifier: '.$old_folder_identifier.' > '.$folder_identifier);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($addressee)) {
$data_update = array('addressee' => $addressee);
$data_where = array('id' => $folderfileid);
array_push($metadata_array,'Addressee: '.$old_addressee.' > '.$addressee);
$wpdb->update($table_name, $data_update, $data_where);
}

$metadata = implode (", ", $metadata_array);

if (($il != $old_ilval) && ($pattdocid_new != $pattdocid)) {
$pattdocid = $pattdocid . ' > ' . $pattdocid_new;
}
do_action('wpppatt_after_folder_doc_metadata', $ticket_id, $metadata, $pattdocid);

//send notification and email when any folder/file metadata
if($il != $old_ilval || $essential_record != $old_erval || $old_title != $title || $old_date != $date || $old_author != $author 
|| $old_record_type != $record_type || $old_site_name != $site_name || $old_site_id != $site_id || $old_close_date != $close_date ||
$old_contact_email != $contact_email || $old_access_type != $access_type || $old_source_format != $source_format 
|| $old_folder_identifier != $folder_identifier || $old_addressee != $addressee) {

$get_customer_name = $wpdb->get_row('SELECT customer_name FROM wpqa_wpsc_ticket WHERE id = "' . $ticket_id . '"');
$get_user_id = $wpdb->get_row('SELECT ID FROM wpqa_users WHERE display_name = "' . $get_customer_name->customer_name . '"');

$user_id_array = [$get_user_id->ID];
$convert_patt_id = Patt_Custom_Func::translate_user_id($user_id_array,'agent_term_id');
$patt_agent_id = implode($convert_patt_id);
$pattagentid_array = [$patt_agent_id];
$data = [];

//disabled email notification
//$email = 1;
Patt_Custom_Func::insert_new_notification('email-folder-file-metadata-updated',$pattagentid_array,$pattdocid,$data,$email);
}

} else {
   echo "Please make an edit.";
}
?>