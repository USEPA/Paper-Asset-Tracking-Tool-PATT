<?php
function mdocs_check_file_rights($the_mdoc=null, $is_manage=true) {
	$is_allowed = false;
	//var_dump($_REQUEST);
	if($the_mdoc != null || isset($_GET['mdocs-export-file'])) {
		// ADMINS GET FULL RIGHTS FOR EVERY FILE
		if(current_user_can( 'mdocs_manage_settings' )) return true;
		// OWNER RIGHTS
		$current_user = wp_get_current_user();
		//var_dump($current_user);
		if(!isset($the_mdoc['owner'])) $the_mdoc['owner'] = $current_user->ID;
		if(!isset($the_mdoc['contributors'])) $the_mdoc['contributors'] = array();
		if(!isset($the_mdoc['file_status'])) $the_mdoc['file_status'] = null;
		if(!isset($the_mdoc['modified'])) $the_mdoc['modified'] = null;
		if(empty($current_user->roles)) $current_user->roles[0] = 'none';
		if($current_user->user_login == $the_mdoc['owner']) return true;
		// CONTRIBUTOR RIGHTS
		if(is_array($the_mdoc['contributors'])) {
			foreach($the_mdoc['contributors'] as $index => $role) {
				if($current_user->user_login == $role) return true; 
				if(in_array($role, $current_user->roles)) return true; 
			}
		}
		// IF NOT ANY OWNER OF THE FILE
		// MEMBER RIGHTS
		if(!isset($_REQUEST['show_type'])) $_REQUEST['show_type'] = 'none';
		if(!isset($_REQUEST['type'])) $_REQUEST['type'] = 'none';
		if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'versions') $_REQUEST['show_type'] = 'versions';
		if(is_user_logged_in() && !is_admin()) {
			if($the_mdoc['file_status'] != 'hidden' && get_option( 'mdocs-hide-all-files' ) == false) $is_allowed = true;
			if(floatval($the_mdoc['modified']) > time()) $is_allowed = false;
			return $is_allowed;
		// MEMBER RIGHTS FOR DESCRIPTION PREVIEW AND VERSION
		} elseif(is_user_logged_in() && is_admin() && $_REQUEST['show_type'] != 'none') {
			if($the_mdoc['file_status'] != 'hidden' && get_option( 'mdocs-hide-all-files' ) == false) $is_allowed = true;
			if(floatval($the_mdoc['modified']) > time()) $is_allowed = false;
			return $is_allowed;
		// MEMBER RIGHTS IF ADMIN
		} elseif(is_user_logged_in() && is_admin()) {
			return false;
		// NON-MEMBER RIGHTS
		} else {
			if(get_option( 'mdocs-hide-all-files' ) == true) return false;
			if(get_option( 'mdocs-hide-all-files-non-members' ) == true) return false;
			if(get_option( 'mdocs-hide-all-files-non-members' ) == false && get_option( 'mdocs-hide-all-files' ) == false) $is_allowed = true;
			if($the_mdoc['file_status'] === 'hidden') $is_allowed = false;
			if($the_mdoc['non_members'] === ''  ) $is_allowed = false;
			if(floatval($the_mdoc['modified']) > time()) $is_allowed = false;
			return $is_allowed;
		}
		
		//if(isset($_REQUEST['mdocs-export-file']) && current_user_can( 'mdocs_download_export' )) return false;
		//if(isset($_REQUEST['mdocs-export-file']) && !current_user_can( 'mdocs_download_export' )) return false;
		return $is_allowed;
	} else return $is_allowed;
}
function mdocs_check_post_rights($the_mdoc) {
	$current_user = wp_get_current_user();
	$is_allow = false;
	// ADMINS GET FULL RIGHTS FOR EVERY FILE
	if(current_user_can( 'manage_options' )) return true;
	// OWNER RIGHTS
	if($current_user->user_login == $the_mdoc['owner']) return true;
	// CONTRIBUTOR RIGHTS
	if(is_array($the_mdoc['contributors'])) {
		foreach($the_mdoc['contributors'] as $index => $role) {
			if($current_user->user_login == $role) return true; 
			if(in_array($role, $current_user->roles)) return true; 
		}
	}
	// POST STATUS
	if(is_user_logged_in() && get_option('mdocs-hide-all-posts')) return false;
	if(is_user_logged_in() && get_option('mdocs-hide-all-posts') == false) return true;
	if(!is_user_logged_in() && get_option('mdocs-hide-all-posts')) return false;
	if(!is_user_logged_in() && get_option('mdocs-hide-all-posts') == false) return true;
	if(!is_user_logged_in() && get_option('mdocs-hide-all-posts-non-members')) return false;
	if(get_post($the_mdoc['parent']) == null) $is_allow = false;
	return $is_allow;
}
function mdocs_contributors_check($contrib) {
	if(!is_array($contrib))  {
		return array();
	} else return $contrib;
}
function mdocs_add_update_rights($the_mdoc, $current_cat) {
	if(mdocs_check_file_rights($the_mdoc)) {
	?>
	<li role="presentation">
		<a role="menuitem" tabindex="-1" data-toggle="mdocs-modal" data-target="#mdocs-add-update" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" data-is-admin="<?php echo is_admin(); ?>" data-action-type="update-doc"  data-current-cat="<?php echo $current_cat; ?>" href=""  class="add-update-btn" >
			<i class="fas fa-file" aria-hidden="true"></i> <?php _e('Manage File','memphis-documents-library'); ?>
		</a>
	</li>
	<?php
	}
}
function mdocs_goto_post_rights($the_mdoc, $permalink) {
	if(mdocs_check_post_rights($the_mdoc)) {
		?>
		<!--<li role="presentation">
			<a role="menuitem" tabindex="-1" href="<?php echo str_replace('?mdocs-cat=','', $permalink); ?>" target="<?php echo get_option('mdocs-post-target-type'); ?>">
			<i class="fas fa-arrow-circle-right" aria-hidden="true" ></i> <?php _e('Goto Post','memphis-documents-library'); ?></a>
		</li>-->
		<?php
	}
}
function mdocs_manage_versions_rights($the_mdoc, $index, $current_cat) {
	
	if(mdocs_check_file_rights($the_mdoc)) {
		?>
		<!--<li role="presentation">
			<a class="manage-versions-button" role="menuitem" tabindex="-1" data-toggle="mdocs-modal" data-target="#mdocs-manage-versions" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" data-is-admin="<?php echo is_admin(); ?>" >
			<i class="fa fa-road" aria-hidden="true"></i> <?php _e('Manage Versions','memphis-documents-library'); ?></a>
			
			<a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=<?php echo $current_cat; ?>&action=mdocs-versions&mdocs-index=<?php echo $index; ?>"><i class="fa fa-road" aria-hidden="true"></i> <?php _e('Manage Versions','memphis-documents-library'); ?></a>
		</li>-->
		<?php
	}
}
function mdocs_download_rights($the_mdoc) {
    global $wpdb;
	if(mdocs_check_file_rights($the_mdoc) && $the_mdoc['non_members'] == 'on' || is_user_logged_in()) { 
	    
		$get_post_id = wp_get_post_parent_id( $the_mdoc['id'] );
		$get_s3_link = $wpdb->get_row("SELECT object_location FROM wpqa_wpsc_epa_folderdocinfo_files WHERE post_id = '" . $get_post_id . "'");
        $s3_link = $get_s3_link->object_location;
		?>
		<li role="presentation">
			<a role="menuitem" tabindex="-1" href="<?php echo $s3_link; ?>"><i class="fas fa-cloud-download-alt" aria-hidden="true"></i> <?php _e('Download','memphis-documents-library'); ?></a>
		</li>
		<?php
	} else { ?>
		<li role="presentation">
			<a role="menuitem" tabindex="-1" href="<?php echo wp_login_url(mdocs_sanitize_string(get_permalink($the_mdoc['parent']))); ?>"><i class="fas fa-cloud-download-alt" aria-hidden="true"></i> <?php _e('Login to Download','memphis-documents-library'); ?></a>
		</li>
		<?php
	}
}
function mdocs_preview_rights($the_mdoc) {
	global $mdocs_img_types;
	$preview_type = 'file-preview';
	if(!in_array($the_mdoc['type'], $mdocs_img_types) ) $preview_type = 'file-preview';
	else $preview_type = 'img-preview';
	if( get_option('mdocs-show-preview')) {
		?> 
		<li role="presentation">
			<a class="<?php echo $preview_type; ?>" role="menuitem" tabindex="-1" data-toggle="mdocs-modal" data-target="#mdocs-file-preview" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" data-is-admin="<?php echo is_admin(); ?>" href=""><i class="fa fa-search mdocs-preview-icon" aria-hidden="true"></i> <?php _e('Preview','memphis-documents-library'); ?></a>
		</li>
		<?php
	}
}
function mdocs_desciption_rights($the_mdocs) {
	if(get_option('mdocs-show-description')) {
	?>
	<li role="presentation"><a class="description-preview" role="menuitem" tabindex="-1" href="#" data-toggle="mdocs-modal" data-target="#mdocs-description-preview" data-mdocs-id="<?php echo $the_mdocs['id']; ?>" data-is-admin="<?php echo is_admin(); ?>" ><i class="fa fa-leaf" aria-hidden="true"></i> <?php _e('Description','memphis-documents-library'); ?></a></li>
	<?php
	}
}
function mdocs_share_rights($the_mdoc, $permalink) {
	$permalink = str_replace('?mdocs-cat=', '', $permalink);
	if(get_option('mdocs-show-share')) {
	?>
	<li role="presentation"><a class="sharing-button" role="menuitem" tabindex="-1" href="#" data-toggle="mdocs-modal" data-doc-id="<?php echo $the_mdoc['id']; ?>" data-target="#mdocs-share" data-permalink="<?php echo $permalink;?>" data-download="<?php echo get_site_url().'/?mdocs-file='.$the_mdoc['id']; ?>" ><i class="fa fa-share" aria-hidden="true"></i> <?php _e('Share','memphis-documents-library'); ?></a></li>
	<?php
	}
}
function mdocs_rating_rights($the_mdoc) {
	$sa = mdocs_get_table_atts();
	if($sa['show-rating']['show'] && is_user_logged_in()) {
	?>
	<li role="presentation"><a class="ratings-button" role="menuitem" tabindex="-1" href="" data-toggle="mdocs-modal" data-target="#mdocs-rating" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" data-is-admin="<?php echo is_admin(); ?>"><i class="fas fa-star" aria-hidden="true"></i> <?php _e('Rate','memphis-documents-library'); ?></a></li>
	<?php
	}
}
function mdocs_delete_file_rights($the_mdoc, $index, $current_cat) {
	if(mdocs_check_file_rights($the_mdoc)) {
		?>
		<li role="presentation">
			<a role="menuitem" tabindex="-1" href="#" class="mdocs-delete-file" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" data-mdocs-cat="<?php echo $current_cat; ?>" ><i class="fa fa-times-circle" aria-hidden="true"></i> <?php _e('Delete File','memphis-documents-library'); ?></a>
		</li>
		<?php
	}
}
function mdocs_refresh_box_view($the_mdoc, $index) {
	if(mdocs_check_file_rights($the_mdoc)) {
		?>
		<li role="presentation"><a class="box-view-refresh-button" role="menuitem" tabindex="-1" href="#" data-toggle="mdocs-modal" data-id="<?php echo $the_mdoc['id']; ?>" data-index="<?php echo $index; ?>" data-filename="<?php echo $the_mdoc['filename']; ?>" ><i class="fa fa-sync" aria-hidden="true"></i> <?php _e('Refresh Preview and Thumbnail','memphis-documents-library'); ?></a></li>
		<?php
	}
}
function mdocs_versions_rights($the_mdoc) {
	if(get_option('mdocs-show-versions') && !is_admin() || isset($_REQUEST['is_dashboard']) && $_REQUEST['is_dashboard'] == '') {
		?>
		<li role="presentation"><a class="versions-button" role="menuitem" tabindex="-1" href="#" data-toggle="mdocs-modal" data-target="#mdocs-versions" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" ><i class="fa fa-code-branch" aria-hidden="true"></i> <?php _e('Other Versions','memphis-documents-library'); ?></a></li>
		<?php
	}
}
?>