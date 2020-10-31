<?php
function mdocs_file_upload() {
	if (!isset( $_POST['mdocs-upload-file-field'] ) || !wp_verify_nonce( $_POST['mdocs-upload-file-field'], 'mdocs-upload-file-action' )) {
		print 'Sorry, your nonce did not verify.';
		exit;
	} else {
		if(current_user_can('mdocs_allow_upload') || current_user_can('mdocs_allow_upload_frontend')) {
			$the_error_msg = '';
			$current_user = wp_get_current_user();
			$mdocs = mdocs_array_sort();
			$mdocs_cats = get_option('mdocs-cats');
			foreach($mdocs as $index => $doc) {
				if($_POST['mdocs-index'] == $doc['id']) {
					$mdocs_index = $index; break;
				}
			}
			$_POST = mdocs_sanitize_array($_POST);
			$_FILES['mdocs'] = mdocs_sanitize_array($_FILES['mdocs']);
			if(get_option('mdocs-convert-to-latin')) $_FILES['mdocs']['name'] = mdocs_filenames_to_latin(stripcslashes($_FILES['mdocs']['name']));
			$file_info = pathinfo($_FILES['mdocs']['name']);
			$mdocs_name = $_POST['mdocs-name'];
			if($_FILES['mdocs']['name'] != '')	$mdocs_fle_type = strtolower($file_info['extension']);
			else $mdocs_fle_type = '';
			$mdocs_fle_size = $_FILES["mdocs"]["size"];
			$mdocs_type = $_POST['mdocs-type'];
			
	// NEW PATT START		
			// $mdocs_cat = $_POST['mdocs-cat'];
			$data_array = apply_filters( 'wppatt_mld_get_patt_mdocs_cat', $_POST ); // Hook: /scripts/mld_patt_hooks
			
			$mdocs_cat = $data_array['mdocs_cat'];
			$folderdocinfofile_id = $data_array['folderdocinfofile_id'];
	// NEW PATT END

			$mdocs_desc = wpautop($_POST['mdocs-desc']);
			$mdocs_version = $_POST['mdocs-version'];
			if(isset($_POST['mdocs-social'])) $mdocs_social = $_POST['mdocs-social'];
			else $mdocs_social = false;
			$mdocs_non_members = @$_POST['mdocs-non-members'];
			$mdocs_file_status = $_POST['mdocs-file-status'];
			if($mdocs_file_status == '') $mdocs_file_status = 'public';
			$mdocs_doc_preview = @$_POST['mdocs-doc-preview'];
			if(!isset($_POST['mdocs-contributors']))  $_POST['mdocs-contributors'] = array();
			else $_POST['mdocs-contributors'] = array_values($_POST['mdocs-contributors']);
			if(isset($_POST['mdocs-post-status']) && $_POST['mdocs-post-status'] != '') $mdocs_post_status = $_POST['mdocs-post-status'];
			else $mdocs_post_status = 'publish';
			if(!isset($_POST['mdocs-real-author'])) $_POST['mdocs-real-author'] = '';
			$upload_dir = wp_upload_dir();	
			$mdocs_user = $current_user->user_login;
			if($mdocs_file_status == 'hidden') $mdocs_post_status_sys = 'draft';
			else $mdocs_post_status_sys = $mdocs_post_status;
			$the_post_status = $mdocs_post_status_sys;
			$_FILES['mdocs']['post_status'] = $the_post_status;
			
			//MDOCS FILE TYPE VERIFICATION	
			$mimes = get_allowed_mime_types();
			$valid_mime_type = false;
			foreach ($mimes as $type => $mime) {
				$file_type = wp_check_filetype($_FILES['mdocs']['name']);
				$found_ext = stripos($type,$file_type['ext']);
				if($found_ext !== false) {
					$valid_mime_type = true;
					break;
				}
			}
			if($_FILES['mdocs']['size'] < mdocs_file_upload_max_size()) { 
				if(!empty($mdocs_cats)) {
					if($mdocs_type == 'mdocs-add') {
						if($valid_mime_type) {
							$_FILES['mdocs']['post-status'] = $mdocs_post_status;
							$upload = mdocs_process_file($_FILES['mdocs'], false);
							if($mdocs_version == '') $mdocs_version = '1.0';
							if(!isset($upload['error'])) {
								if(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '') {
									$is_image = @getimagesize($upload['file']);
									if($is_image == false && pathinfo($upload['file'], PATHINFO_EXTENSION) != 'zip' && pathinfo($upload['file'], PATHINFO_EXTENSION) != 'rar') {
										$boxview = new mdocs_box_view();
										$upload['type'] = pathinfo($upload['file'], PATHINFO_EXTENSION);;
										$boxview_file = $boxview->uploadFile($upload);
										$boxview_file = $boxview_file['entries'][0];
									} else $boxview_file['id'] = 0;
								} else $boxview_file['id'] = 0;
								array_push($mdocs, array(
									'id'=>(string)$upload['attachment_id'],
									'parent'=>(string)$upload['parent_id'],
									'filename'=>$upload['filename'],
									'name'=>$upload['name'],
									'desc'=>$upload['desc'],
									'type'=>$mdocs_fle_type,
									'cat'=>$mdocs_cat,
									'owner'=>$mdocs_user,
									'contributors'=>$_POST['mdocs-contributors'],
									'author'=>$_POST['mdocs-real-author'],
									'size'=>intval($mdocs_fle_size),
									'modified'=>$upload['modified'],
									'version'=>(string)$mdocs_version,
									'show_social'=>(string)$mdocs_social,
									'non_members'=> (string)$mdocs_non_members,
									'file_status'=>(string)$mdocs_file_status,
									'post_status'=> (string)$mdocs_post_status,
									'post_status_sys'=> (string)$mdocs_post_status_sys,
									'doc_preview'=>(string)$mdocs_doc_preview,
									'downloads'=>intval(0),
									'archived'=>array(),
									'ratings'=>array(),
									'rating'=>intval(0),
									'box-view-id' => $boxview_file['id'],
								));
								$mdocs = mdocs_array_sort($mdocs);
								mdocs_save_list($mdocs);

// NEW PATT START

do_action( 'wppatt_mld_save_attachment_info_db', $upload, $_POST, $folderdocinfofile_id );

// NEW PATT END

							} else $the_error_msg = $upload['error'];
						} else $the_error_msg = MDOCS_ERROR_2;
					} elseif($mdocs_type == 'mdocs-update') {
						if($_FILES['mdocs']['name'] != '') {
							if($valid_mime_type) {
								$old_doc = $mdocs[$mdocs_index];
								$old_doc_name = $old_doc['filename'].'-v'.preg_replace('/ /', '',$old_doc['version']);
								@rename($upload_dir['basedir'].'/mdocs/'.$old_doc['filename'],$upload_dir['basedir'].'/mdocs/'.$old_doc_name);
								$name = substr($old_doc['filename'], 0, strrpos($old_doc['filename'], '.') );
								$filename = $file_info['basename']; 	// old value $name.'.'.$mdocs_fle_type;
								$_FILES['mdocs']['name'] = $filename;
								$_FILES['mdocs']['parent'] = $old_doc['parent'];
								$_FILES['mdocs']['id'] = $old_doc['id'];
								$_FILES['mdocs']['post-status'] = $mdocs_post_status;
								$upload = mdocs_process_file($_FILES['mdocs']);
								if(!isset($upload['error'])) {
									if(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '') {
										$is_image = @getimagesize($upload['file']);
										if($is_image == false && pathinfo($upload['file'], PATHINFO_EXTENSION) != 'zip' && pathinfo($upload['file'], PATHINFO_EXTENSION) != 'rar') {
											$boxview = new mdocs_box_view();
											$upload['type'] = pathinfo($upload['file'], PATHINFO_EXTENSION);;
											$boxview_file = $boxview->uploadFile($upload);
											$boxview_file = $boxview_file['entries'][0];
											$the_mdoc = get_the_mdoc_by($old_doc['id'], 'id');
											$boxview->deleteFile($the_mdoc);
										} else $boxview_file['id'] = 0;
									} else $boxview_file['id'] = 0;
									if($mdocs_version == '') $mdocs_version = '1.0';
									elseif($mdocs_version == $mdocs[$mdocs_index]['version']) $mdocs_version = mdocs_increase_minor_version($mdocs[$mdocs_index]['version']);
									$mdocs[$mdocs_index]['filename'] = $upload['filename'];
									$mdocs[$mdocs_index]['name'] = $upload['name'];
									$mdocs[$mdocs_index]['desc'] = $upload['desc'];
									$mdocs[$mdocs_index]['version'] = (string)$mdocs_version;
									$mdocs[$mdocs_index]['type'] = (string)$mdocs_fle_type;
									$mdocs[$mdocs_index]['cat'] = $mdocs_cat;
									$mdocs[$mdocs_index]['owner'] = $mdocs[$mdocs_index]['owner'];
									$mdocs[$mdocs_index]['contributors'] = $_POST['mdocs-contributors'];
									$mdocs[$mdocs_index]['author'] = $_POST['mdocs-real-author'];
									$mdocs[$mdocs_index]['size'] = intval($mdocs_fle_size);
									$mdocs[$mdocs_index]['modified'] = $upload['modified'];
									$mdocs[$mdocs_index]['show_social'] =(string)$mdocs_social;
									$mdocs[$mdocs_index]['non_members'] =(string)$mdocs_non_members;
									$mdocs[$mdocs_index]['file_status'] =(string)$mdocs_file_status;
									$mdocs[$mdocs_index]['post_status'] =(string)$mdocs_post_status;
									$mdocs[$mdocs_index]['post_status_sys'] =(string)$mdocs_post_status_sys;
									$mdocs[$mdocs_index]['doc_preview'] =(string)$mdocs_doc_preview;
									$mdocs[$mdocs_index]['box-view-id'] = $boxview_file['id'];
									array_push($mdocs[$mdocs_index]['archived'], $old_doc_name);
									$mdocs = mdocs_array_sort($mdocs);
									mdocs_save_list($mdocs);
									
					//  do_action( 'wppatt_mld_update_attachment_info_db', $mdocs, $_POST, $_FILES  );
									
								} else $the_error_msg = $upload['error'];
							} else $the_error_msg =MDOCS_ERROR_2;
						} else {
							$desc = $mdocs_desc;
							if($mdocs_name == '') $mdocs[$mdocs_index]['name'] = $_POST['mdocs-pname'];
							else $mdocs[$mdocs_index]['name'] = $mdocs_name;
							if($mdocs_version == '') $mdocs_version = $mdocs[$mdocs_index]['version'];
							$mdocs[$mdocs_index]['desc'] = $desc;
							$mdocs[$mdocs_index]['version'] = (string)$mdocs_version;
							$mdocs[$mdocs_index]['cat'] = $mdocs_cat;
							$mdocs[$mdocs_index]['owner'] = $mdocs[$mdocs_index]['owner'];
							$mdocs[$mdocs_index]['contributors'] = $_POST['mdocs-contributors'];
							$mdocs[$mdocs_index]['author'] = $_POST['mdocs-real-author'];
				
							$date = mdocs_format_date($_POST['mdocs-last-modified']);
							if($mdocs[$mdocs_index]['modified'] != $date['gmdate']) $mdocs[$mdocs_index]['modified'] = floatval($date['gmdate']);
							
							$mdocs[$mdocs_index]['show_social'] =(string)$mdocs_social;
							$mdocs[$mdocs_index]['non_members'] =(string)$mdocs_non_members;
							$mdocs[$mdocs_index]['file_status'] =(string)$mdocs_file_status;
							$mdocs[$mdocs_index]['post_status'] =(string)$mdocs_post_status;
							$mdocs[$mdocs_index]['post_status_sys'] =(string)$mdocs_post_status_sys;
							$mdocs[$mdocs_index]['doc_preview'] =(string)$mdocs_doc_preview;
							//if($upload['modified'] > time() && $mdocs_post_status === 'publish') $post_status = 'future';
							//else $post_status = (string)$mdocs_post_status;
							$post_status = (string)$mdocs_post_status;
							$mdocs_post = array(
								'ID' => $mdocs[$mdocs_index]['parent'],
								'post_title' => $mdocs[$mdocs_index]['name'],
								'post_status' => $post_status,
								'post_date' => $date['wp-date'],
								'post_date_gmt' => $date['wp-gmdate'],
							);
							$mdocs_post_id = wp_update_post( $mdocs_post );
							if(isset($_POST['mdocs-categories'])) {
								$category_as_id = array();
								if(is_array($_POST['mdocs-categories'])) {
									foreach($_POST['mdocs-categories'] as $category) array_push($category_as_id, get_cat_ID($category));
									wp_set_post_categories( $mdocs_post_id, $category_as_id );
								}
							}

							wp_set_post_tags($mdocs_post_id, $_POST['mdocs-tags']);

							$mdocs_attachment = array(
								'ID' => $mdocs[$mdocs_index]['id'],
								'post_title' => $mdocs_name
							);
							wp_update_post( $mdocs_attachment );
							$attachment = array(
								'ID' => $mdocs[$mdocs_index]['id'],
								'post_date' => $date['wp-date'],
								'post_date_gmt' => $date['wp-gmdate'],
							);

							$mdocs_attach_id = wp_update_post( $attachment );
							$mdocs = mdocs_array_sort($mdocs);
							mdocs_save_list($mdocs);
						}
// PATT START
						do_action( 'wppatt_mld_update_attachment_info_db', $mdocs, $_POST, $_FILES  );
// PATT END						
					}
				} else $the_error_msg = MDOCS_ERROR_3;
			} else $the_error_msg = __('The file you are trying to upload is bigger than your php.ini files upload_max_filesize.  You will have to increase that value enable to upload this file.','memphis-documents-library');
			// STOPS FORM RESUBMIT
			if(is_admin()) {
// PATT START	
			//	if($the_error_msg != '') @header('location: '.$_REQUEST['mdocs-permalink'].$_REQUEST['mdocs-cat'].'&mdocs-error-msg='.$the_error_msg);	
			//	else @header('location: '.$_REQUEST['mdocs-permalink'].$_REQUEST['mdocs-cat']);
								
				if($the_error_msg != '') @header('location: '.$_REQUEST['mdocs-permalink'].'&mdocs-error-msg='.$the_error_msg);
				else @header('location: '.$_REQUEST['mdocs-permalink']);
// PATT END				
			} else {
				if($the_error_msg != '') {
					mdocs_errors($the_error_msg, 'error',true);
				} else @header('location: '.get_permalink());
			}
		}
	}
}

function mdocs_upload_button($atts) {
	if(current_user_can('mdocs_allow_upload_frontend')) {
		ob_start();
		if(isset($atts['align'])) {
			if($atts['align'] == 'center') $align = 'mdocs-float-center';
			elseif($atts['align'] == 'right') $align = 'mdocs-float-right';
			elseif($atts['align'] == 'center') $align = 'mdocs-float-left';
			else $align = 'mdocs-float-left';
		} else $align = 'mdocs-float-left';
		//load_add_update_modal($atts);
		if(isset($_FILES['mdocs']) && $_FILES['mdocs']['name'] != '' && $_POST['mdocs-type'] == 'mdocs-add') mdocs_file_upload();
		elseif(isset($_FILES['mdocs']) && $_POST['mdocs-type'] == 'mdocs-update') mdocs_file_upload();
		?>
		<div class="<?php echo $align; ?>">
			<button id="mdocs-upload-frontend" data-action-type="add-doc" data-toggle="mdocs-modal" data-target="#mdocs-add-update" class="mdocs-download-btn btn btn-primary" ><?php echo __('Upload File','memphis-documents-library'); ?></button>
		</div>	
		<div style="clear: both;"></div>
		<?php
		$the_button = ob_get_clean();
		return $the_button;
	}
}

function mdocs_check_uploader_options($atts, $type, $func_show, $func_hide) {
	//var_dump($type);
	$cats = get_option('mdocs-cats');
	$type_formatted = preg_replace('/ /', '_', $type);
	$type_options = preg_replace('/ /', '-', $type);
	if(mdocs_convert_to_boolean(get_option('mdocs-show-upload-'.$type_options)) == false && is_admin()) {
		$default_value = mdocs_check_upload_defaults($type_formatted, $type_options, $atts);
		if(function_exists($func_hide)) call_user_func($func_hide, $default_value);
		else echo 'Please add a hide-function to your form-data.';
	} elseif(!isset($atts[$type_formatted.'_edit']) && !isset($atts[$type_formatted.'_hide']) && !isset($atts['hide_all']) && !isset($atts['edit_all']) || isset($atts[$type_formatted.'_edit']) && mdocs_convert_to_boolean($atts[$type_formatted.'_edit']) || isset($atts['hide_all']) && mdocs_convert_to_boolean($atts['hide_all']) == false || isset($atts[$type_formatted.'_hide']) && mdocs_convert_to_boolean($atts[$type_formatted.'_hide']) == false || isset($atts['edit_all']) && mdocs_convert_to_boolean($atts['edit_all'] || mdocs_convert_to_boolean(get_option('mdocs-show-upload-'.$type_options)) && is_admin()) ) {
		$default_value = mdocs_check_upload_defaults($type_formatted, $type_options, $atts);
		?>
		<div class="form-group form-group-lg">
			<label class="col-sm-2 control-label" for="mdocs-<?php echo $type; ?>"><?php echo ucwords($type); ?></label>
			<div class="col-sm-10">
				<?php
				if(isset($atts[$type_formatted.'_edit']) && $atts[$type_formatted.'_edit'] == false) {
					call_user_func($func_hide, $default_value);
					call_user_func($func_show,$cats, $default_value, 'display');
				} else call_user_func($func_show,$cats, $default_value);
				?>
			</div>
		</div>
		<?php
	} else {
		$default_value = mdocs_check_upload_defaults($type_formatted, $type_options, $atts);
		if(isset($atts[$type_formatted.'_hide']) && mdocs_convert_to_boolean($atts[$type_formatted.'_hide']) == false || isset($atts['hide_all']) && mdocs_convert_to_boolean($atts['hide_all']) == false || isset($atts[$type_formatted.'_edit']) && mdocs_convert_to_boolean($atts[$type_formatted.'_edit']) == false|| isset($atts['edit_all']) && mdocs_convert_to_boolean($atts['edit_all']) == false) { ?>
			<div class="form-group form-group-lg">
				<label class="col-sm-2 control-label" for="mdocs-<?php echo $type; ?>"><?php echo ucwords($type); ?></label>
				<div class="col-sm-10">
					<?php
					call_user_func($func_hide, $default_value);
					call_user_func($func_show,$cats, $default_value, 'display');
					?>
				</div>
			</div>
		<?php } else {
			call_user_func($func_hide, $default_value);
		}
	}
}
function mdocs_check_upload_defaults($type_formatted, $type_options, $atts) {
	$cats = get_option('mdocs-cats');
	if($type_formatted == 'folder') {
		if(isset($atts[$type_formatted])) $the_data = mdocs_get_the_folder($atts[$type_formatted]);
		else $the_data = null;
		if($the_data == null) $default_value = $cats[0]['slug'];
		else $default_value = $the_data['slug'];
	} elseif($type_formatted == 'version') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = '';
		return $default_value;
	} elseif($type_formatted == 'date') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = '';
	} elseif($type_formatted == 'file_status') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = '';
	} elseif($type_formatted == 'post_status') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = '';
	} elseif($type_formatted == 'show_social_apps') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = 'checked';
	} elseif($type_formatted == 'downloadable_by_non_members') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = 'checked';
	} elseif($type_formatted == 'contributors') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = '';
	} elseif($type_formatted == 'author') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = '';
	} elseif($type_formatted == 'real_author') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = '';
	} elseif($type_formatted == 'tags') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = '';
	} elseif($type_formatted == 'categories') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = '';
	} elseif($type_formatted == 'description') {
		if(isset($atts[$type_formatted])) $default_value = $atts[$type_formatted];
		else $default_value = '';
	} else $default_value = '';
	
	return $default_value;
}
// NAME
function mdocs_upload_display_name($cats, $default_value, $display='') {
	?>
	<input class="form-control" type="text" name="mdocs-name" id="mdocs-name" />
	<?php
}
function mdocs_upload_hide_name($default_value) {
	?>
	<input type=hidden name="mdocs-cat" value="<?php echo $default_value; ?>" >
	<?php
}
// FOLDER
function mdocs_upload_display_folder($cats, $default_value, $display='') {
	?>
	
	<select class="form-control" name="mdocs-cat<?php if($display == 'display') echo '-'.$display;?>" <?php if($display == 'display') echo 'disabled'; ?> >
	<?php mdocs_display_folder_options_menu($cats, $default_value); ?>
	</select>
	<?php
}

function mdocs_upload_hide_folder($default_value) {

// PATT START 
	?>
<!-- 	<input type=hidden name="mdocs-cat" value="<?php echo $default_value; ?>" > -->
	<input type=hidden name="mdocs-cat" value="<?php
		if ($_GET['id'] == '') {
			echo $_GET['mdocs-cat'];
		} else {
			echo $_GET['id'];
		}
	?>" >
	<?php
// PATT END

}

// VERSION
function mdocs_upload_display_version($cats, $default_value, $display='') {
	?>
	<input class="form-control" type="text" name="mdocs-version<?php if($display == 'display') echo '-'.$display; ?>" <?php if($display == 'display') echo 'disabled'; ?> value="<?php echo $default_value; ?>"/>
	<?php
}
function mdocs_upload_hide_version($default_value) {
	?>
	<input type=hidden name="mdocs-version" value="<?php echo $default_value; ?>" >
	<?php
}
// DATE
function mdocs_upload_display_date($cats, $default_value, $display='') {
	?>
	<input class="form-control" type="text" name="mdocs-last-modified<?php if($display == 'display') echo '-'.$display; ?>" <?php if($display == 'display') echo 'disabled'; ?> value="<?php echo $default_value; ?>"/>
	<?php
}
function mdocs_upload_hide_date($default_value) {
	?>
	<input type=hidden name="mdocs-last-modified" value="<?php echo $default_value; ?>" >
	<?php
}
// FILE STATUS
function mdocs_upload_display_file_status($cats, $default_value, $display='') {
	?>
	<select class="form-control input-lg" name="mdocs-file-status<?php if($display == 'display') echo '-'.$display; ?>" <?php if($display == 'display') echo 'disabled'; ?> id="mdocs-file-status" >
		<option value="public" <?php if($default_value == 'public') echo 'selected'; ?> ><?php _e('Public - [ Everyone can view this file ]','memphis-documents-library'); ?></option>
		<option value="hidden" <?php if($default_value == 'private') echo 'selected'; ?> ><?php _e('Private - [ Only you can view this file ]','memphis-documents-library'); ?></option>
	</select>
	<?php
}
function mdocs_upload_hide_file_status($default_value) {
	if($default_value == 'private') $default_value = 'hidden';
	?>
	<input type=hidden name="mdocs-file-status" value="<?php echo $default_value; ?>" >
	<?php
}
// POST STATUS
function mdocs_upload_display_post_status($cats, $default_value, $display='') {
	?>
	<select class="form-control input-lg" name="mdocs-post-status<?php if($display == 'display') echo '-'.$display; ?>" <?php if($display == 'display') echo 'disabled'; ?> id="mdocs-post-status" >
		<option value="publish" <?php if($default_value == 'public') echo 'selected'; ?>><?php _e('Published','memphis-documents-library'); ?></option>
		<option value="private" <?php if($default_value == 'private') echo 'selected'; ?>><?php _e('Private','memphis-documents-library');  ?></option>
		<option value="pending"  <?php if($default_value == 'pending') echo 'selected'; ?>><?php _e('Pending Review','memphis-documents-library');  ?></option>
		<option value="draft" <?php if($default_value == 'draft') echo 'selected'; ?>><?php _e('Draft','memphis-documents-library');  ?></option>
	</select>
	<?php
}
function mdocs_upload_hide_post_status($default_value) {
	?>
	<input type=hidden name="mdocs-post-status" value="<?php echo $default_value; ?>" >
	<?php
}
// SOCIAL APPS
function mdocs_upload_display_social_apps($cats, $default_value, $display='') {
	?>
	<input class="form-control" type="checkbox" name="mdocs-social<?php if($display == 'display') echo '-'.$display; ?>" <?php if($display == 'display') echo 'disabled '; echo $default_value; ?>  />
	<?php
}
function mdocs_upload_hide_social_apps($default_value) {
	if($default_value == 'checked' || $default_value == 'on') $default_value = 'on';
	else $default_value = 'off';
	?>
	<input type="hidden" name="mdocs-social" value="<?php echo $default_value; ?>" />
	<?php
}
// DOWNLOADABLE BY NON MEMBERS
function mdocs_upload_display_downloadable_nonmembers($cats, $default_value, $display='') {
	?>
	<input class="form-control" type="checkbox" name="mdocs-non-members<?php if($display == 'display') echo '-'.$display; ?>" <?php if($display == 'display') echo 'disabled '; echo $default_value; ?> />
	<?php
}
function mdocs_upload_hide_downloadable_nonmembers($default_value) {
	if($default_value == 'checked' || $default_value == 'on') $default_value = 'on';
	else $default_value = 'off';
	?>
	<input type="hidden" name="mdocs-non-members" value="<?php echo $default_value; ?>" />
	<?php
}
// CONTRIBUTORS
function mdocs_upload_display_contributors($cats, $default_value, $display='') {
	?>
	<div class="mdocs-add-contributor-container" data-contributor-type="add-update">
		<div class="mdocs-contributors-container">
			<button type="button" class="btn btn-primary" id="mdocs-current-owner"></button>
		</div>
		<input autocomplete="off" class="form-control mdocs-add-contributors" type="text" name="mdocs-add-contributors<?php if($display == 'display') echo '-'.$display; ?>" <?php if($display == 'display') echo 'disabled'; ?> placeholder="<?php _e('Add contributor, users and roles types are allowed.', 'memphis-documents-library'); ?>"/>
		<div class="mdocs-user-search-list hidden" ></div>
	</div>
	<?php
}
function mdocs_upload_hide_contributors($default_value) {
	?>
	<input type=hidden name="mdocs-add-contributors" value="<?php echo $default_value; ?>" >
	<?php
}
// AUTHOR
function mdocs_upload_display_author($cats, $default_value, $display='') {
	?>
	<input class="form-control" type="text" name="mdocs-real-author<?php if($display == 'display') echo '-'.$display; ?>" <?php if($display == 'display') echo 'disabled'; ?> id="mdocs-real-author" placeholder="<?php _e('Type the name of the author.', 'memphis-documents-library'); ?>" />
	<?php
}
function mdocs_upload_hide_author($default_value) {
	?>
	<input type=hidden name="mdocs-real-author" value="<?php echo $default_value; ?>" >
	<?php
}
// TAGS
function mdocs_upload_display_tags($cats, $default_value, $display='') {
	?>
	<input class="form-control" type="text" name="mdocs-tags<?php if($display == 'display') echo '-'.$display; ?>" <?php if($display == 'display') echo 'disabled'; ?> id="mdocs-tags" placeholder="<?php _e('Comma Separated List', 'memphis-documents-library'); ?>" />
	<?php
}
function mdocs_upload_hide_tags($default_value) {
	?>
	<input type=hidden name="mdocs-tags" value="<?php echo $default_value; ?>" >
	<?php
}
// CATEGORIES
function mdocs_upload_display_categories($cats, $default_value, $display='') {
	$args = array("hide_empty" => 0, "type" => "post", "orderby" => "name", "order" => "ASC" );
	$categories = get_categories($args);
	?>
	<select multiple class="form-control" name="mdocs-categories<?php if($display == 'display') echo '-'.$display; ?>[]" <?php if($display == 'display') echo 'disabled'; ?> id="mdocs-post-categories">
		<?php
			foreach($categories as $cat_index => $category) {
				echo '<option value="'.$category->name.'">'.$category->name.'</option>';
			}
		?>
	</select>
	<?php
}
function mdocs_upload_hide_categories($default_value) {
	?>
	<input type=hidden name="mdocs-categories" value="<?php echo $default_value; ?>" >
	<?php
}
// DESCRIPTION
function mdocs_upload_display_description($cats, $default_value, $display='') {
	if($display == 'display') $class_name = 'mdocs-desc-'.$display;
	else $class_name = 'mdocs-desc';
	$editor_settings = array('media_buttons' => false, 'editor_class' => 'disabled', 'editor_css' => '<style>display: none;</style>');
	//$editor_settings = array();
	wp_editor('', $class_name, $editor_settings);
}
function mdocs_upload_hide_description($default_value) {
	?>
	<input type=hidden name="mdocs-desc" value="<?php echo $default_value; ?>" >
	<?php
}

function mdocs_uploader($is_admin=true, $atts=null) {
	global $post;
	if(!is_admin()) {
		$start = strpos($post->post_content, '[mdocs_upload_btn');
		$cut = substr($post->post_content, $start+17);
		$end = strpos($cut, ']');
		$final_shortcode = substr($cut,0,$end);
		$atts = shortcode_parse_atts($final_shortcode);
	}
	$current_user = wp_get_current_user();
	
	
	if(isset($post->ID)) {
		$redirect = get_option('mdocs-frontend-upload-redirect');
		if($redirect == 0) $the_permalink = get_permalink($post->ID);
		else $the_permalink = '';
		$is_admin = false;
	} else {
		$the_permalink = 'admin.php?page=memphis-documents.php&mdocs-cat=';
		$is_admin = true;
	}
?>
<div class="row">
	<div class="col-md-12" id="mdocs-add-update-container">
		<div class="page-header">
			<h1 id="mdocs-add-update-header"><?php _e('loading', 'memphis-documents-library'); ?>...</h1>
		</div>
		<div class="">
<!-- PATT start -->
			<div id="alert_status_modal"></div>
<!-- PATT end -->				
			<form class="form-horizontal" enctype="multipart/form-data" action="" method="POST" id="mdocs-add-update-form">
				<?php wp_nonce_field( 'mdocs-upload-file-action', 'mdocs-upload-file-field' ); ?>
				<input type="hidden" name="mdocs-current-user" value="<?php echo $current_user->user_login; ?>" />
				<input type="hidden" name="mdocs-type" value="" />
				<input type="hidden" name="mdocs-index" value="" />
				<input type="hidden" name="mdocs-cat" value="" />
				<input type="hidden" name="mdocs-pname" value="" />
				<!--<input type="hidden" name="mdocs-nonce" value="<?php //echo $_SESSION['mdocs-nonce']; ?>" />-->
				<input type="hidden" name="mdocs-post-status-sys" value="" />
<!-- PATT start -->
<!-- 				<input type ="hidden" name="mdocs-permalink" value="<?php echo $the_permalink; ?>" /> -->				
				<input type ="hidden" name="mdocs-permalink" value="admin.php?page=memphis-documents.php&mdocs-cat=<?php 
if ($_GET['id'] == '') {
	echo $_GET['mdocs-cat'];
} else {
	echo $_GET['id'];
}
				?>" />
<!-- PATT end -->								
				<input type="hidden" name="mdocs-is-admin" value="<?php echo $is_admin; ?>" />
				<div class="well well-lg">
					<div class="page-header">
						<h2><?php _e('File Properties','memphis-documents-library'); ?></h2>
					</div>
					<div class="form-group form-group-lg has-success">
						<label class="col-sm-2 control-label" for="mdocs-name"><?php _e('Name','memphis-documents-library'); ?></label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="mdocs-name" id="mdocs-name" />
						</div>
					</div>
<!-- PATT BEGIN -->						
<!--
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label" for="mdocs"><?php _e('File Uploader','memphis-documents-library'); ?></label>
						<div class="col-sm-10">
							<input class="form-control" type="file" name="mdocs" />
							<p class="help-block" id="mdocs-current-doc"></p>
						</div>
					</div>
-->
									
					<?php do_action( 'wppatt_mld_s3_uploader' ); ?>

<!-- PATT END -->					
					<?php
					/*
					foreach(get_option('mdocs-displayed-file-info') as $index => $file_info) {
						if($file_info['is-form']) {
							$form_info = $file_info['form-data'];
							mdocs_check_uploader_options($atts, $file_info['slug'], $form_info['display-function'], $form_info['hide-function']);
						}
					}
					*/
					mdocs_check_uploader_options($atts, __('folder','memphis_documents_library'), 'mdocs_upload_display_folder', 'mdocs_upload_hide_folder');
					mdocs_check_uploader_options($atts, __('version', 'memphis_documents_library'), 'mdocs_upload_display_version', 'mdocs_upload_hide_version');
					mdocs_check_uploader_options($atts, __('date', 'memphis_documents_library'), 'mdocs_upload_display_date', 'mdocs_upload_hide_date');
					mdocs_check_uploader_options($atts, __('file status', 'memphis_documents_library'), 'mdocs_upload_display_file_status', 'mdocs_upload_hide_file_status');
					mdocs_check_uploader_options($atts, __('post status', 'memphis_documents_library'), 'mdocs_upload_display_post_status', 'mdocs_upload_hide_post_status');
					mdocs_check_uploader_options($atts, __('show social apps', 'memphis_documents_library'), 'mdocs_upload_display_social_apps', 'mdocs_upload_hide_social_apps');
					mdocs_check_uploader_options($atts, __('downloadable by non members', 'memphis_documents_library'), 'mdocs_upload_display_downloadable_nonmembers', 'mdocs_upload_hide_downloadable_nonmembers');
					mdocs_check_uploader_options($atts, __('contributors', 'memphis_documents_library'), 'mdocs_upload_display_contributors', 'mdocs_upload_hide_contributors');
					mdocs_check_uploader_options($atts, __('real author', 'memphis_documents_library'), 'mdocs_upload_display_author', 'mdocs_upload_hide_author');
					mdocs_check_uploader_options($atts, __('tags', 'memphis_documents_library'), 'mdocs_upload_display_tags', 'mdocs_upload_hide_tags');
					mdocs_check_uploader_options($atts, __('categories', 'memphis_documents_library'), 'mdocs_upload_display_categories', 'mdocs_upload_hide_categories');
					mdocs_check_uploader_options($atts, __('description', 'memphis_documents_library'), 'mdocs_upload_display_description', 'mdocs_upload_hide_description');
					
					?>
				</div>
<!-- PATT begin -->				
<!-- 				<input type="submit" class="btn btn-primary" id="mdocs-save-doc-btn" value="" /> -->
				<div class="row" style="margin: auto">
					<div id="wppatt_popup_footer" class="col-sm-12">
						<button type="button" class="btn patt-button patt-button-close" data-dismiss="modal" >Close</button> <!-- btn btn-default -->
						<input type="submit" class="btn patt-button patt-button-save" id="wppatt-mdocs-save-doc-btn" value="PATT Add / Update" /> <!-- btn btn-primary -->
					</div>
				</div>
<!-- PATT end -->		
			</form>
		</div>
		
	</div>
</div>
	
<?php
	//}
}
?>