<?php

//if ( ! defined( 'ABSPATH' ) ) {
//	exit; // Exit if accessed directly
//}

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

global $current_user, $wpscfunction, $wpdb;


$modified_date = $wpdb->get_results(
"
SELECT 
* from wpqa_wpsc_epa_folderdocinfo WHERE file_object_id <> '' AND date_updated < CURRENT_DATE - INTERVAL 14 DAY
"
);

foreach ($modified_date as $data) {

$filename = $data->file_name;
$file_location = $data->file_location;

if (strpos($filename, ',') == 0) {
$file_name_with_full_path = $_SERVER['DOCUMENT_ROOT'] . '/wordpress3/wp-content' . $file_location . $filename;

    if (file_exists($file)) {
        echo $file_name_with_full_path;
        //unlink($file);
    } else {
        echo "file_not_found";
    }
    
    
} elseif(strpos($filename, ',') > 0) {
$filename_array = explode(',', $filename); //split string into array seperated by ', '

foreach ($filename_array as $file) {
    
$files_array = array();
$file_name_with_full_path = $_SERVER['DOCUMENT_ROOT'] . '/wordpress3/wp-content' . $file_location . $file;
array_push($files_array, $file_name_with_full_path);

foreach ($files_array as $file) {
    if (file_exists($file)) {
        print_r($files_array);
        unlink($file);
    } else {
        echo "file_not_found";
    }
}

}
}

}

?>