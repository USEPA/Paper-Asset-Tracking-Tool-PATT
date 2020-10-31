<?php 

// PATT BEGIN

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

/*								// PATT START
$filename_suffix = '111';
$mdocs_cat = '222';
$request_id = '1';
global $wpdb;
$wpdb->show_errors();
$table_name = $wpdb->prefix.'wpsc_epa_folderdocinfo_files';

$wpdb->insert(
            $table_name,
            array(
						'folderdocinfo_id'  => $mdocs_cat,
						'folderdocinfofile_id'   => $mdocs_cat.'-a'.$filename_suffix,
						'attachment' => '1',
						'file_name'  => 'test',
						'file_location'   => '/uploads/mdocs/',
						'title'  => $request_id,
						'description'   => 'test',
						'tags' => 'test')
     );
*/

$folderdocinfo_id = '0000001-1-01-2';
$get_attachment_ids = $wpdb->get_results("SELECT folderdocinfofile_id
FROM wpqa_wpsc_epa_folderdocinfo_files
WHERE attachment = '1' AND folderdocinfo_id = '".$folderdocinfo_id."'");

$folderdocinfofile_id_array = array();

foreach($get_attachment_ids as $item)
    {
$clean_folderdocinfofileid = substr($item->folderdocinfofile_id, strrpos($item->folderdocinfofile_id, '-') + 1);

if (substr($clean_folderdocinfofileid, 0, 1) === 'a') {
array_push($folderdocinfofile_id_array, substr($clean_folderdocinfofileid, 1));
}
}

print_r($folderdocinfofile_id_array);
$max_value = max($folderdocinfofile_id_array);
echo $max_value + 1;

?>