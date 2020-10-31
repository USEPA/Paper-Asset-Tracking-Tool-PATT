<?php
if (is_multisite()) {
	 mdocs_multi_site_remove();
} else {
	mdocs_single_site_remove();
}
function mdocs_multi_site_remove() {
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
	if ($blogs) {
		$init_blog = true;
		foreach($blogs as $blog) {
			switch_to_blog($blog['blog_id']);
			$upload_dir = wp_upload_dir();
			$mdocs_list = get_option('mdocs-list');
			mdocs_uninstall_remove_posts_and_attachments();
			if($init_blog) $results = $wpdb->get_results( 'SELECT * FROM wp_options WHERE option_name LIKE "%mdoc%" ', ARRAY_A );
			else $results = $wpdb->get_results( 'SELECT * FROM wp_'.$blog['blog_id'].'_options WHERE option_name LIKE "%mdoc%" ', ARRAY_A );
			foreach($results as $result) delete_option($result['option_name']);
			$files = glob($upload_dir['basedir'].'/mdocs/*'); 
			foreach($files as $file) if(is_file($file)) unlink($file);
			$files = glob($upload_dir['basedir'].'/mdocs/.*'); 
			foreach($files as $file) if(is_file($file)) unlink($file);
			if(is_dir($upload_dir['basedir'].'/mdocs/')) rmdir($upload_dir['basedir'].'/mdocs/');
			$query = new WP_Query('pagename=mdocuments-library');
			@wp_delete_post( $query->post->ID, true );
			$init_blog = false;
		}
		restore_current_blog();
	}
}
function mdocs_single_site_remove($blog_id=null) {
	global $wpdb;
	$upload_dir = wp_upload_dir();
	$mdocs_list = get_option('mdocs-list');
	mdocs_uninstall_remove_posts_and_attachments();
	if($blog_id == null) $results = $wpdb->get_results( 'SELECT * FROM wp_options WHERE option_name LIKE "mdocs%" ', ARRAY_A );
	else $results = $wpdb->get_results( 'SELECT * FROM wp_'.$blog_id.'_options WHERE option_name LIKE "mdocs%" ', ARRAY_A ); 
	foreach($results as $result) delete_option($result['option_name']);
	$files = glob($upload_dir['basedir'].'/mdocs/*'); 
	foreach($files as $file) if(is_file($file)) unlink($file);
	if(is_dir($upload_dir['basedir'].'/mdocs/')) rmdir($upload_dir['basedir'].'/mdocs/');
	$query = new WP_Query('pagename=mdocuments-library');
	wp_delete_post( $query->post->ID, true );
}
// REMOVES ALL MDOCS POSTS AND ATTACHMENTS
function mdocs_uninstall_remove_posts_and_attachments() {
	$query = new WP_Query(array(
		'post_type' => 'mdocs-posts',
		'post_status' => 'any',
		'posts_per_page' => -1,
	));
	foreach($query->posts as $index => $post) {
		wp_delete_post($post->ID, true );
	}
	$query = new WP_Query(array(
		'post_type' => 'attachment',
		'post_status' => 'any',
		'posts_per_page' => -1,
	));
	foreach($query->posts as $index => $post) {
		if(strpos($post->post_content, 'mdocs_media_attachment')) wp_delete_post($post->ID, true );
	}
}
?>