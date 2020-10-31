<?php
function mdocs_batch_edit() {
	global $current_user;
	$date_format = get_option('mdocs-date-format');
	$ids = json_decode($_POST['ids'])
	?>
	<h1><?php _e('Batch Edit', 'memphis-documents-library'); ?></h1>
	<?php
	$mdocs = get_option('mdocs-list');
	$cats = get_option('mdocs-cats');
	?>
	<form id="mdocs-batch-edit-form">
		<table class="table table-striped table-hover mdocs-batch-edit-table mdocs-batch-tables">
			<tr>
				<th id="mdocs-batch-edit-name"><?php _e('Name' ,'memphis-documents-library'); ?></th>
				<?php if(method_exists('DateTime', 'createFromFormat')) { ?>
				<th id="mdocs-batch-edit-modified"><?php _e('Date Modified' ,'memphis-documents-library'); ?></th>
				<?php } ?>
				<th id="mdocs-batch-edit-version"><?php _e('Version' ,'memphis-documents-library'); ?></th>
				<th id="mdocs-batch-edit-dl"><?php _e('DL' ,'memphis-documents-library'); ?></th>
				<th id="mdocs-batch-edit-ss"><?php _e('SS' ,'memphis-documents-library'); ?></th>
				<th id="mdocs-batch-edit-nm"><?php _e('NM' ,'memphis-documents-library'); ?></th>
				<th id="mdocs-batch-edit-fs"><?php _e('FS' ,'memphis-documents-library'); ?></th>
				<th id="mdocs-batch-edit-ps"><?php _e('PS' ,'memphis-documents-library'); ?></th>
				<th id="mdocs-batch-edit-folder"><?php _e('Folder' ,'memphis-documents-library'); ?></th>
				<th id="mdocs-batch-edit-contributors"><?php _e('Contributors' ,'memphis-documents-library'); ?></th>
			</tr>
	<?php
	foreach($mdocs as $index => $the_mdoc) {
		foreach($ids as $id) {
			if($id == $the_mdoc['id']) {
				if(is_admin()) { 
					if($the_mdoc['contributors'] != null) {
						foreach($the_mdoc['contributors'] as $user) {
							$contributor = false;
							if($current_user->user_login == $user) { $contributor = true; break; }
							if(in_array($user, $current_user->roles)) { $contributor = true; break; }
						}
					} else $contributor = false;
					if(empty($current_user->roles)) $current_user->roles[0] = 'none';
					if($contributor == true || $the_mdoc['owner'] == $current_user->user_login || $current_user->roles[0] == 'administrator') {
						$the_date = mdocs_format_unix_epoch($the_mdoc['modified'], true); 
						?>
						<tr>
							<td>
								<input type="text" name="mdocs-batch-edit[<?php echo $index; ?>][name]" class="form-control" placeholder="Name" value="<?php echo stripcslashes($the_mdoc['name']); ?>">
								<small class="text-muted"><i><?php echo $the_mdoc['filename']; ?></i></small>
							</td>
							<?php if(method_exists('DateTime', 'createFromFormat'))  { ?>
							<td>
								<input type="text" name="mdocs-batch-edit[<?php echo $index; ?>][modifed]" class="form-control" placeholder="<?php echo date('Y-m-d'); ?>" value="<?php echo $the_date['formated-date']; ?>" >
							</td>
							<?php } ?>
							<td><input type="text" name="mdocs-batch-edit[<?php echo $index; ?>][version]" class="form-control" placeholder="1.0" value="<?php echo $the_mdoc['version']; ?>" ></td>
							<td><input type="text" name="mdocs-batch-edit[<?php echo $index; ?>][downloads]" class="form-control" placeholder="0" value="<?php echo $the_mdoc['downloads']; ?>" ></td>
							<td><input type="checkbox" name="mdocs-batch-edit[<?php echo $index; ?>][show-social]" <?php if($the_mdoc['show_social'] == 'on') echo 'checked'; ?> ></td>
							<td><input type="checkbox" name="mdocs-batch-edit[<?php echo $index; ?>][non-members]" <?php if($the_mdoc['non_members'] == 'on') echo 'checked'; ?> ></td>
							<td>
								<select class="form-control" name="mdocs-batch-edit[<?php echo $index; ?>][file-status]" >
									<option value="public" <?php if($the_mdoc['file_status'] == 'public') echo 'selected'; ?>><?php _e('Public','memphis-documents-library'); ?></option>
									<option value="hidden" <?php if($the_mdoc['file_status'] == 'hidden') echo 'selected'; ?>><?php _e('Hidden','memphis-documents-library'); ?></option>
								</select>
							</td>
							<td>
								<select class="form-control" name="mdocs-batch-edit[<?php echo $index; ?>][post-status]" >
									<option value="publish" <?php if($the_mdoc['post_status'] == 'publish') echo 'selected'; ?> ><?php _e('Published','memphis-documents-library'); ?></option>
									<option value="private" <?php if($the_mdoc['post_status'] == 'private') echo 'selected'; ?> ><?php _e('Private','memphis-documents-library');  ?></option>
									<option value="pending"  <?php if($the_mdoc['post_status'] == 'pending') echo 'selected'; ?> ><?php _e('Pending Review','memphis-documents-library');  ?></option>
									<option value="draft" <?php if($the_mdoc['post_status'] == 'draft') echo 'selected'; ?> ><?php _e('Draft','memphis-documents-library');  ?></option>		
								</select>
							</td>
							<td>
								<select class="form-control" name="mdocs-batch-edit[<?php echo $index; ?>][cat]">
									<?php mdocs_display_folder_options_menu($cats, $the_mdoc['cat']); ?>
								</select>
							</td>
							<td>
								
								<div class="mdocs-add-contributor-container" data-contributor-type="batch-edit" data-mdocs-index="<?php echo $index; ?>">
									<input autocomplete="off" class="form-control mdocs-add-contributors" type="text" name="mdocs-batch-edit-add-contributors" class="" placeholder="<?php _e('Enter a user or role type.', 'memphis-documents-library'); ?>" >
									<div class="mdocs-user-search-list hidden" data-mdocs-id="<?php echo $index; ?>"></div>
									<input type="hidden" name="mdocs-owner-value" value="<?php echo $the_mdoc['owner']; ?>" >
									<div class="mdocs-contributors-container" data-mdocs-index="<?php echo $index; ?>">
										<?php
										foreach($the_mdoc['contributors'] as $id => $contributor) {
											?>
											<input type="hidden" name="mdocs-batch-edit[<?php echo $index; ?>][contributors][<?php echo $id; ?>]" value="<?php echo $contributor; ?>" >
											<button type="button" class="btn btn-sm btn-success" data-mdocs-index="<?php echo $index; ?>" data-contributor-index="<?php echo $id; ?>"><i class="fa fa-user" aria-hidden="true"></i> <?php echo $contributor; ?> <i class="fa fa-times mdocs-contributors-delete-btn" id="" aria-hidden="true"></i></button>
											<?php
										}
										?>
									</div>
								</div>
							</td>
						</tr>
						<?php
					} 
				}
			}
		}
	}
	?>
		</table>
		<ul class="list-group">
			<li class="list-group-item active"><?php _e('Legend','memphis-documents-library');  ?></li>
			<li class="list-group-item"><?php _e('Name','memphis-documents-library');  ?> = <?php _e('Name','memphis-documents-library');  ?></li>
			<li class="list-group-item"><?php _e('Date Modified','memphis-documents-library');  ?> = <?php _e('Date Modified','memphis-documents-library');  ?></li>
			<li class="list-group-item"><?php _e('Ver','memphis-documents-library');  ?> = <?php _e('Version','memphis-documents-library');  ?></li>
			<li class="list-group-item"><?php _e('DL','memphis-documents-library');  ?> = <?php _e('Downloads','memphis-documents-library');  ?></li>
			<li class="list-group-item"><?php _e('SS','memphis-documents-library');  ?> = <?php _e('Show Social Media Buttons','memphis-documents-library');  ?></li>
			<li class="list-group-item"><?php _e('NM','memphis-documents-library');  ?> = <?php _e('Downloadable by Non Members','memphis-documents-library');  ?></li>
			<li class="list-group-item"><?php _e('FS','memphis-documents-library');  ?> = <?php _e('File Status','memphis-documents-library');  ?></li>
			<li class="list-group-item"><?php _e('PS','memphis-documents-library');  ?> = <?php _e('Post Status','memphis-documents-library');  ?></li>
			<li class="list-group-item"><?php _e('Folder','memphis-documents-library');  ?> = <?php _e('Folder','memphis-documents-library');  ?></li>
		</ul>
		<div id="mdocs-batch-edit-test"></div>
	</form>
	<?php
};
function mdocs_batch_edit_save() {
	$form_data = array();
	parse_str($_POST['data'], $form_data);
	//var_dump($form_data['mdocs-batch-edit']);
	$mdocs = get_option('mdocs-list');
	
	$date_format = get_option('mdocs-date-format');
	foreach($form_data['mdocs-batch-edit'] as $index => $doc_data) {
		$mdocs[$index]['name'] = sanitize_text_field((string)$doc_data['name']);
		$date = mdocs_format_date($doc_data['modifed']);
		if($date['gmdate'] > time() && $doc_data['post-status'] === 'publish') $post_status = 'future';
		else $post_status = (string)$doc_data['post-status'];
		$mdocs[$index]['modified'] = intval(sanitize_text_field($date['gmdate']));
		$mdocs[$index]['version'] = sanitize_text_field((string)$doc_data['version']);
		$mdocs[$index]['downloads'] = intval(sanitize_text_field($doc_data['downloads']));
		$mdocs[$index]['show_social'] = sanitize_text_field((string)$doc_data['show-social']);
		$mdocs[$index]['non_members'] = sanitize_text_field((string)$doc_data['non-members']);
		$mdocs[$index]['file_status'] = sanitize_text_field((string)$doc_data['file-status']);
		$mdocs[$index]['post_status'] = sanitize_text_field((string)$doc_data['post-status']);
		$mdocs[$index]['cat'] = sanitize_text_field((string)$doc_data['cat']);
		if($doc_data['contributors'] != null) $mdocs[$index]['contributors'] = $doc_data['contributors'];
		elseif($doc_data['contributors'] == null) $mdocs[$index]['contributors'] = array();
		$mdocs_post = array(
			'ID' => $mdocs[$index]['parent'],
			'post_title' => $mdocs[$index]['name'],
			'post_status' => $post_status,
			'post_date' => $date['wp-date'],
			'post_date_gmt' => $date['wp-gmdate'],
		);
		$mdocs_post_id = wp_update_post( $mdocs_post );
	}
	//var_dump($mdocs[1]);
	//die();
	mdocs_save_list($mdocs);
}
function mdocs_batch_move() {
	global $current_user;
	$ids = json_decode($_POST['ids']);
	?>
	<h1><?php _e('Batch Move', 'memphis-documents-library'); ?></h1>
	<?php
	$mdocs = get_option('mdocs-list');
	$cats = get_option('mdocs-cats');
	?>
	<form id="mdocs-batch-move-form">
		<h4><?php _e('Select the folder you want to transfer the files to', 'memphis-documents-library'); ?>:</h4>
		<select class="form-control" name="mdocs-batch-move-cat">
			<?php mdocs_display_folder_options_menu($cats); ?>
		</select>
		<h4><?php _e('List of files', 'memphis-documents-library'); ?>:</h4>
		<table class="table table-striped table-hover mdocs-batch-tables">
			<tr>
				<th><?php _e('File', 'memphis-documents-library'); ?></th>
				<th><?php _e('Filename', 'memphis-documents-library'); ?></th>
				<th id="mdocs-batch-move-checked"></th>
			</tr>
			<?php
			foreach($mdocs as $index => $the_mdoc) {
				foreach($ids as $id) {
					if($id == $the_mdoc['id']) {
						if(is_admin()) { 
							if($the_mdoc['contributors'] != null) {
								foreach($the_mdoc['contributors'] as $user) {
									$contributor = false;
									if($current_user->user_login == $user) { $contributor = true; break; }
									if($current_user->roles[0] = $user) { $contributor = true; break; }
								}
							} else $contributor = false;
							if(empty($current_user->roles)) $current_user->roles[0] = 'none';
							if($contributor == true || $the_mdoc['owner'] == $current_user->user_login || $current_user->roles[0] == 'administrator') {
								?>
								<tr>
									<td><?php echo $the_mdoc['filename']; ?></td>
									<td><?php echo stripcslashes($the_mdoc['name']); ?></td>
									<td>
										<input type="checkbox" name="mdocs-batch-move[<?php echo $index; ?>][checked]" checked>
									</td>
								</tr>
								<?php
							}
						}
					}
				}
			}
			?>
		</table>
	</form>
	<?php
}
function mdocs_batch_move_save() {
	$form_data = array();
	parse_str($_POST['data'], $form_data);
	$mdocs = get_option('mdocs-list');
	foreach($form_data['mdocs-batch-move'] as $index => $doc_data) {
		$mdocs[$index]['cat'] = $form_data['mdocs-batch-move-cat'];
	}
	mdocs_save_list($mdocs);
}
function mdocs_batch_delete() {
	global $current_user;
	$ids = json_decode($_POST['ids']);
	?>
	<h1><?php _e('Batch Delete', 'memphis-documents-library'); ?></h1>
	<?php
	$mdocs = get_option('mdocs-list');
	?>
	<form id="mdocs-batch-delete-form">
		<h4><?php _e('Are you sure you want to delete these files', 'memphis-documents-library'); ?>:</h4>
		<table class="table table-striped table-hover mdocs-batch-tables">
			<tr>
				<th><?php _e('File', 'memphis-documents-library'); ?></th>
				<th><?php _e('Filename', 'memphis-documents-library'); ?></th>
				<th id="mdocs-batch-delete-checked"></th>
			</tr>
			<?php
			foreach($mdocs as $index => $the_mdoc) {
				foreach($ids as $id) {
					if($id == $the_mdoc['id']) {
						if(is_admin()) { 
							if($the_mdoc['contributors'] != null) {
								foreach($the_mdoc['contributors'] as $user) {
									$contributor = false;
									if($current_user->user_login == $user) { $contributor = true; break; }
									if(in_array($user, $current_user->roles)) { $contributor = true; break; }
								}
							} else $contributor = false;
							if(empty($current_user->roles)) $current_user->roles[0] = 'none';
							if($contributor == true || $the_mdoc['owner'] == $current_user->user_login || $current_user->roles[0] == 'administrator') {
								?>
								<tr>
									<td>
										<?php
										echo $the_mdoc['filename'];
										if(count($the_mdoc['archived']) > 0) echo '<div class="well well-sm"><small class="text-primary">'.__('Previous Versions', 'memphis-documents-library').'</small>';
										?>
										<ul>
											<?php foreach($the_mdoc['archived'] as $a_index => $archive) { ?>
											<li><i><small class="text-info"><?php echo $archive; ?></small></i></li>
											<?php  } ?>
										</ul>
										<?php if(count($the_mdoc['archived']) > 0) echo '</div>'; ?>
									</td>
									<td><?php echo stripcslashes($the_mdoc['name']); ?></td>
									<td>
										<input type="checkbox" name="mdocs-batch-delete[<?php echo $index; ?>][checked]" checked>
									</td>
								</tr>
								<?php
							}
						}
					}
				}
			}
			?>
		</table>
	</form>
	<?php
}
function mdocs_batch_delete_save() {
	$form_data = array();
	parse_str($_POST['data'], $form_data);
	$mdocs = get_option('mdocs-list');
	$upload_dir = wp_upload_dir();
	foreach($form_data['mdocs-batch-delete'] as $index => $doc_data) {
		if(is_array($mdocs[$index]['archived'])) foreach($mdocs[$index]['archived'] as $key => $value) @unlink($upload_dir['basedir'].'/mdocs/'.$value);
		wp_delete_attachment( intval($mdocs[$index]['id']), true );
		wp_delete_post( intval($mdocs[$index]['parent']), true );
		if(get_option('mdocs-box-view-key') != ''  && get_option('mdocs-preview-type') == 'box') {
			$boxview = new mdocs_box_view();
			$boxview->deleteFile($mdocs[$index]);
		}
		if(file_exists($upload_dir['basedir'].'/mdocs/'.$mdocs[$index]['filename'])) @unlink($upload_dir['basedir'].'/mdocs/'.$mdocs[$index]['filename']);
		unset($mdocs[$index]);
	}
	$mdocs = array_values($mdocs);
	mdocs_save_list($mdocs);
	
}
?>