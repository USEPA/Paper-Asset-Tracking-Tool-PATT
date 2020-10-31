<?php
function mdocs_dashboard_menu() {
	add_menu_page( __('Folder/File Attachments','memphis-documents-library'), __('Folder/File Attachments','memphis-documents-library'), 'mdocs_allow_upload', 'memphis-documents.php', 'mdocs_dashboard', 'dashicons-media-document' );
	if(get_option('mdocs-disable-bootstrap-admin')) add_submenu_page( 'memphis-documents.php', __('Settings', 'memphis-documents-library'), __('Settings', 'memphis-documents-library'), 'administrator', 'memphis-documents.php&mdocs-cat=settings', 'mdocs_settings' );
}

function mdocs_dashboard() {
	if(isset($_FILES['mdocs']) && $_FILES['mdocs']['name'] != '' && $_POST['mdocs-type'] == 'mdocs-add') mdocs_file_upload();
	elseif(isset($_FILES['mdocs']) && $_POST['mdocs-type'] == 'mdocs-update') mdocs_file_upload();
	elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-import') {
		if(mdocs_file_upload_max_size() < $_FILES['mdocs-import-file']['size']) mdocs_errors(MDOCS_ERROR_7, 'error');
		else mdocs_import_zip();
	} elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-revision') mdocs_update_revision();
	elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-cats') mdocs_update_cats();
	mdocs_dashboard_view();
}

function mdocs_dashboard_view() {
	if(isset($_GET['mdocs-cat'])) $current_cat = mdocs_sanitize_string($_GET['mdocs-cat']);
	else $current_cat = null;
	if(isset($_GET['mdocs-error-msg'])) mdocs_errors($_GET['mdocs-error-msg'],'error');
	if($current_cat == 'import') mdocs_import($current_cat);
	elseif($current_cat == 'export') mdocs_export($current_cat);
	elseif($current_cat == 'cats') mdocs_edit_cats($current_cat);
	//elseif($current_cat == 'cats' && MDOCS_DEV) mdocs_folder_editor($current_cat);
	elseif($current_cat == 'settings') mdocs_settings();
	elseif($current_cat == 'batch') mdocs_batch_upload($current_cat);
	elseif($current_cat == 'short-codes') mdocs_shortcodes($current_cat);
	elseif($current_cat == 'filesystem-cleanup') mdocs_filesystem_cleanup($current_cat);
	elseif($current_cat == 'restore') mdocs_restore_defaults($current_cat);
	elseif($current_cat == 'allowed-file-types') mdocs_allowed_file_types($current_cat);
	elseif($current_cat == 'find-lost-files') mdocs_find_lost_files($current_cat);
	elseif($current_cat == 'server-compatibility') mdocs_server_compatibility($current_cat);
	else echo mdocs_the_list();
}

function mdocs_add_update_ajax($edit_type='Add Document') {
	if(isset($_REQUEST['mdocs-id'])) $the_mdoc = mdocs_get_file_by($_REQUEST['mdocs-id'], 'id');
	else $the_mdoc = null;
	if($the_mdoc == null) $the_mdoc = array('parent' => '', 'owner' => '');
	//if(current_user_can('mdocs_allow_upload') && is_admin() || current_user_can('mdocs_allow_upload_frontend') && !is_admin()) {
		// POST CATEGORIES
		$post_categories = wp_get_post_categories($the_mdoc['parent']);
		if(count($post_categories) > 0) {
			$the_mdoc['mdocs-categories'] = array();
			foreach($post_categories as $post_cat) {
				$the_category_name = get_the_category_by_ID($post_cat);
				array_push($the_mdoc['mdocs-categories'], $the_category_name);
			}
		} else $the_mdoc['mdocs-categories'] = null;
		// POST TAGS
		$post_tags = wp_get_post_tags($the_mdoc['parent']);
		$the_tags = '';
		foreach($post_tags as $post_tag) $the_tags .= $post_tag->name.', ';
		$the_tags = rtrim($the_tags, ', ');
		$the_mdoc['post-tags'] = $the_tags;
		$date_format = get_option('mdocs-date-format');
		if($edit_type == 'Update Document') {
			$the_date = mdocs_format_unix_epoch($the_mdoc['modified']);
			$the_mdoc['gmdate'] = date($date_format, $the_date['date']);
		} else {
			$the_date = mdocs_format_unix_epoch();
			$the_mdoc['gmdate'] = date($date_format, $the_date['date']);
		}
		echo json_encode($the_mdoc);
	//} else {
		//$error['error'] = __('The permission of this file have changed and you no longer have acces to it, please contact the ower of the file.', 'memphis-documents-library')."\n\r";
		//$error['error'] .= __('[ File Owner ]', 'memphis-documents-library').' => '.$the_mdoc['owner']."\n\r";
		//echo json_encode($error);
	//}
}
?>