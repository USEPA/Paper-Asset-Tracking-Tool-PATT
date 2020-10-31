<?php
function mdocs_batch_upload($current_cat) {
	// INPUT SANITIZATION
	$post_page = sanitize_text_field($_REQUEST['page']);
	$post_cat = sanitize_text_field($_REQUEST['mdocs-cat']);
	$do_zip = false;
	//$mdocs = get_option('mdocs-list');
	$cats = get_option('mdocs-cats');
	$do_complte = false;
	if(isset($_REQUEST['mdocs-action']) && $_REQUEST['mdocs-action'] ==__('Process FTP Folder','memphis-documents-library')) {
		$upload_dir = wp_upload_dir();
		$get_files = scandir($upload_dir['basedir'].'/mdocs-ftp/');
		$zip_result = array();
		$zip_result['file'] = array();
		foreach($get_files as $ftp_file) {
			if($ftp_file != '.' && $ftp_file != '..' && $ftp_file != '__MACOSX' && $ftp_file != '.DS_Store') array_push($zip_result['file'],$upload_dir['basedir'].'/mdocs-ftp/'.$ftp_file);
		}
		if(count($zip_result['file']) == 0) $is_error = 'There was no files found in the mDocs FTP directory';
		else $is_error = '';
		$do_zip = true;
	} elseif(isset($_FILES['mdocs-batch']) && $_FILES['mdocs-batch']['name'] != '') {
		$string = '<p>'.__('Zip Upload Output:','memphis-documents-library').'</p>';
		foreach($_FILES['mdocs-batch'] as $index => $value) {
			if($index == 'error') {
				$error_value = $value;
				$string .=  $index.' ==> '.mdocs_file_upload_errors($value).'<br>';
			}
			else $string .=  $index.' ==> '.$value.'<br>';
		}
		$string .= '</p>';
		if(MDOCS_DEV || $error_value > 0) {
			$is_error = $string;
		} else $is_error = '';
		
		
		if(!file_exists(sys_get_temp_dir().'/mdocs/')) mkdir(sys_get_temp_dir().'/mdocs/');
		$zip_result = mdocs_unzip($_FILES['mdocs-batch']['tmp_name'], sys_get_temp_dir());
		$do_zip = true;
	} elseif (isset($_REQUEST['mdocs-batch-complete'])) {
		$do_complte = true;
	}
	mdocs_list_header();
?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('Batch Library Upload','memphis-documents-library'); ?></h3>
	</div>
	<div class="panel-body">
		<p><?php _e('There are two batch file processing methods, the zip process is the quickest way but is sometime unreliable.  The FTP method takes a little longer and you need FTP access to your server but is more reliable.', 'memphis-documents-library'); ?></p>
		<h3>Zip Method</h3>
		<p><?php _e('Create a zip file of all the documents you want to upload.  You may name the file whatever you want.  Once you have created the file, simply upload it, then use the quick select form to place the files in the proper directory.  Once satisfied press the \'Complete\' button to finsh the process.','memphis-documents-library'); ?></p>
		<h3>FTP Method</h3>
		<p><?php _e('Using a FTP application connect to your sever and navagate to this folder:','memphis-documents-library'); ?>
		<em><i><?php
		$upload_dir = wp_upload_dir();
		echo $upload_dir['basedir'].'/mdocs-ftp/.  ';
		?></i></em>
		<?php _e('Upload all your files to this folder then return to Memphis Documents Library and press the "Process FTP Folder" button.','memphis-documents-library'); ?></p>
		<h4><?php _e('NOTE: Depending on the amout of files, batch upload can take a long time, please be patient.', 'memphis-documents-library'); ?></h4>

<?php if($do_zip == false && $do_complte == false) { ?>
		<form class="mdocs-uploader-form" enctype="multipart/form-data" action="<?php echo get_site_url().'/wp-admin/admin.php?page='.$post_page.'&mdocs-cat='.$post_cat; ?>" method="POST">
			<label><?php _e('Default Folder','memphis-documents-library'); ?>:</label>
			<select name="mdocs[cat]">
					<?php mdocs_display_folder_options_menu($cats, $current_cat); ?>
			</select>
			<input type="file" name="mdocs-batch" /><br><br>
			<input type="submit" name="mdocs-action" class="btn btn-primary" value="<?php _e('Upload Zip File','memphis-documents-library') ?>" />
			<input type="submit" name="mdocs-action" class="btn btn-danger" value="<?php _e('Process FTP Folder','memphis-documents-library') ?>" /><br/>
		</form>
	</div>
</div>
<?php } elseif($do_zip) {
	$cats = get_option('mdocs-cats');
	if(!isset($_REQUEST['mdocs']['cat'])) $current_cat = key($cats);
	else $current_cat = $_REQUEST['mdocs']['cat'];
	?>
		<p><?php if($is_error != '') mdocs_errors($is_error); ?></p>
		<form class="mdocs-uploader-form" enctype="multipart/form-data" action="<?php echo get_site_url().'/wp-admin/admin.php?page='.$post_page.'&mdocs-cat='.$post_cat; ?>" method="POST">
			<input type="hidden" name="mdocs-batch-complete" value="1" />
			<input type="hidden" name="mdocs-type" value="mdocs-add" />
			<?php
			if($zip_result != null && isset($zip_result['file'])) {
				?>
				<table class="table table-hover table-striped">
					<tr>
						<th><?php _e('Filename', 'memphis-documents-library'); ?></th>
						<th><?php _e('Default Folder', 'memphis-documents-library'); ?></th>
						<th><?php _e('Version', 'memphis-documents-library'); ?></th>
					</tr>
				<?php
				foreach($zip_result['file'] as $index => $zip_file) {
					$file = basename(sanitize_text_field($zip_file));
					if(get_option('mdocs-convert-to-latin')) $filename = mdocs_filenames_to_latin($file);
					else $filename = $file;
					$ext = strrchr($file,'.');
					$file = str_replace($ext, '', $file);
					?>
					<tr>
						<td>
							<input type="hidden" name="mdocs[filename][<?php echo $index; ?>]" value="<?php echo $filename; ?>" />
							<input type="hidden" name="mdocs[tmp-file][<?php echo $index; ?>]" value="<?php echo $zip_file; ?>" />
							<input type="text" name="mdocs[name][<?php echo $index; ?>]" value="<?php echo $file; ?>"/> <?php echo mdocs_convert_bytes(filesize($zip_file)); ?>
						</td>
						<td>
							<select name="mdocs[cat][<?php echo $index; ?>]">
								<?php mdocs_display_folder_options_menu($cats, $current_cat); ?>
							</select>
						</td>
						<td>
							<input type="text" name="mdocs[version][<?php echo $index; ?>]" value="1.0" />
						</td>
					</tr>
					
					<?php
				}
				?>
				</table>
				<br>
				<input type="submit" class="btn btn-primary" value="<?php _e('Complete','memphis-documents-library') ?>" />
				<br/>
			</form>
		</div>
	</div>
		<?php
		} else {
			mdocs_errors(__('The zip file you are trying to upload can not be unzipped.', 'memphis-documents-library').'<br><br>'.__('Error: ', 'memphis-documents-library'). mdocs_zip_archive_errors($zip_result), 'error');
			mdocs_rmdir(sys_get_temp_dir().'/mdocs-import');
			?>
			<br>
			<form class="mdocs-uploader-form" enctype="multipart/form-data" action="<?php echo get_site_url().'/wp-admin/admin.php?page='.$post_page.'&mdocs-cat='.$post_cat; ?>" method="POST">
				<label><?php _e('Default Folder','memphis-documents-library'); ?>:
				<select name="mdocs[cat]">
						<?php mdocs_display_folder_options_menu($cats, $current_cat); ?>
				</select>
				</label><br><br>
				<input type="file" name="mdocs-batch" /><br>
				<input type="submit" class="btn btn-primary" value="<?php _e('Upload Zip File','memphis-documents-library') ?>" /><br/>
			</form>
		</div>
	</div>
			<?php
		}
} elseif ($_REQUEST['mdocs-batch-complete'] ) {
	$file = array();
	$current_user = wp_get_current_user();
	$batch_log = '';
	if(isset($_REQUEST['mdocs'])) {
		
		$_REQUEST['mdocs'] = mdocs_sanitize_array($_REQUEST['mdocs']);
		foreach($_REQUEST['mdocs']['tmp-file'] as $index => $tmp) {
			$valid_mime_type = false;
			$file['name'] = $_REQUEST['mdocs']['filename'][$index];
			$result = wp_check_filetype($tmp);
			$file['tmp_name'] = $tmp;
			$file['error'] = 0;
			if(file_exists($tmp)) $file['size'] = filesize($tmp);
			$file['post_status'] = 'publish';
			$file['post-status'] = 'publish';
			$mdocs_fle_type = substr(strrchr($file['name'], '.'), 1 );
			//MDOCS FILE TYPE VERIFICATION	
			$mimes = get_allowed_mime_types();
			foreach ($mimes as $type => $mime) {
			  if ($mime === $result['type']) {
				$valid_mime_type = true;
				break;
			  }
			}
			$batch_log .= __('Processed File => ','memphis-documents-library').$file['name']."<br>";
			if($valid_mime_type) {
				$upload = mdocs_process_file($file);
				if(!isset($upload['error'])) {
					$mdocs = get_option('mdocs-list');
					if(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '') {
						$is_image = @getimagesize($upload['file']);
						if($is_image == false && pathinfo($upload['file'], PATHINFO_EXTENSION) != 'zip' && pathinfo($upload['file'], PATHINFO_EXTENSION) != 'rar') {
							$boxview = new mdocs_box_view();
							$upload['type'] = pathinfo($upload['file'], PATHINFO_EXTENSION);
							$boxview_file = $boxview->uploadFile($upload);
							$boxview_file = $boxview_file['entries'][0];
						} else $boxview_file['id'] = 0;
					} else $boxview_file['id'] = 0;
					$date = mdocs_format_unix_epoch();
					array_push($mdocs, array(
						'id'=>(string)$upload['attachment_id'],
						'parent'=>(string)$upload['parent_id'],
						'filename'=>$upload['filename'],
						'name'=>$_REQUEST['mdocs']['name'][$index],
						'desc'=>'',
						'type'=>$mdocs_fle_type,
						'cat'=>$_REQUEST['mdocs']['cat'][$index],
						'owner'=>$current_user->user_login,
						'contributors'=>array(),
						'size'=>(string)$file['size'],
						'modified'=>$date['gmdate'],
						'version'=>(string)$_REQUEST['mdocs']['version'][$index],
						'show_social'=>(string)'on',
						'non_members'=> (string)'on',
						'file_status'=>(string)'public',
						'post_status'=> (string)'publish',
						'post_status_sys'=> (string)'publish',
						'doc_preview'=>(string)'',
						'downloads'=>(string)0,
						'archived'=>array(),
						'ratings'=>array(),
						'rating'=>0,
						'box-view-id' => $boxview_file['id'],
					));
					$mdocs = mdocs_array_sort($mdocs);
					mdocs_save_list($mdocs);
					$batch_log .= __('Mime Type Allowed => ','memphis-documents-library').$result['type']."<br>";
					$batch_log .= '<span class="text-success">'. __('File Uploaded with No Errors.','memphis-documents-library')."</span><br><br>";
				} else {
					$batch_log .= '<span class="text-danger">'.__('File Was Not Uploaded because an Error occured.','memphis-documents-library')."</span><br><br>";
				}
			} else {
				$batch_log .= __("Invalid Mime Type => ").$result['type'].__(" Unable to process file.")."<br>";
				$batch_log .='<span class="text-danger">'. __('File Was Not Uploaded because an Error occured.','memphis-documents-library')."</span><br><br>";
			} 
			$file = array();
		}
	}
	$batch_log .= __("Cleaning up tmp folder and files")."<br><br>";
	
	mdocs_rmdir(sys_get_temp_dir().'/mdocs');
	mdocs_rmdir(sys_get_temp_dir().'/mdocs-import');
	$batch_log .= __("Batch Process Complete.");
	?>
	
			<h3 class="panel-title"><?php _e('Batch Upload Complete','memphis-documents-library'); ?></h3>
		
		
			<p><?php _e('The batch process has completed, below is a log of results:','memphis-documents-library'); ?></p>
			<p><?php echo $batch_log; ?></p>
		
	
			<form class="mdocs-uploader-form" enctype="multipart/form-data" action="<?php echo get_site_url().'/wp-admin/admin.php?page='.$post_page.'&mdocs-cat='.$post_cat; ?>" method="POST">
				<label><?php _e('Default Folder','memphis-documents-library'); ?>:</label>
				<select name="mdocs[cat]">
						<?php mdocs_display_folder_options_menu($cats, $current_cat); ?>
				</select>
				<input type="file" name="mdocs-batch" /><br><br>
				<input type="submit" class="btn btn-primary" value="<?php _e('Upload Zip File','memphis-documents-library') ?>" />
				<input type="submit" class="btn btn-danger" value="<?php _e('Process FTP Folder','memphis-documents-library') ?>" /><br/>
			</form>
		</div>
	</div>
	<?php
}
}
?>