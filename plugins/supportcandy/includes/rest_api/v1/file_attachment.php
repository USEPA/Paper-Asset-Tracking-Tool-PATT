<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$response  = array();
$file      = $_FILES['file'];
$tempExt   = explode('.', $file['name']);
$extension = strtolower($tempExt[count($tempExt)-1]);
$isError   = false;

$wpsc_allow_attachment_type = get_option('wpsc_allow_attachment_type');
$wpsc_attachment_type       = explode(',',$wpsc_allow_attachment_type);
$wpsc_attachment_type       = array_map('trim', $wpsc_attachment_type);
$wpsc_attachment_type       = array_map('strtolower', $wpsc_attachment_type);

// Check if extension is allowed or not
switch ($extension){
    case 'exe':
    case 'php':
    case 'js':
        $isError = true;
        break;
}
if ( preg_match('/php/i', $extension) || preg_match('/phtml/i', $extension) ){
  $isError = true;
}
if (!(in_array($extension,$wpsc_attachment_type))) {
  $isError = true;
}
if ($isError) {
  $response = new WP_Error(
      'file_format_not_allowed',
      'Given file format is not allowed!',
      array(
          'status' => 403,
      )
  );
  return;
}

// get next attachment term
$attachment_count = get_option('wpsc_attachment_count');
if(!$attachment_count) $attachment_count = 1;
$term = wp_insert_term( 'attachment_'.$attachment_count, 'wpsc_attachment' );
if(!$term || is_wp_error($term)) {
  $response = new WP_Error(
      'internal_server_error',
      'Taxonomy not inserted!',
      array(
          'status' => 500,
      )
  );
  return;
}
update_option('wpsc_attachment_count',++$attachment_count);

$now   = date("Y-m-d H:i:s");
$time  = strtotime($now);
$month = date("m",$time);
$year  = date("Y",$time);

$upload_dir = wp_upload_dir();
if (!file_exists($upload_dir['basedir'] . '/wpsc/'.$year)) {
  mkdir($upload_dir['basedir'] . '/wpsc/'.$year, 0755, true);
}
if (!file_exists($upload_dir['basedir'] . '/wpsc/'.$year.'/'.$month)) {
    mkdir($upload_dir['basedir'] . '/wpsc/'.$year.'/'.$month, 0755, true);
}

add_term_meta ($term['term_id'], 'filename', $file['name']);

$save_file_name = str_replace(' ','_',$file['name']);
$save_file_name = str_replace(',','_',$file['name']);
$save_file_name = explode('.', $save_file_name);

$img_extensions = array('png','jpeg','jpg','bmp','pdf','PNG','JPEG','JPG','BMP','PDF');
if(!in_array($extension, $img_extensions)){
  $extension = $extension.'.txt';
  add_term_meta ($term['term_id'], 'is_image', '0');
} else {
  add_term_meta ($term['term_id'], 'is_image', '1');
}

unset( $save_file_name[count($save_file_name)-1] );

$save_file_name = implode('-', $save_file_name);

$save_file_name = time().'_'.preg_replace('/[^A-Za-z0-9\-]/', '', $save_file_name).'.'.$extension;

$save_directory = $upload_dir['basedir'] . '/wpsc/'.$year.'/'.$month.'/'.$save_file_name;

move_uploaded_file( $file['tmp_name'], $save_directory );

add_term_meta ($term['term_id'], 'save_file_name', $save_file_name);
add_term_meta ($term['term_id'], 'active', '0');
add_term_meta ($term['term_id'], 'time_uploaded', date("Y-m-d H:i:s"));
add_term_meta ($term['term_id'], 'is_restructured', 1);

$attachment_id = $term['term_id'];

$response = array(
  'status' => 200,
  'attachment_id' => $attachment_id,
);
