<?php
function mdocs_import() {
	$upload_dir = wp_upload_dir();
	mdocs_list_header();
?>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('Import Files','memphis-documents-library'); ?></h3>
	</div>
	<div class="panel-body">
		<p><?php _e('There are two type of imports you can choose from.','memphis-documents-library'); ?></p>
		<p>
			<ol>
				<!--
				<li><em><?php _e('Keep Existing Files','memphis-documents-library'); ?></em>
					<blockquote><?php _e('Is the safest way to import.  This option keeps all your current files and only imports new ones. 
					<br>If a file that is being imported matches one on the current system, the one on the current system will be left untouched,
					<br>and you will have to manually import these files.','memphis-documents-library'); ?></blockquote>
				</li>
				-->
				<li><em><?php _e('Delete Old Files','memphis-documents-library'); ?></em>
					<blockquote><?php _e('Is a good when you have a empty documents library or you at looking to refresh your current library.'); ?>   
					<br><?php _e('This method deletes all files, posted and version on the current system. After the method has completed you will
					<br>get a list of all the conflicts that have occured make note of them.','memphis-documents-library'); ?>
					<i><?php _e('Please take great care in using this method as there is little to no return.','memphis-documents-library'); ?></i></blockquote>
				</li>
			</ol>
		</p>
		<form  id="mdocs-import" method="post" action="admin.php?page=memphis-documents.php&mdocs-cat=import" enctype="multipart/form-data">
			<h3><?php _e('Add the Zip File','memphis-documents-library'); ?>:</h3>
			<p><em><i><?php _e('Remember to always export any valuable data before doing an import.','memphis-documents-library'); ?></i></em></p>
			<!--<input type="hidden" value="memphis-documents.php" id="page" name="page"/>-->
			<input type="hidden" value="mdocs-import" id="action" name="action"/>
			<input type="radio" value="keep" name="radio1" disabled> <?php _e('Keep Existing Files','memphis-documents-library'); ?> 
			<input type="radio" value="overwrite" name="radio1" checked> <?php _e('Delete Old Files','memphis-documents-library'); ?><br><br>
			<input type="file" name="mdocs-import-file" id="mdocs-import-file"/><br>
			<input type="submit" class="btn btn-primary" id="mdocs-import-submit" value="<?php _e('Import Memphis Documents Library','memphis-documents-library') ?>" />
		</form>
	</div>
</div>
<?php
}

function mdocs_import_zip() {
	if(get_option('mdocs-convert-to-latin')) $_FILES['mdocs-import-file']['name'] = mdocs_filenames_to_latin($_FILES['mdocs-import-file']['name']);
	if($_FILES['mdocs-import-file']['name'] != '' ) {
		$error = false;
		$upload_dir = wp_upload_dir();
		$mdocs = get_option('mdocs-list');
		$mdocs_zip_file = sys_get_temp_dir().'/'.$_FILES['mdocs-import-file']['name'];
		//Backup Current Memphis Documents
		if(!file_exists($upload_dir['basedir'].'/mdocs-backup/')) mkdir($upload_dir['basedir'].'/mdocs-backup/');
		$files = glob($upload_dir['basedir'].'/mdocs/*'); 
		foreach($files as $file){ 
		  if(is_file($file))
			$explode = explode('/',$file);
			$filename = $explode[count($explode)-1];
			@rename($file, $upload_dir['basedir'].'/mdocs-backup/'.$filename);
		}
		if(file_exists($mdocs_zip_file)) unlink($mdocs_zip_file);
		move_uploaded_file($_FILES['mdocs-import-file']['tmp_name'], $mdocs_zip_file);
		$zip_result = mdocs_unzip($mdocs_zip_file, sys_get_temp_dir());
		if(is_array($zip_result)) {
			$zip_dir = basename($zip_result['dir']);
			$mdocs_cats_file = false;
			$mdocs_list_file = false;
			if(file_exists($zip_result['dir'].'/mdocs-list.txt')) {
				$mdocs_list_file = unserialize(file_get_contents($zip_result['dir'].'/mdocs-list.txt'));
			} else $error = true;
			if(file_exists($zip_result['dir'].'/mdocs-cats.txt')) {
				$mdocs_cats_file = unserialize(file_get_contents($zip_result['dir'].'/mdocs-cats.txt'));
				if(!is_array($mdocs_cats_file[0])) {
					$new_mdocs_cats = array();
					foreach($mdocs_cats_file as $index => $cat) {
						array_push($new_mdocs_cats, array('slug' => $index,'name' => $cat, 'parent' => '', 'children' => array(), 'depth' => 0));
					}
					$mdocs_cats_file = $new_mdocs_cats;
					mdocs_errors(__('Old folder structure found, updated to the new folder structure.  It is recommened that you re-export you files again.  The process did finish.','memphis-documents-library'), 'error');
				}
			} else $error = true;
			if($mdocs_cats_file === false || $mdocs_list_file === false || $error ) {
				mdocs_rmdir(sys_get_temp_dir().'/mdocs-import');
				mdocs_rmdir($upload_dir['basedir'].'/mdocs-backup/');
				mdocs_errors('There was an error processing your saved variables file.  Sorry the import process can not continue.','error');
			} else {
				update_option('mdocs-zip',$_FILES['mdocs-import-file']['name']);
				$mdocs_list_conflicts = array();
				$mdocs_cats_conflicts = array();
				if($_POST['radio1'] == 'overwrite') {
					mdocs_remove_posts_and_attachments();
					mdocs_save_list($mdocs_list_file);
					update_option('mdocs-cats', $mdocs_cats_file, '' , 'no');
					$cats = get_option('mdocs-cats');
					$mdocs = array();
				}
				/* DISABLED NEED MORE WORK NOT RELIABLE 
				else {
					$mdocs_cats = get_option('mdocs-cats');
					$modocs_list_return = array();
					foreach($mdocs as $key => $value) {
						$found = false;
						foreach($mdocs_list_file as $k => $v) {
							if($mdocs_list_file[$k]['filename'] == $mdocs[$key]['filename']) {
								$explode = explode('.',$v['filename']);
								$ext = $explode[count($explode)-1];
								$_150x150 = substr_replace($v['filename'],'-150x150',-4);
								$name = $_150x150.'.'.$ext;
								array_push($mdocs_list_conflicts, $mdocs_list_file[$k]['filename']);
								$found = true;
								unset($mdocs_list_file[$k]);
								$thumbnails = glob($upload_dir['basedir'].'/mdocs-backup/'.$name.'-150x55*');
								$versions = glob($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'].'-v*');
								foreach($thumbnails as $t) copy($t, str_replace('mdocs-backup','mdocs',$t));
								foreach($versions as $v) copy($v, str_replace('mdocs-backup','mdocs',$v));
								@copy($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'], $upload_dir['basedir'].'/mdocs/'.$value['filename']);
								break;
							}
						}
						if($found == false) {
							$explode = explode('.',$value['filename']);
							array_pop($explode);
							$name = implode('',$explode);
							$thumbnails = glob($upload_dir['basedir'].'/mdocs-backup/'.$name.'-150x55*');
							$versions = glob($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'].'-v*');
							foreach($thumbnails as $t) copy($t, str_replace('mdocs-backup','mdocs',$t));
							foreach($versions as $v) copy($v, str_replace('mdocs-backup','mdocs',$v));
							copy($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'], $upload_dir['basedir'].'/mdocs/'.$value['filename']);
						}
					}
					foreach($mdocs_cats_file as $key => $value) {
						$found = false;
						if(!empty($cats)) {
							foreach($mdocs_cats as $k => $v) {
								if($key == $k) {
									array_push($mdocs_cats_conflicts, $mdocs_cats[$k]);
									$found = true;
									break;
								}
							}
						}
						if($found == false) $mdocs_cats[$key] = $value;
					}
					
					update_option('mdocs-cats',$mdocs_cats, '' , 'no');
				}
				*/
				foreach($mdocs_list_file as $key => $value) {
					$mimes = get_allowed_mime_types();
					$valid_mime_type = false;
					foreach ($mimes as $type => $mime) {
						$file_type = wp_check_filetype($value['filename']);
						if($file_type['type'] != false) {
							$found_ext = strpos($type,strtolower($file_type['ext']));
							if($found_ext !== false) {
								$valid_mime_type = true;
								break;
							}
						} else $valid_mime_type = false;
					}
					if($valid_mime_type) {
						$file_not_found = false;
						if(file_exists($zip_result['dir'].'/'.$value['filename'])) {
							$upload_result = move_uploaded_file($zip_result['dir'].'/'.$value['filename'], $upload_dir['basedir'].'/mdocs/'.strtolower($value['filename']));
							if($upload_result == false) $upload_result = @rename($zip_result['dir'].'/'.$value['filename'], $upload_dir['basedir'].'/mdocs/'.strtolower($value['filename']));
							//if($upload_result == false) var_dump($value['filename']);
						} else $file_not_found = true;
						
						
						
						if($file_not_found == false) {
							if(is_array($value['archived']) && count($value['archived']) > 0){
								foreach($value['archived'] as $archive) {
									if(file_exists($zip_result['dir'].'/'.$archive)) {
										$upload_result = move_uploaded_file($zip_result['dir'].'/'.$archive, $upload_dir['basedir'].'/mdocs/'.strtolower($archive));
										if($upload_result == false) $upload_result = @rename($zip_result['dir'].'/'.$archive, $upload_dir['basedir'].'/mdocs/'.strtolower($archive));
										//if($upload_result == false) var_dump($archive);
									}
								}
							}
						
						
						
							$hide_all_posts = get_option('mdocs-hide-all-posts');
							$hide_all_posts_non_members = get_option('mdocs-hide-all-posts-non-members');
							if($mdocs_list_file[$key]['post_status'] == '' ) $the_post_stauts = 'publish';
							else $the_post_stauts = $mdocs_list_file[$key]['post_status'];
							if($mdocs_list_file[$key]['post_status_sys'] == '') $the_post_stauts_sys = 'publish';
							else $the_post_stauts_sys = $mdocs_list_file[$key]['post_status_sys'];
							$file = array(
								'type'=>'null',
								'tmp_name'=>'null',
								'error'=> 0,
								'size' => 0,
								'filename'=>strtolower($value['filename']),
								'name'=>$mdocs_list_file[$key]['name'],
								'desc'=>$mdocs_list_file[$key]['desc'],
								'post-status'=>$the_post_stauts,
								'modifed'=>floatval($mdocs_list_file[$key]['modified']));
							$upload = mdocs_process_file($file, true);
							if(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '') {
								$is_image = @getimagesize($upload['file']);
								if($is_image == false && pathinfo($upload['file'], PATHINFO_EXTENSION) != 'zip' && pathinfo($upload['file'], PATHINFO_EXTENSION) != 'rar') {
									$boxview = new mdocs_box_view();
									$upload['type'] = pathinfo($upload['file'], PATHINFO_EXTENSION);
									$boxview_file = $boxview->uploadFile($upload);
									$boxview_file = $boxview_file['entries'][0];
								} else $boxview_file['id'] = 0;
							} else $boxview_file['id'] = 0;
							@$owner = get_user_by('login', $mdocs_list_file[$key]['owner'])->user_login;
							if($owner == '') {
								$current_user = wp_get_current_user();
								$owner = $current_user->user_login;
							}
							if($mdocs_list_file[$key]['contributors'] == null) $mdocs_list_file[$key]['contributors'] = array();
							if(!isset($mdocs_list_file[$key]['author'])) $mdocs_list_file[$key]['author'] = '';
							array_push($mdocs, array(
								'id'=>(string)$upload['attachment_id'],
								'parent'=>(string)$upload['parent_id'],
								'filename'=>(string)$upload['filename'],
								'name'=>(string)$upload['name'],
								'desc'=>(string)$upload['desc'],
								'type'=>$mdocs_list_file[$key]['type'],
								'cat'=>$mdocs_list_file[$key]['cat'],
								'owner'=>$owner,
								'contributors'=>$mdocs_list_file[$key]['contributors'],
								'author'=>$mdocs_list_file[$key]['author'],
								'size'=>(string)$mdocs_list_file[$key]['size'],
								'modified'=>floatval($mdocs_list_file[$key]['modified']),
								'version'=>(string)$mdocs_list_file[$key]['version'],
								'downloads'=>(string)$mdocs_list_file[$key]['downloads'],
								'archived'=>$mdocs_list_file[$key]['archived'],
								'show_social'=>$mdocs_list_file[$key]['show_social'],
								'non_members'=>$mdocs_list_file[$key]['non_members'],
								'file_status'=>$mdocs_list_file[$key]['file_status'],
								'post_status'=>$the_post_stauts,
								'post_status_sys'=>$the_post_stauts_sys,
								'ratings'=>$mdocs_list_file[$key]['ratings'],
								'rating'=>$mdocs_list_file[$key]['rating'],
								'doc_preview'=>$mdocs_list_file[$key]['doc_preview'],
								'box-view-id' => $boxview_file['id'],
							));
							mdocs_save_list($mdocs);
							} else {
								array_push($mdocs_list_conflicts,$value['filename'].': '.__('File extension not allowed', 'memphis-documents-library'));
								mdocs_save_list($mdocs);
							} 
						} 
					}
					
				mdocs_rmdir($upload_dir['basedir'].'/mdocs-backup');
				mdocs_rmdir(sys_get_temp_dir().'/mdocs-import');
				unlink($mdocs_zip_file);
				
				if(count($mdocs_list_conflicts) > 0) mdocs_errors('The following files where not added to the Documents Library. You will have to upload these files manually:<ul><li><em>' .implode('</li><li>',$mdocs_list_conflicts).'</em></li></ul>', 'error');
				if(count($mdocs_cats_conflicts) > 0) mdocs_errors('The following categories where not added to the Documents Library. You will have to add these categories manually:<ul><li><em>' .implode('</li><li>',$mdocs_cats_conflicts).'</em></li></ul>', 'error');
			
			}
		} else {
			mdocs_rmdir($upload_dir['basedir'].'/mdocs-backup');
		}
	} else mdocs_errors('The file you are trying to upload is not the correct file.  Please try again.','error');
}

function mdocs_unzip($zip_file, $output_path) {
	$zip = new ZipArchive;
	$zip_result = false;
	$open_results = $zip->open($zip_file);
	if ($zip->open($zip_file) === TRUE) {
		$zip->extractTo(sys_get_temp_dir().'/mdocs-import');
		foreach(scandir(sys_get_temp_dir().'/mdocs-import') as $index => $find_folder) {
			if($find_folder !== '.' && $find_folder != '..' && $find_folder != '__MACOSX' && $find_folder != '.DS_Store') {
				if(is_dir(sys_get_temp_dir().'/mdocs-import/'.$find_folder)) {
					$zip_result['dir'] =  sys_get_temp_dir().'/mdocs-import/'.$find_folder;
				} else $zip_result['dir'] = sys_get_temp_dir().'/mdocs-import';
			}
		}
		foreach(scandir($zip_result['dir']) as $index => $find_files) {
			if($find_files !== '.' && $find_files != '..' && $find_files != '__MACOSX' && $find_files != '.DS_Store' && $find_files != '.htaccess' && $find_files != is_dir($zip_result['dir'].'/'.$find_files)) {
				if(get_option('mdocs-convert-to-latin')) {
					if(!isset($zip_result['file'])) $zip_result['file'][0] = $zip_result['dir'].'/'.mdocs_filenames_to_latin($find_files);
					else array_push($zip_result['file'], $zip_result['dir'].'/'.mdocs_filenames_to_latin($find_files));
					rename($zip_result['dir'].'/'.$find_files, $zip_result['dir'].'/'.mdocs_filenames_to_latin($find_files));
				} else {
					if(!isset($zip_result['file'])) $zip_result['file'][0] = $zip_result['dir'].'/'.$find_files;
					else array_push($zip_result['file'], $zip_result['dir'].'/'.$find_files);
					rename($zip_result['dir'].'/'.$find_files, $zip_result['dir'].'/'.$find_files);
				}
				}
			
		}
		return $zip_result;
	} else {
		return $open_results;
	}
}
?>
