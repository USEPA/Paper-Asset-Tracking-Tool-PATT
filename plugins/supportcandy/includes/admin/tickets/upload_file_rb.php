<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $wpscfunction;
$extensions    = array('jpg','JPG','jpeg','JPEG','png','PNG','gif','GIF');
$tempExtension = explode('.', $_FILES['file']['name']);
$tempExtension = strtolower($tempExtension[count($tempExtension)-1]);

$wpsc_allow_attachment_type = get_option('wpsc_allow_attachment_type');
$wpsc_attachment_type       = explode(',',$wpsc_allow_attachment_type);
$wpsc_attachment_type       = array_map('trim', $wpsc_attachment_type);
$wpsc_attachment_type       = array_map('strtolower', $wpsc_attachment_type);

$file_url = '';
if (in_array($tempExtension,$extensions) && in_array($tempExtension,$wpsc_attachment_type)) {
  $upload_dir     = wp_upload_dir();
  $save_file_name = $_FILES['file']['name'];
  $save_file_name = str_replace(' ','_',$save_file_name);
  $save_file_name = str_replace(',','_',$save_file_name);
  $save_file_name = time().'_'. $save_file_name;

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

  $save_directory = $upload_dir['basedir'] . '/wpsc/'.$year.'/'.$month.'/'.$save_file_name;
  $file_url       = "";
  
  $responce = move_uploaded_file( $_FILES['file']['tmp_name'], $save_directory );
  if( $responce ){
    
    $attachment_count = get_option('wpsc_attachment_count');
    if(!$attachment_count) $attachment_count = 1;
    $term = wp_insert_term( 'attachment_img_'.$attachment_count, 'wpsc_image_attachment' );
    if(!$term || is_wp_error($term)) die();
    update_option('wpsc_attachment_count', ++$attachment_count);

    $file_path = addslashes($save_directory);
    add_term_meta ($term['term_id'], 'file_path', $file_path);

    $file_url = site_url() . "?wpsc_img_attachment=" . $term['term_id'];

  }
}
echo json_encode($file_url);
