<?php
function mdocs_restore_defaults() {
	mdocs_list_header();
	?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Restore Memphis Document Library\'s to Defaults','memphis-documents-library'); ?></h3>
		</div>
		<div class="panel-body">
			<p><?php _e('This will return Memphis Documents Library to its default install state.  All your files, post, and categories will not be remove and all setting will return to their default state.  You can delete everything if you uncheck the "Keep all files, post, and categories." checkbox.  <em>Please backup your files before continuing.</em>','memphis-documents-library'); ?></p>
			<div class="mdocs-clear-both"></div>
			<br>
			<form enctype="multipart/form-data" method="post" action="#" class="mdocs-setting-form" id="mdocs-retore-defaults-form">
				<input type="hidden" name="mdocs-restore-default" value="clean-up" />
				<input type="submit" class="btn btn-primary" value="<?php _e('Restore To Default','memphis-documents-library') ?>" />
				<input type="checkbox" name="mdocs-keep-all-files" checked ><?php _e('Keep all files, post, and categories.', 'memphis-documents-library'); ?>
			</form>
		</div>
	</div>
	<?php
}
function mdocs_restore_default() {
	if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'restore') {
		$blog_id = intval($_REQUEST['blog_id']);
		if ( is_main_site($blog_id) ) mdocs_single_site_remove();
		else mdocs_single_site_remove($blog_id);
	} else { 
		if (is_multisite()) {
			 mdocs_multi_site_remove();
		} else {
			mdocs_single_site_remove();
		}
	}
}

function mdocs_multi_site_remove() {
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
	if ($blogs) {
		$init_blog = true;
		foreach($blogs as $blog) {
			switch_to_blog($blog['blog_id']);
			if($init_blog) $results = $wpdb->get_results( 'SELECT * FROM wp_options WHERE option_name LIKE "mdocs%" ', ARRAY_A );
			else $results = $wpdb->get_results( 'SELECT * FROM wp_'.$blog['blog_id'].'_options WHERE option_name LIKE "mdocs%" ', ARRAY_A );
			foreach($results as $result) {
				if($result['option_name'] != 'mdocs-list' && $result['option_name'] != 'mdocs-cats') delete_option($result['option_name']);
			}
			$init_blog = false;
			if(isset($_REQUEST['mdocs-keep-all-files'])) {
				$upload_dir = wp_upload_dir();
				$mdocs_list = get_option('mdocs-list');
				mdocs_remove_posts_and_attachments();
				$files = glob($upload_dir['basedir'].'/mdocs/*'); 
				foreach($files as $file) if(is_file($file)) unlink($file);
				$files = glob($upload_dir['basedir'].'/mdocs/.*'); 
				foreach($files as $file) if(is_file($file)) unlink($file);
				if(is_dir($upload_dir['basedir'].'/mdocs/')) rmdir($upload_dir['basedir'].'/mdocs/');
				$query = new WP_Query('pagename=mdocuments-library');
				wp_delete_post( $query->post->ID, true );
			}
			
		}
		restore_current_blog();
	}
}
function mdocs_single_site_remove($blog_id=null) {
	global $wpdb;
	$upload_dir = wp_upload_dir();
	$mdocs_list = get_option('mdocs-list');
	if($blog_id == null) $results = $wpdb->get_results( 'SELECT * FROM wp_options WHERE option_name LIKE "mdocs%" ', ARRAY_A );
	else $results = $wpdb->get_results( 'SELECT * FROM wp_'.$blog_id.'_options WHERE option_name LIKE "mdocs%" ', ARRAY_A ); 
	foreach($results as $result) {
		if($result['option_name'] != 'mdocs-list' && $result['option_name'] != 'mdocs-cats') delete_option($result['option_name']);
	}
	if(!isset($_REQUEST['form-data']['mdocs-keep-all-files'])) {
		mdocs_remove_posts_and_attachments();
		$files = glob($upload_dir['basedir'].'/mdocs/*'); 
		foreach($files as $file) if(is_file($file)) unlink($file);
		if(is_dir($upload_dir['basedir'].'/mdocs/')) rmdir($upload_dir['basedir'].'/mdocs/');
		$query = new WP_Query('pagename=mdocuments-library');
		wp_delete_post( $query->post->ID, true );
		update_option('mdocs-list', array());
		$temp_cats[0] = array('base_parent' => '', 'index' => 0, 'parent_index' => 0, 'slug' => 'mdocuments', 'name' => 'Documents', 'parent' => '', 'children' => array(), 'depth' => 0,);
		update_option('mdocs-cats',$temp_cats, '' , 'no');
	}
	
}

?>
