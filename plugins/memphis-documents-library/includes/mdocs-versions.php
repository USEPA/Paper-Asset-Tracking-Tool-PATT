<?php
function mdocs_versions() {
	$the_mdoc = mdocs_get_file_by($_REQUEST['mdocs-id'], 'id');
	$upload_dir = wp_upload_dir();
	$date_format = get_option('mdocs-date-format');
	$the_date = mdocs_format_unix_epoch($the_mdoc['modified'], true);
	$the_mdoc = mdocs_get_file_by($_POST['mdocs-id'], 'id');
	if(mdocs_check_file_rights($the_mdoc)) {
?>
	<script>
		mdocs_delete_version();
		mdocs_update_to_revision();
	</script>
	<h1 class="text-primary"><?php echo __('Versions','memphis-documents-library'); ?></h1>
	<h2 class="text-success"><?php echo stripcslashes($the_mdoc['name']); ?> - <i><small><?php echo $the_mdoc['filename']; ?></small></i></h2>
	<form enctype="multipart/form-data" action="" method="POST" id="mdocs-update-to-revision-form" data-folder="<?php echo $_REQUEST['folder']; ?>" data-sort="<?php echo $_REQUEST['sort']; ?>" data-sort-type="<?php echo $_REQUEST['sort-type']; ?>" data-is-dashboard="<?php echo $_REQUEST['is-dashboard']; ?>" >
		<input type="hidden" name="mdocs-id" value="<?php echo $the_mdoc['id']; ?>" />
		<table  class="table table-striped">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-name" ><?php _e('File','memphis-documents-library'); ?></th>
					<th scope="col" class="manage-column column-name" ><?php _e('Version','memphis-documents-library'); ?></th>
					<th scope="col" class="manage-column column-name" ><?php _e('Date Modified','memphis-documents-library'); ?></th>
					<th scope="col" class="manage-column column-name" ><?php _e('Download','memphis-documents-library'); ?></th>
					<th scope="col" class="manage-column column-name" ><?php _e('Delete','memphis-documents-library'); ?></th>
					<th scope="col" class="manage-column column-name" ><?php _e('Current','memphis-documents-library'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col" class="manage-column column-name" ><?php _e('File','memphis-documents-library'); ?></th>
					<th scope="col" class="manage-column column-name" ><?php _e('Version','memphis-documents-library'); ?></th>
					<th scope="col" class="manage-column column-name" ><?php _e('Date Modified','memphis-documents-library'); ?></th>
					<th scope="col" class="manage-column column-name" ><?php _e('Download','memphis-documents-library'); ?></th>
					<th scope="col" class="manage-column column-name" ><?php _e('Delete','memphis-documents-library'); ?></th>
					<th scope="col" class="manage-column column-name" ><?php _e('Current','memphis-documents-library'); ?></th>
				</tr>
			</tfoot>
			<tbody id="the-list">
					<tr>
						<td class="mdocs-blue" id="file" ><?php echo $the_mdoc['filename']; ?></td>
						<td class="mdocs-green" id="version" ><?php echo $the_mdoc['version']; ?></td>
						<td class="mdocs-red" id="date"><?php  echo $the_date['formated-date']; ?></td>
						<td id="download"><input type="button" id="mdocs-download" onclick="mdocs_download_current_version('<?php echo $the_mdoc['id']; ?>')" class="btn btn-primary" value=<?php _e("Download", 'memphis-documents-library'); ?>  /></td>
						<td></td>
						<td><input type="radio" name="mdocs-update-to-revision-radio" value="current" checked data-file="<?php echo $the_mdoc['filename']; ?>" data-version="<?php echo $the_mdoc['version']; ?>" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" /></td>
					</tr>
				</tr>
			<?php
				foreach( array_reverse($the_mdoc['archived']) as $key => $archive ){
					$file = substr($archive, 0, strrpos($archive, '-'));
					$version = substr(strrchr($archive, '-'), 2 );
					if(file_exists($upload_dir['basedir'].'/mdocs/'.$archive) || file_exists($upload_dir['basedir'].'/mdocs/'.strtolower($archive))) {
						if(file_exists($upload_dir['basedir'].'/mdocs/'.strtolower($archive))) $archive = strtolower($archive);
						$archive_date_modified = date($date_format, filemtime($upload_dir['basedir'].'/mdocs/'.$archive)+MDOCS_TIME_OFFSET);
						?>
						<tr >
							<td class="mdocs-blue" id="file" ><?php echo $file; ?></td>
							<td class="mdocs-green" id="version" ><?php echo $version; ?></td>
							<td class="mdocs-red" id="date"><?php  echo $archive_date_modified; ?></td>
							<td id="download"><input onclick="mdocs_download_version('<?php echo $the_mdoc['id']; ?>', '<?php echo $archive; ?>')" type="button" id="mdocs-download" name="<?php echo $key; ?>" class="btn btn-primary" value="<?php _e("Download"); ?>"  /></td>
							<td id="delete-version">
								<input type="button" class="btn btn-danger" name="mdocs-delete-version-btn" value="<?php _e('Delete', 'memphis-documents-library'); ?>" data-archive="<?php echo $archive; ?>" data-mdocs-id="<?php echo $the_mdoc['id']; ?>"/>
							</td>
							<td><input type="radio" name="mdocs-update-to-revision-radio" value="" data-file="<?php echo $file; ?>" data-version="<?php echo $version; ?>" data-mdocs-id="<?php echo $the_mdoc['id']; ?>"  /></td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
		<br/>
		<script>
			mdocs_refresh_table();
		</script>
		<input type="submit" class="btn btn-primary" value="<?php _e('Update To Revision') ?>" />
		<!--<button id="mdocs-manage-version-close-btn" type="button" class="btn btn-default " data-dismiss="modal" data-folder="<?php echo $_REQUEST['folder']; ?>" data-sort="<?php echo $_REQUEST['sort']; ?>" data-sort-type="<?php echo $_REQUEST['sort-type']; ?>" data-is-dashboard="<?php echo $_REQUEST['is-dashboard']; ?>" ><?php _e('Close','memphis-documents-library'); ?></button>-->
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
	</form>
<?php
	}
}

function mdocs_delete_version() {
	$mdocs = mdocs_array_sort();
	$the_mdoc = mdocs_get_file_by($_POST['mdocs-id'], 'id');
	$the_mdocs_index = mdocs_get_file_index_by($_POST['mdocs-id'], 'id');
	$upload_dir = wp_upload_dir();
	$archive_index = array_search($_POST['archive'], $the_mdoc['archived']);
	$version_file = $the_mdoc['archived'][$archive_index];
	unset($the_mdoc['archived'][$archive_index]);
	$the_mdoc['archived'] = array_values($the_mdoc['archived']);
	$mdocs[$the_mdocs_index] = $the_mdoc;
	mdocs_save_list($mdocs);
	unlink($upload_dir['basedir'].'/mdocs/'.$version_file);	
	$thumbnail_search = substr($version_file, 0, strrpos($version_file, '-', -1));
	$thumbnail_search = substr($thumbnail_search, 0, strrpos($thumbnail_search, '.', -1));
	$thumbnails = glob($upload_dir['basedir'].'/mdocs/'.$thumbnail_search.'-*');
	foreach($thumbnails as $thumbnail) unlink($thumbnail);	
}

function mdocs_update_to_revision() {
	if($_REQUEST['current'] != 'current') {
		global $current_user;
		$_REQUEST['file'] = sanitize_text_field($_REQUEST['file']);
		$_REQUEST['file'] = basename($_REQUEST['file']);
		$mdocs = mdocs_array_sort();
		$the_mdoc = mdocs_get_file_by($_REQUEST['mdocs-id'], 'id');
		$the_mdoc_index = mdocs_get_file_index_by($_REQUEST['mdocs-id'], 'id');
		$upload_dir = wp_upload_dir();
		$file_type = wp_check_filetype($_REQUEST['file']);
		// RENAME OLD FILE TO REVISION
		$old_revision_name = $the_mdoc['filename'].'-v'.preg_replace('/ /','',$the_mdoc['version']);
		rename($upload_dir['basedir'].'/mdocs/'.$the_mdoc['filename'],$upload_dir['basedir'].'/mdocs/'.$old_revision_name);
		$the_file = explode('.',$the_mdoc['filename']);
		foreach(glob($upload_dir['basedir'].'/mdocs/'.$the_file[0].'-*.'.$the_file[1]) as $filename) {
			unlink($filename);
		}
		// RENAME REVISION TO NEW FILE
		$the_revision_file =  $_REQUEST['file'].'-v'.$_REQUEST['version'];
		$the_revision_file_type =  $file_type['ext'];
		$archive_index = array_search($the_revision_file, $the_mdoc['archived']);
		unset($mdocs[$the_mdoc_index]['archived'][$archive_index]);
		$mdocs[$the_mdoc_index]['archived'] = array_values($mdocs[$the_mdoc_index]['archived']);
		rename($upload_dir['basedir'].'/mdocs/'.$the_revision_file, $upload_dir['basedir'].'/mdocs/'.$_REQUEST['file']);
		// UPDATE MDOCS LIST
		$mdocs[$the_mdoc_index]['filename'] = $_REQUEST['file'];
		$mdocs[$the_mdoc_index]['name'] = $the_mdoc['name'];
		$mdocs[$the_mdoc_index]['version'] = mdocs_increase_minor_version($the_mdoc['version']);
		$mdocs[$the_mdoc_index]['type'] = (string)$the_revision_file_type;
		$mdocs[$the_mdoc_index]['size'] = (string)filesize($upload_dir['basedir'].'/mdocs/'.$_REQUEST['file']);
		$the_date = mdocs_format_unix_epoch(time());
		$mdocs[$the_mdoc_index]['modified'] = $the_date['gmdate'];
		array_push($mdocs[$the_mdoc_index]['archived'], $old_revision_name);
		mdocs_save_list($mdocs);
		// UPDATE WORDPRESS PAGES
		$date = mdocs_format_date(time());
		$mdocs_post = array(
			'ID' => $the_mdoc['parent'],
			'post_author' => $current_user->ID,
			'post_date' => $date['wp-date'],
			'post_date_gmt' => $date['wp-gmdate'],
		);
		$mdocs_post_id = wp_update_post( $mdocs_post );
		$attachment = array(
			'ID' => $the_mdoc['id'],
			'post_mime_type' => $file_type['type'],
			'post_title' => $the_mdoc['name'],
			'post_author' => $current_user->ID,
			'post_date' => $date['wp-date'],
			'post_date_gmt' => $date['wp-gmdate'],
		 );
		update_attached_file( $the_mdoc['id'], $upload_dir['basedir'].'/mdocs/'.$_REQUEST['file'] );
		$mdocs_attach_id = wp_update_post( $attachment );
		$mdocs_attach_data = wp_generate_attachment_metadata( $mdocs_attach_id, $upload_dir['basedir'].'/mdocs/'.$_REQUEST['file'] );
		wp_update_attachment_metadata( $mdocs_attach_id, $mdocs_attach_data );
		mdocs_versions();
	} else {
		mdocs_versions();
		?>
		<br>
		<div class="alert alert-info text-center"><?php _e('You are already at the most recent version of this document.', 'memphis-documents-library'); ?></div>
		<?php
	}
	
}

?>