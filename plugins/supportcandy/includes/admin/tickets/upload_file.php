<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
$isError       = false;
$errorMessege  = '';
$attachment_id = 0;

if(!$_FILES){
    $isError=true;
}
$wpsc_allow_attachment_type = get_option('wpsc_allow_attachment_type');
$wpsc_attachment_type       = explode(',',$wpsc_allow_attachment_type);
$wpsc_attachment_type       = array_map('trim', $wpsc_attachment_type);
$wpsc_attachment_type       = array_map('strtolower', $wpsc_attachment_type);

$tempExtension = explode('.', $_FILES['file']['name']);
$extension     = strtolower($tempExtension[count($tempExtension)-1]);

if( !$isError ){
    
    switch ($extension){
        case 'exe':
        case 'php':
        case 'js':
            $isError      = true;
            $errorMessege = __('Error: file format not supported!','supportcandy');
            break;
    }
    if ( preg_match('/php/i', $extension) || preg_match('/phtml/i', $extension) ){
      $isError=true;
      $errorMessege=__('Error: file format not supported!','supportcandy');
    }
    if (!(in_array($extension,$wpsc_attachment_type))) {
      $isError = true;
      $errorMessege = __('Error: file format not supported!','supportcandy');
    }
}

if( !$isError && $_FILES['file']['tmp_name']==''){
    $isError      = true;
    $errorMessege = __('Error: file size exceeded allowed limit!','supportcandy');
}

if( !$isError ){

    $attachment_count = get_option('wpsc_attachment_count');
    if(!$attachment_count) $attachment_count = 1;
    $term = wp_insert_term( 'attachment_'.$attachment_count, 'wpsc_attachment' );
    if(!$term || is_wp_error($term)) die();
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
    
    add_term_meta ($term['term_id'], 'filename', $_FILES['file']['name']);

    $save_file_name = str_replace(' ','_',$_FILES['file']['name']);
    $save_file_name = str_replace(',','_',$_FILES['file']['name']);
    $save_file_name = explode('.', $save_file_name);
    
    $img_extensions = array('png','jpeg','jpg','bmp','pdf','gif','PNG','JPEG','JPG','BMP','PDF','GIF');
    $extension      = $save_file_name[count($save_file_name)-1];
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

    move_uploaded_file( $_FILES['file']['tmp_name'], $save_directory );
    
    add_term_meta ($term['term_id'], 'save_file_name', $save_file_name);
    add_term_meta ($term['term_id'], 'active', '0');
    add_term_meta ($term['term_id'], 'time_uploaded', $now);
    add_term_meta ($term['term_id'], 'is_restructured', 1);

    $attachment_id = $term['term_id'];
    $errorMessege  = __('done','supportcandy');

}

$isError=($isError)?'yes':'no';

$response = array(
    'error'        => $isError,
    'errorMessege' => $errorMessege,
    'id'           => $attachment_id
);

echo json_encode($response);