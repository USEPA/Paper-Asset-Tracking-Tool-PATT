<?php

//if ( ! defined( 'ABSPATH' ) ) {
//	exit; // Exit if accessed directly
//}

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');
require WPPATT_ABSPATH . 'includes/admin/pages/scripts/vendor/autoload.php';
include_once( WPPATT_ABSPATH . 'includes/api_authorization_strings.php' );

global $current_user, $wpscfunction, $wpdb;

function flip_isset_diff($b, $a) {
    $at = array_flip($a);
    $d = array();
    foreach ($b as $i)
        if (!isset($at[$i])) 
            $d[] = $i;

    return $d;
}

		$s3 = new Aws\S3\S3Client([
			'region'  => $s3_region,
			'version' => 'latest',
			'credentials' => [
				'key'    => $s3_key,
				'secret' => $s3_secret,
			]
		]);	
		
		 $results = $s3->getPaginator('ListObjects', [
        'Bucket' => $s3_bucket
    ]);

$s3_object_keys_array = array();
    foreach ($results as $result) {
        foreach ($result['Contents'] as $object) {
            array_push($s3_object_keys_array, $object['Key']);
        }
    }

//print_r($s3_object_keys_array);

	$table_name = $wpdb->prefix.'wpsc_epa_folderdocinfo_files';
	
	$get_s3_db_object_keys = $wpdb->get_results("SELECT object_key
		FROM ".$table_name."
		WHERE object_key != ''");
	
	$s3_db_object_keys_array = array();
	
	foreach($get_s3_db_object_keys as $item) {
		$s3_db_object_keys = $item->object_key;
		array_push($s3_db_object_keys_array, $s3_db_object_keys);
	}
//print_r($s3_db_object_keys_array);
	
$diff_array = flip_isset_diff($s3_object_keys_array,$s3_db_object_keys_array);

//print_r($diff_array);

	foreach($diff_array as $key => $value) {

    $result = $s3->deleteObject([
        'Bucket' => $s3_bucket,
        'Key'    => $value
    ]);
    
    //var_dump($result);
    
    if ($result['@metadata']['statusCode'] == 204)
    {
        echo $value . ' was deleted from S3.<br />';
    }
	}
?>