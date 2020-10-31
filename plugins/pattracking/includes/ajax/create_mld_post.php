<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
global $wpdb, $current_user, $wpscfunction;

if (!$current_user->ID) die();


$obj_s3link = isset($_POST['obj_s3link']) ? sanitize_text_field($_POST['obj_s3link']) : '';
$obj_key = isset($_POST['obj_key']) ? sanitize_text_field($_POST['obj_key']) : '';
$obj_size = isset($_POST['obj_size']) ? sanitize_text_field($_POST['obj_size']) : '';
$obj_type = isset($_POST['obj_type']) ? sanitize_text_field($_POST['obj_type']) : '';
$obj_name = isset($_POST['obj_name']) ? sanitize_text_field($_POST['obj_name']) : '';
$mduff = isset($_POST['mduff']) ? sanitize_text_field($_POST['mduff']) : '';

$mdocs_name = isset($_POST['mdocs_name']) ? sanitize_text_field($_POST['mdocs_name']) : '';
$mdocs_tags = isset($_POST['mdocs_tags']) ? sanitize_text_field($_POST['mdocs_tags']) : '';
$mdocs_type = isset($_POST['mdocs_type']) ? sanitize_text_field($_POST['mdocs_type']) : '';
$mdocs_last_modified = isset($_POST['mdocs_last_modified']) ? sanitize_text_field($_POST['mdocs_last_modified']) : '';
$mdocs_version = isset($_POST['mdocs_version']) ? sanitize_text_field($_POST['mdocs_version']) : '';
$mdocs_cat = isset($_POST['mdocs_cat']) ? sanitize_text_field($_POST['mdocs_cat']) : '';
$mdocs_social = isset($_POST['mdocs_social']) ? sanitize_text_field($_POST['mdocs_social']) : '';
$mdocs_non_members = isset($_POST['mdocs_non_members']) ? sanitize_text_field($_POST['mdocs_non_members']) : '';
$mdocs_index = isset($_POST['mdocs_index']) ? sanitize_text_field($_POST['mdocs_index']) : '';
$mdocs_pname = isset($_POST['mdocs_pname']) ? sanitize_text_field($_POST['mdocs_pname']) : '';
$mdocs_file_status = isset($_POST['mdocs_file_status']) ? sanitize_text_field($_POST['mdocs_file_status']) : '';
$mdocs_post_status = isset($_POST['mdocs_post_status']) ? sanitize_text_field($_POST['mdocs_post_status']) : '';
$mdocs_add_contributors = isset($_POST['mdocs_add_contributors']) ? sanitize_text_field($_POST['mdocs_add_contributors']) : '';
$mdocs_real_author = isset($_POST['mdocs_real_author']) ? sanitize_text_field($_POST['mdocs_real_author']) : '';
$mdocs_categories = isset($_POST['mdocs_categories']) ? sanitize_text_field($_POST['mdocs_categories']) : '';
$mdocs_desc = isset($_POST['mdocs_desc']) ? sanitize_text_field($_POST['mdocs_desc']) : '';

//$input = $_REQUEST['input'];  //not used.

$error = '';


// mdocs_file_upload style

$_FILES['mdocs']['name'] = $obj_name;
$_FILES['mdocs']['type'] = $obj_type;
$_FILES['mdocs']['tmp_name'] = 'tmp_' . $obj_name;
$_FILES['mdocs']['error'] = 0;
$_FILES['mdocs']['size'] = $obj_size;


$_POST['mdocs-name'] = $mdocs_name;
$_POST['mdocs-tags'] = $mdocs_tags;
$_POST['mdocs-type'] = $mdocs_type;

$_POST['mdocs-last-modified'] = $mdocs_last_modified;
$_POST['mdocs-version'] = $mdocs_version;
$_POST['mdocs-cat'] = $mdocs_cat;
$_POST['mdocs-social'] = $mdocs_social;
$_POST['mdocs-non-members'] = $mdocs_non_members;
$_POST['mdocs-index'] = $mdocs_index;
$_POST['mdocs-pname'] = $mdocs_pname;
$_POST['mdocs-file-status'] = $mdocs_file_status;
$_POST['mdocs-post-status'] = $mdocs_post_status; 
$_POST['mdocs-add-contributors'] = $mdocs_add_contributors;  
$_POST['mdocs-real-author'] = $mdocs_real_author;    
$_POST['mdocs-categories'] = $mdocs_categories;
$_POST['mdocs-desc'] = $mdocs_desc;

$_POST['mdocs-upload-file-field'] = $mduff;













// TEST data
/* 
$_FILES['mdocs']['name'] = 'best-test-file.txt';
$_FILES['mdocs']['type'] = 'text/plain';
$_FILES['mdocs']['tmp_name'] = 'tmp_' . 'aaron';
$_FILES['mdocs']['error'] = 0;
$_FILES['mdocs']['size'] = 1234;
*/

/*
$_POST['mdocs-type'] = 'mdocs-add';
$_POST['mdocs-name'] = 'The best time';
$_POST['mdocs-last-modified'] = '15-10-2020 18:42';
$_POST['mdocs-tags'] = 'comma, separated, list';
$_POST['mdocs-categories'] = '';
$_POST['mdocs-desc'] = '';


$_POST['mdocs-upload-file-field'] = $mduff;

$_POST['mdocs-type'] = 'mdocs-add';
$_POST['mdocs-index'] = '';
$_POST['mdocs-pname'] = '';


$_POST['mdocs-name'] = 'name test';
$_POST['mdocs-version'] = '1.0';
$_POST['mdocs-last-modified'] = '15-10-2020 08:08';
$_POST['mdocs-file-status'] = '';
$_POST['mdocs-post-status'] = ''; // not required
$_POST['mdocs-social'] = 'on';
$_POST['mdocs-non-members'] = 'on';
$_POST['mdocs-add-contributors'] = '';  // not required
$_POST['mdocs-real-author'] = '';     // not required
$_POST['mdocs-tags'] = 'this, is, a, comma list';
$_POST['mdocs-categories'] = '';     // not required
$_POST['mdocs-desc'] = '';

$_POST['mdocs-cat'] = '0000037-1-01-5';
*/


mdocs_file_upload();


if( $mdocs_type == 'mdocs-update' ) {
	$where = [
		'post_id' => $mdocs_index-1
	];
} elseif( $mdocs_type == 'mdocs-add' ) {
	$where = [
		'id' => $_POST['wppatt_files_index']
	];
}

$table_name = $wpdb->prefix . 'wpsc_epa_folderdocinfo_files';
$data = [
	'object_key' => $obj_key,
	'object_location' => $obj_s3link,
//	'file_object_id' => $obj_name,
	'file_size' => $obj_size
];

/*
$where = [
	'id' => $_POST['wppatt_files_index']
];
*/

$wpdb->update( $table_name, $data ,$where );

// OLD SAVE start
// mdocs_process_file style

/*
$file['name'] = $obj_name;
$file['type'] = $obj_type;
$file['tmp_name'] = 'tmp_' . $obj_name;
$file['error'] = 0;
$file['size'] = $obj_size;
$file['post_status'] = 'publish';
$file['post-status'] = 'publish';

$_POST['mdocs-type'] = 'mdocs-add';
$_POST['mdocs-name'] = 'The best time';
$_POST['mdocs-last-modified'] = '15-10-2020 18:42';
$_POST['mdocs-tags'] = 'comma, separated, list';
$_POST['mdocs-categories'] = '';
$_POST['mdocs-desc'] = '';
*/

//$upload = mdocs_process_file( $file, $import=false ); 
// OLD SAVE end



/*
$output = array(
	'error'   => $error,
//	'input' => $input,
	'obj_s3link' => $obj_s3link,
	'obj_key' => $obj_key,
	'obj_size' => $obj_size,
	'obj_type' => $obj_type,
	'upload' => $upload,
	'mduff' => $mduff,
	'wppatt_files_index' => $_POST['wppatt_files_index']
);
echo json_encode($output);	
*/