<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvarsfolderdocid'])){
    
$document_ids = $_POST['postvarsfolderdocid'];
$document_array = explode(",", $document_ids);
$document_count = count($document_array);
$total_count = 0;
$folderdocarray = array();

foreach($document_array as $key => $value) { 
    
$get_document = $wpdb->get_row("SELECT a.folderdocinfo_id as folderdocinfo_id
FROM wpqa_wpsc_epa_folderdocinfo a 
INNER JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id 
INNER JOIN wpqa_wpsc_epa_storage_location c on b.storage_location_id = c.id 
WHERE ((c.aisle <> 0 AND c.bay <> 0 AND c.shelf <> 0 AND c.position <> 0 AND c.digitization_center <> 666 AND b.box_destroyed = 0)) 
AND a.folderdocinfo_id = '" . $value . "'");
$document = $get_document->folderdocinfo_id;

if ($document != '') {
array_push($folderdocarray, $document);
}

if ($document == '') {
$total_count++;
}

}

$folderdocarray_val = implode(',', $folderdocarray);

if ($document_count == $total_count) {
echo 'false'.'|'.$folderdocarray_val;
}

if ($total_count < $document_count && $total_count != 0) {
echo 'warn'.'|'.$folderdocarray_val;
}

if ($total_count < $document_count && $total_count == 0) {
echo 'true'.'|'.$folderdocarray_val;
}

} else {
   echo "Update not successful.";
}
?>