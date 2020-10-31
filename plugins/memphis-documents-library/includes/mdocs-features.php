<?php
// HIGHJACK MEDIA UPLOAD
/*
add_filter('wp_handle_upload_prefilter', 'custom_upload_filter' );
function custom_upload_filter( $file ){
    $file['name'] = 'wordpress-is-awesome-' . $file['name'];
	mdocs_file_upload();
	var_dump($file);
	die();
    return $file;
}
*/
// REWRITE RULES
/*
function custom_rewrite_basic() {
	add_rewrite_rule('^mdocs-file/([0-9]+)/?', 'index.php?mdocs-file=$matches[1]', 'top');
}
add_action('init', 'custom_rewrite_basic', 10 , 0);
*/
?>