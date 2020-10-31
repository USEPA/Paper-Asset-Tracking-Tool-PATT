<?php
function mdocs_init_settings() {
	//register_setting('mdocs-settings', 'mdocs-options');
	//add_option('mdocs-options',array(), 'no');
	add_filter('upload_mimes', 'mdocs_custom_mime_types');
	$temp_cats = array();
	$temp_cats[0] = array('base_parent' => '', 'index' => 0, 'parent_index' => 0, 'slug' => 'mdocuments', 'name' => 'Documents', 'parent' => '', 'children' => array(), 'depth' => 0,);
	register_setting('mdocs-settings', 'mdocs-cats');
	add_option('mdocs-cats',$temp_cats, '' , 'no');
	if(is_string(get_option('mdocs-cats'))) update_option('mdocs-cats',$temp_cats, '' , 'no');
	register_setting('mdocs-settings', 'mdocs-list');
	add_option('mdocs-list',array(), '' , 'no');
	register_setting('mdocs-settings', 'mdocs-num-cats');
	add_option('mdocs-num-cats',1);
	register_setting('mdocs-settings', 'mdocs-num-cats');
	add_option('mdocs-num-cats',1);
	register_setting('mdocs-settings', 'mdocs-zip');
	add_option('mdocs-zip','mdocs-export.zip');
	//register_setting('mdocs-settings', 'mdocs-wp-root');
	//update_option('mdocs-wp-root','');
	register_setting('mdocs-top-downloads', 'mdocs-top-downloads');
	add_option('mdocs-top-downloads',10);
	register_setting('mdocs-top-downloads', 'mdocs-top-rated');
	add_option('mdocs-top-rated',10);
	register_setting('mdocs-top-downloads', 'mdocs-last-updated');
	add_option('mdocs-last-updated',10);
	//GLOBAL VARIABLES
	register_setting('mdocs-global-settings', 'mdocs-list-type');
	update_option('mdocs-list-type','small');
	register_setting('mdocs-global-settings', 'mdocs-list-type-dashboard');
	add_option('mdocs-list-type-dashboard','small');
	register_setting('mdocs-global-settings', 'mdocs-hide-all-files-non-members');
	add_option('mdocs-hide-all-files-non-members', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-all-posts-non-members');
	add_option('mdocs-hide-all-posts-non-members', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-all-posts-non-members-default');
	add_option('mdocs-hide-all-posts-non-members-default', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-all-files');
	add_option('mdocs-hide-all-files', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-all-posts');
	add_option('mdocs-hide-all-posts', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-all-posts-default');
	add_option('mdocs-hide-all-posts-default', false);
	// TAB OPTIONS
	register_setting('mdocs-global-settings', 'mdocs-default-content');
	add_option('mdocs-default-content','description');
	register_setting('mdocs-global-settings', 'mdocs-show-description');
	add_option('mdocs-show-description',true);
	register_setting('mdocs-global-settings', 'mdocs-show-preview');
	add_option('mdocs-show-preview', true);
	register_setting('mdocs-global-settings', 'mdocs-show-versions');
	add_option('mdocs-show-versions', true);
	// ROLES AND PERMISSIONS
	register_setting('mdocs-global-settings', 'mdocs-caps');
	add_option('mdocs-caps', array());
	
	// INITIALIZATION OF DOCUMENTS PAGE
	register_setting('mdocs-global-settings', 'mdocs-box-show-print-and-download');
	add_option('mdocs-box-show-print-and-download', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-widget-numbers');
	add_option('mdocs-hide-widget-numbers', true);
	register_setting('mdocs-global-settings', 'mdocs-show-share');
	add_option('mdocs-show-share', true);
	register_setting('mdocs-global-settings', 'mdocs-show-social');
	add_option('mdocs-show-social', true);
	register_setting('mdocs-global-settings', 'mdocs-download-color-normal');
	add_option('mdocs-download-color-normal', '#d14836');
	register_setting('mdocs-global-settings', 'mdocs-download-color-hover');
	add_option('mdocs-download-color-hover', '#c34131');
	register_setting('mdocs-global-settings', 'mdocs-download-text-color-normal');
	add_option('mdocs-download-text-color-normal', '#ffffff');
	register_setting('mdocs-global-settings', 'mdocs-download-text-color-hover');
	add_option('mdocs-download-text-color-hover', '#ffffff');
	register_setting('mdocs-global-settings', 'mdocs-navbar-bgcolor');
	add_option('mdocs-navbar-bgcolor', '#337ab7');
	register_setting('mdocs-global-settings', 'mdocs-navbar-bordercolor');
	add_option('mdocs-navbar-bordercolor', '#337ab7');
	register_setting('mdocs-global-settings', 'mdocs-navbar-text-color-normal');
	add_option('mdocs-navbar-text-color-normal', '#ffffff');
	register_setting('mdocs-global-settings', 'mdocs-navbar-text-color-hover');
	add_option('mdocs-navbar-text-color-hover', '#f7f7f7');
	register_setting('mdocs-global-settings', 'mdocs-show-new-banners');
	add_option('mdocs-show-new-banners', true);
	register_setting('mdocs-global-settings', 'mdocs-hide-file-type-icon');
	add_option('mdocs-hide-file-type-icon', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-new-update-label');
	add_option('mdocs-hide-new-update-label', true);
	register_setting('mdocs-global-settings', 'mdocs-hide-name');
	add_option('mdocs-hide-name', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-filename');
	add_option('mdocs-hide-filename', false);
	register_setting('mdocs-global-settings', 'mdocs-time-to-display-banners');
	add_option('mdocs-time-to-display-banners', 14);
	register_setting('mdocs-global-settings', 'mdocs-doc-preview');
	add_option('mdocs-doc-preview', false);
	register_setting('mdocs-global-settings', 'mdocs-sort-type');
	add_option('mdocs-sort-type','modified');
	register_setting('mdocs-global-settings', 'mdocs-sort-style');
	add_option('mdocs-sort-style','desc');
	register_setting('mdocs-global-settings', 'mdocs-htaccess');
	add_option('mdocs-htaccess', "Deny from all\nOptions +Indexes");
	//register_setting('mdocs-global-settings', 'mdocs-view-private');
	//add_option('mdocs-view-private', mdocs_init_view_private());
	//register_setting('mdocs-global-settings', 'mdocs-default-caps');
	//add_option('mdocs-default-caps',mdocs_default_caps());
	register_setting('mdocs-global-settings', 'mdocs-date-format');
	add_option('mdocs-date-format', 'd-m-Y G:i');
	register_setting('mdocs-global-settings', 'mdocs-allow-upload');
	add_option('mdocs-allow-upload', array());
	register_setting('mdocs-global-settings', 'mdocs-font-size');
	add_option('mdocs-font-size', '14');
	register_setting('mdocs-global-settings', 'mdocs-post-show-title');
	add_option('mdocs-post-show-title', true);
	register_setting('mdocs-global-settings', 'mdocs-post-title-font-size');
	add_option('mdocs-post-title-font-size', '24');
	register_setting('mdocs-global-settings', 'mdocs-override-post-title-font-size');
	add_option('mdocs-override-document-list-title-font-size', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-subfolders');
	add_option('mdocs-hide-subfolders', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-all-subfolders');
	add_option('mdocs-hide-all-subfolders', false);
	register_setting('mdocs-global-settings', 'mdocs-show-post-menu');
	add_option('mdocs-show-post-menu', false);
	register_setting('mdocs-global-settings', 'mdocs-disable-user-sort');
	add_option('mdocs-disable-user-sort', false);
	register_setting('mdocs-global-settings', 'mdocs-disable-bootstrap');
	add_option('mdocs-disable-bootstrap', false);
	register_setting('mdocs-global-settings', 'mdocs-disable-bootstrap-admin');
	add_option('mdocs-disable-bootstrap-admin', false);
	register_setting('mdocs-global-settings', 'mdocs-disable-jquery');
	add_option('mdocs-disable-jquery', false);
	register_setting('mdocs-global-settings', 'mdocs-disable-fontawesome');
	add_option('mdocs-disable-fontawesome', false);
	register_setting('mdocs-global-settings', 'mdocs-show-no-file-found');
	add_option('mdocs-show-no-file-found', true);
	register_setting('mdocs-global-settings', 'mdocs-preview-type');
	add_option('mdocs-preview-type', 'google');
	register_setting('mdocs-global-settings', 'mdocs-preview-type');
	add_option('mdocs-preview-type', 'google');
	register_setting('mdocs-global-settings', 'mdocs-box-view-key');
	add_option('mdocs-box-view-key', '');
	register_setting('mdocs-global-settings', 'mdocs-remove-posts-from-homepage');
	add_option('mdocs-remove-posts-from-homepage', false);
	register_setting('mdocs-global-settings', 'mdocs-dropdown-toggle-fix');
	add_option('mdocs-dropdown-toggle-fix', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-navbar');
	add_option('mdocs-hide-navbar', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-sortbar');
	add_option('mdocs-hide-sortbar', false);
	register_setting('mdocs-global-settings', 'mdocs-convert-to-latin');
	add_option('mdocs-convert-to-latin', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-footer');
	add_option('mdocs-hide-footer', false);
	register_setting('mdocs-global-settings', 'mdocs-hide-widget-column-titles');
	add_option('mdocs-hide-widget-column-titles', false);
	register_setting('mdocs-global-settings', 'mdocs-frontend-upload-redirect');
	add_option('mdocs-frontend-upload-redirect', false);
	// FILE HIGHLIGHT COLOURS
	register_setting('mdocs-global-settings', 'mdocs-file-highlight-color-new-normal');
	add_option('mdocs-file-highlight-color-new-normal', '#dff0d8');
	register_setting('mdocs-global-settings', 'mdocs-file-highlight-color-new-hover');
	add_option('mdocs-file-highlight-color-new-hover', '#d0e9c6');
	register_setting('mdocs-global-settings', 'mdocs-file-highlight-color-updated-normal');
	add_option('mdocs-file-highlight-color-updated-normal', '#d9edf7');
	register_setting('mdocs-global-settings', 'mdocs-file-highlight-color-updated-hover');
	add_option('mdocs-file-highlight-color-updated-hover', '#c4e3f3');
	
	//***** MEGACOOKIE SAVED VARIABLES ******//
	
	register_setting('mdocs-global-settings', 'mdocs-show-upload-folder');
	add_option('mdocs-show-upload-folder', true);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-version');
	add_option('mdocs-show-upload-version', true);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-date');
	add_option('mdocs-show-upload-date', true);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-file-status');
	add_option('mdocs-show-upload-file-status', true);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-post-status');
	add_option('mdocs-show-upload-post-status', true);
	
	
	register_setting('mdocs-global-settings', 'mdocs-show-upload-social');
	add_option('mdocs-show-upload-social', true);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-show-social-apps');
	add_option('mdocs-show-upload-show-social-apps', get_option('mdocs-show-upload-social'));
	
	
	register_setting('mdocs-global-settings', 'mdocs-show-non-members');
	add_option('mdocs-show-non-members', true);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-downloadable-by-non-members');
	add_option('mdocs-show-upload-downloadable-by-non-members', get_option('mdocs-show-non-members'));
	
	
	register_setting('mdocs-global-settings', 'mdocs-show-upload-contributors');
	add_option('mdocs-show-upload-contributors', true);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-tags');
	add_option('mdocs-show-upload-tags', true);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-categories');
	add_option('mdocs-show-upload-categories', true);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-description');
	add_option('mdocs-show-upload-description', true);
	
	register_setting('mdocs-global-settings', 'mdocs-show-upload-real-author');
	add_option('mdocs-show-upload-real-author', false);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-author');
	add_option('mdocs-show-upload-author', get_option('mdocs-show-upload-real-author'));
	
	
	register_setting('mdocs-global-settings', 'mdocs-show-current-folder-on-top');
	add_option('mdocs-show-current-folder-on-top', false);
	register_setting('mdocs-global-settings', 'mdocs-show-upload-button-on-normal-page');
	add_option('mdocs-show-upload-button-on-normal-page', false);
	register_setting('mdocs-global-settings', 'mdocs-show-media-files');
	add_option('mdocs-show-media-files', false);
	register_setting('mdocs-global-settings', 'mdocs-show-advanced-search');
	add_option('mdocs-show-advanced-search', true);
	
	
	register_setting('mdocs-global-settings', 'mdocs-hide-entry-div');
	add_option('mdocs-hide-entry-div', false);
	register_setting('mdocs-global-settings', 'mdocs-override-time-offset');
	add_option('mdocs-override-time-offset', false);
	register_setting('mdocs-global-settings', 'mdocs-override-time-offset-value');
	add_option('mdocs-override-time-offset-value', 0);
	register_setting('mdocs-global-settings', 'mdocs-disable-sessions');
	add_option('mdocs-disable-sessions', false);
	register_setting('mdocs-global-settings', 'mdocs-post-target-type');
	add_option('mdocs-post-target-type', '_blank');
	register_setting('mdocs-global-settings', 'mdocs-hide-widget-titles');
	add_option('mdocs-hide-widget-titles', false);
	// *****MEGACOOKIE SAVED VARIABLES END**** //
	// GLOBAL SETTING 2
	register_setting('mdocs-global-settings-2', 'mdocs-documents-page-created');
	add_option('mdocs-documents-page-created', false);
	register_setting('mdocs-global-settings-2', 'mdocs-allowed-mime-types');
	add_option('mdocs-allowed-mime-types', array());
	if(is_string(get_option('mdocs-allowed-mime-types'))) update_option('mdocs-allowed-mime-types',array());
	register_setting('mdocs-global-settings-2', 'mdocs-removed-mime-types');
	add_option('mdocs-removed-mime-types', array());
	if(is_string(get_option('mdocs-removed-mime-types'))) update_option('mdocs-removed-mime-types',array());
	// PATCHES
	register_setting('mdocs-patch-vars', 'mdocs-patches');
	add_option('mdocs-patches', array());
}
?>