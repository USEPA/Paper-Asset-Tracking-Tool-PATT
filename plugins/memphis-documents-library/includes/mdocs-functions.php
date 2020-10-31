<?php
$the_rating = array();

function mdocs_edit_file($the_mdocs, $index, $current_cat) {
	?>
	<div class="mdocs-edit-file">
		<span class="update" id="<?php echo $index ?>">
			<i class="fa fa-pencil" aria-hidden="true"></i> <a href="<?php echo 'admin.php?page=memphis-documents.php&mdocs-cat='.$current_cat.'&action=update-doc&mdocs-index='.$index; ?>" title="Update this file" class="edit"><?php _e('Update','memphis-documents-library'); ?></a> |
		</span>
		<span class='delete'>
			<i class="fa fa-remove" aria-hidden="true"></i> <a class='submitdelete' onclick="return showNotice.warn();" href="<?php echo 'admin.php?mdocs-nonce='.$_SESSION['mdocs-nonce'].'&page=memphis-documents.php&mdocs-cat='.$current_cat.'&action=delete-doc&mdocs-index='.$index; ?>"><?php _e('Delete','memphis-documents-library'); ?></a> |
		</span>
		<span class="versions">
			<i class="icon-off"></i> <a href="<?php echo 'admin.php?page=memphis-documents.php&mdocs-cat='.$current_cat.'&mdocs-index='.$index; ?>&action=mdocs-versions" title="<?php _e('Versions','memphis-documents-library'); ?>" class="edit"><?php _e('Versions','memphis-documents-library'); ?></a></span>
	</div>
	<?php
}
function mdocs_get_table_atts() {
	$sa = get_option('mdocs-displayed-file-info');
	foreach($sa as $index => $type) {
		if(!isset($type['show'])) $sa[$index]['show'] = false;
		if(!isset($type['form-data']['show-in-form'])) $sa[$index]['form-data']['show-in-form'] = false;
		if(!isset($type['form-data']['disabled-in-form'])) $sa[$index]['form-data']['disabled-in-form'] = false;
		if(!isset($type['form-data']['default'])) $sa[$index]['form-data']['default'] = '';
		//if(!isset($type['form-data']['show-in-form'])) $sa[$index]['form-data']['show-in-form'] = false;
	}
	return $sa;
}
function  mdocs_display_tabs($the_mdoc) {
	$mdocs_default_content = get_option('mdocs-default-content');
	$mdocs_show_description = get_option('mdocs-show-description');
	$mdocs_show_preview = get_option('mdocs-show-preview');
	$mdocs_show_versions = get_option('mdocs-show-versions');
	ob_start();
	if($mdocs_show_description) {
		?><a class="mdocs-nav-tab <?php if($mdocs_default_content=='description') echo 'mdocs-nav-tab-active'; ?>" data-mdocs-show-type="desc" data-mdocs-id="<?php echo $the_mdoc['id']; ?>"><?php _e('Description', 'memphis-documents-library'); ?></a><?php
	}
	if($mdocs_show_preview) {
		?><a class="mdocs-nav-tab <?php if($mdocs_default_content=='preview') echo 'mdocs-nav-tab-active'; ?>"  data-mdocs-show-type="preview" data-mdocs-id="<?php echo $the_mdoc['id']; ?>"><?php _e('Preview', 'memphis-documents-library'); ?></a><?php
	}
	if($mdocs_show_versions) {
		?><a class="mdocs-nav-tab <?php if($mdocs_default_content=='versions') echo 'mdocs-nav-tab-active'; ?>"  data-mdocs-show-type="versions" data-mdocs-id="<?php echo $the_mdoc['id']; ?>"><?php _e('Versions', 'memphis-documents-library'); ?></a><?php
	}
	?>
	<div class="mdocs-show-container" id="mdocs-show-container-<?php echo $the_mdoc['id']; ?>">
		<?php
		if(!isset($_POST['show_type']) && $mdocs_show_description && $mdocs_default_content == 'description') {
			mdocs_show_description($the_mdoc['id']);
		} elseif(!isset($_POST['show_type']) && $mdocs_show_preview && $mdocs_default_content == 'preview') {
			mdocs_show_preview($the_mdoc);
		} elseif(!isset($_POST['show_type']) && $mdocs_show_versions && $mdocs_default_content == 'versions') {
			mdocs_show_versions($the_mdoc['parent']);
		}
		?>
	</div>
	<?php
	$the_des = ob_get_clean();
	return $the_des;
}

function mdocs_rename_file($upload, $file_name) {
	$upload_dir = wp_upload_dir();
	$index = 0;
	$filename_no_comma = str_replace(',','',$file_name);
	$org_filename = $filename_no_comma;
	while(file_exists($upload_dir['basedir'].'/mdocs/'.$filename_no_comma)) {
		$index++;
		$explode = explode('.',$org_filename);
		$tail = $index.'.'.$explode[count($explode)-1];
		array_pop($explode);
		$filename_no_comma = implode('',$explode).$tail;
	}
	
	$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$filename_no_comma;
	$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$filename_no_comma;
	$upload['filename'] = $filename_no_comma;
	$name = substr($file_name, 0, strrpos($file_name, '.') );
	if(!isset($_POST['mdocs-name']) || $_POST['mdocs-name'] == '') $upload['name'] = $name;
	else $upload['name'] = $_POST['mdocs-name'];
	return $upload;
}
/**
 * Returns the text for a specific file upload error
 * @param int $index The specific errors index value.
 * @return string The error text of the specific index.
 */
function mdocs_file_upload_errors($index) {
	$file_errors = array(
		0 => __('There is no error, the file uploaded with success', 'memphis-documents-library'),
		1 => __('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'memphis-documents-library'),
		2 => __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'memphis-documents-library'),
		3 => __('The uploaded file was only partially uploaded', 'memphis-documents-library'),
		4 => __('No file was uploaded', 'memphis-documents-library'),
		6 => __('Missing a temporary folder', 'memphis-documents-library'),
		7 => __('Failed to write file to disk.', 'memphis-documents-library'),
		8 => __('A PHP extension stopped the file upload.', 'memphis-documents-library'),
	);
	return $file_errors[$index];
}
/**
 * Returns the text for a specific zip error
 * @param int $code The specific errors index value.
 * @return string The error text of the specific code.
 */
function mdocs_zip_archive_errors($code) {
	switch ($code) {
		case 0:
			return 'No error';
		case 1:
			return 'Multi-disk zip archives not supported';
		case 2:
			return 'Renaming temporary file failed';
		case 3:
			return 'Closing zip archive failed';
		case 4:
			return 'Seek error';
		case 5:
			return 'Read error';
		case 6:
			return 'Write error';
		case 7:
			return 'CRC error';
		case 8:
			return 'Containing zip archive was closed';
		case 9:
			return 'No such file';
		case 10:
			return 'File already exists';
		case 11:
			return 'Can\'t open file';
		case 12:
			return 'Failure to create temporary file';
		case 13:
			return 'Zlib error';
		case 14:
			return 'Malloc failure';
		case 15:
			return 'Entry has been changed';
		case 16:
			return 'Compression method not supported';
		case 17:
			return 'Premature EOF';
		case 18:
			return 'Invalid argument';
		case 19:
			return 'Not a zip archive';
		case 20:
			return 'Internal error';
		case 21:
			return 'Zip archive inconsistent';
		case 22:
			return 'Can\'t remove file';
		case 23:
			return 'Entry has been deleted';
		default:
			return 'An unknown error has occurred('.intval($code).')';
	}             
}
function mdocs_delete_file() {
	$mdocs_file = mdocs_get_file_by(mdocs_sanitize_string($_REQUEST['mdocs-id']), 'id');
	$upload_dir = wp_upload_dir();
	if(is_array($mdocs_file['archived'])) foreach($mdocs_file['archived'] as $key => $value) @unlink($upload_dir['basedir'].'/mdocs/'.$value);
	wp_delete_attachment( intval($mdocs_file['id']), true );
	wp_delete_post( intval($mdocs_file['parent']), true );
	
// PATT BEGIN
if($_REQUEST['s3-ecms'] != 1) {
	do_action( 'wppatt_mld_delete_attachment_info_db', $mdocs_file, $upload_dir );
}
// PATT END	

	if(file_exists($upload_dir['basedir'].'/mdocs/'.$mdocs_file['filename'])) @unlink($upload_dir['basedir'].'/mdocs/'.$mdocs_file['filename']);
	mdocs_delete_file_by(mdocs_sanitize_string($_REQUEST['mdocs-id']), 'id');
	if(get_option('mdocs-box-view-key') != '' && get_option('mdocs-preview-type') == 'box') {
		$boxview = new mdocs_box_view();
		$boxview->deleteFile($mdocs_file);
	}
}
/**
 * Returns an array of date values.
 * @param string $date The date string sent to be converted. eg 02-12-2016 9:12am
 * @return array The date array.
 */
function mdocs_format_date($date) {
	date_default_timezone_set('UTC');
	$date_format = get_option('mdocs-date-format');
	$check_if_time_in_format = preg_match('/[gGhHis]/',$date_format);
	$formated_dates = array();
	if(method_exists('DateTime', 'createFromFormat')) {
		$dtime = DateTime::createFromFormat($date_format, $date);
		if($dtime != false) {
			if($check_if_time_in_format == false) $gmdate = $formated_dates['gmdate'] = floatval($dtime->getTimestamp());
			else $gmdate = $formated_dates['gmdate'] = floatval($dtime->getTimestamp()-MDOCS_TIME_OFFSET);
			$formated_dates['date'] = $gmdate+MDOCS_TIME_OFFSET;
			$formated_dates['formated-date'] = date($date_format,$formated_dates['date']);
			$formated_dates['wp-date'] = date('Y-m-d H:i:s',$formated_dates['date']);
			$formated_dates['wp-gmdate'] = date('Y-m-d H:i:s',$gmdate);
		} else {
			$gmdate = $formated_dates['gmdate'] = time();
			$formated_dates['date'] = $gmdate+MDOCS_TIME_OFFSET;
			$formated_dates['formated-date'] = date($date_format,$formated_dates['date']);
			$formated_dates['wp-date'] = date('Y-m-d H:i:s',$formated_dates['date']);
			$formated_dates['wp-gmdate'] = date('Y-m-d H:i:s',$gmdate);
		}
	} else {
		$gmdate = $formated_dates['gmdate'] = time();
		$formated_dates['date'] = $gmdate+MDOCS_TIME_OFFSET;
		$formated_dates['formated-date'] = date($date_format,$formated_dates['date']);
		$formated_dates['wp-date'] = date('Y-m-d H:i:s',$formated_dates['date']);
		$formated_dates['wp-gmdate'] = date('Y-m-d H:i:s',$gmdate);
	}
	return $formated_dates;
}
/**
 * Returns an array of date values.
 * @param float $gmdate The unix epoch date sent to be converted. eg 1486199520
 * @return array The date array.
 */
function mdocs_format_unix_epoch($gmdate=null) {
	date_default_timezone_set('UTC');
	if($gmdate == null) $gmdate = time();
	$date_format = get_option('mdocs-date-format');
	$formated_dates = array();
	$formated_dates['gmdate'] = $gmdate;
	$formated_dates['date'] = $gmdate+MDOCS_TIME_OFFSET;
	$formated_dates['formated-date'] = date($date_format, $formated_dates['date']);
	$formated_dates['wp-date'] = date('Y-m-d H:i:s', $formated_dates['date']);
	$formated_dates['wp-gmdate'] = date('Y-m-d H:i:s', $gmdate);
	return $formated_dates;
}
/**
 * Returns the Memphis Documents data.
 * @param string $search_value The value to search for.
 * @param string $search_index The array key to use for searching for the documents array data.
 * @ return An array of data for the specific document.
 */
function get_the_mdoc_by($search_value, $search_index,$return_index=false) {
	$mdocs = mdocs_array_sort();
	foreach($mdocs as $index => $doc) {
		if($search_value == $doc[$search_index]) {
			$doc['index'] = $index;
			if($return_index == true) return $index;
			else return $doc;
			break;
		}
	}
}
function mdocs_get_file_by($search_value, $search_index) {
	return get_the_mdoc_by($search_value, $search_index);
}
function mdocs_get_file_index_by($search_value, $search_index) {
	return get_the_mdoc_by($search_value, $search_index,true);
}
/**
 * Deletes the Memphis Documents data.
 * @param string $search_value The value to search for.
 * @param string $search_index The array key to use for searching for the documents array data.
 */
function mdocs_delete_file_by($search_value, $search_index) {
	$mdocs = get_option('mdocs-list');
	foreach($mdocs as $index => $doc) {
		if($search_value == $doc[$search_index]) {
			$doc['index'] = $index;
			unset($mdocs[$index]);
			$mdocs = array_values($mdocs);
			mdocs_save_list($mdocs);
			break;
		}
	}
}
/**
 * Checks whether to hide the file type icon if false, then return a html image string of the files type.
 * @param array $the_mdoc The mDocs file to be processsed
 * @return string An html string of the files type.
 */
function mdocs_get_file_type_icon($the_mdoc, $override=false, $padding=false, $just_link=false) {
	if(get_option('mdocs-hide-file-type-icon') == false || $override == true) {
		$file_type = wp_check_filetype($the_mdoc['filename']);
		if($file_type['ext'] == false) {
			$exp = explode('.',$the_mdoc['filename']);
			$file_type['ext'] = strtolower($exp[1]);
		} else $file_type['ext'] = strtolower($file_type['ext']);
		if($padding) $padding = 'style="margin-left: 15%"';
		else $padding = '';
		if($just_link == false) {
			if(file_exists(MDOCS_PATH.'assets/imgs/filetype-icons/'.$file_type['ext'].'.png'))  return '<img src="'.plugins_url().'/memphis-documents-library/assets/imgs/filetype-icons/'.$file_type['ext'].'.png" class="mdoc-file-type-icon" '.$padding.' />';
			else $return = '<img src="'.plugins_url().'/memphis-documents-library/assets/imgs/filetype-icons/unknow.png" class="mdoc-file-type-icon" '.$padding.' />';
		} else {
			if(file_exists(MDOCS_PATH.'assets/imgs/filetype-icons/'.$file_type['ext'].'.png'))  return plugins_url().'/memphis-documents-library/assets/imgs/filetype-icons/'.$file_type['ext'].'.png';
			else $return = plugins_url().'/memphis-documents-library/assets/imgs/filetype-icons/unknow.png';
		}
	}
}
/**
 * Removes a diretory and all of it files and child directories.
 * @param string $the_dir The full path to the directory to be removed.
 */
function mdocs_rmdir($the_dir) {
	if(is_dir($the_dir)) {
		$the_children = glob($the_dir.'/*'); 
		foreach($the_children as $the_child){
			if(is_dir($the_child)) {
				mdocs_rmdir($the_child);
			} elseif(is_file($the_child)) {
				unlink($the_child);
			}
		}
		$hidden = preg_grep('/^\.\w+/', scandir($the_dir));
		foreach($hidden as $file) {
			unlink($the_dir.'/'.$file);
		}
		rmdir($the_dir);
	}
}
/**
 * Sanitizes an array and returns a safe version of the data in the array.
 * @param array $array The array to be sanitized.
 * @return The sanitized array.
 */
function mdocs_sanitize_array($array) {
	foreach($array as $index => $array_value) {
		switch($index) {
			case 'mdocs-desc':
				$array[$index] = wp_kses_post($array_value);
				break;
			case 'mdocs-last-modified':
				$array[$index] = sanitize_text_field($array_value);
				$array[$index] = esc_attr($array[$index]);
				break;
			default:
				if(is_string($array[$index])) {
					$array[$index] = sanitize_text_field($array_value);
					$array[$index] = esc_attr($array[$index]);
				}
				break;
		}
	}
	return $array;
}
function mdocs_sanitize_string($string) {
	$string = sanitize_text_field($string);
	$string = esc_attr($string);
	return $string;
}
function mdocs_increase_minor_version($version) {
	if(strlen($version) > 1) {
		$last_char = substr($version, -1);
		if(is_numeric($last_char)) {
			$minor_version = intval($last_char);
			$minor_version++;
			$version_front = substr($version, 0, strlen($version)-1);
			return $version_front.$minor_version;
		} else return $version;
	} else {
		return $version.'.1';
	}
}
/**
 * Uploads the file and created a mDocs post.
 * @param array $file The files properties.
 * @param boolean $import A boolean value that checks if the file to be processed is an imported file.
 */
function mdocs_process_file($file, $import=false) {
	global $current_user;
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$upload_result = '';
	if(isset($_POST['mdocs-type'])) $mdocs_type = $_POST['mdocs-type'];
	else $mdocs_type = null;
	$post_status = $file['post-status'];
	if($import) $desc = $file['desc'];
	elseif(isset($_POST['mdocs-desc'])) $desc = $_POST['mdocs-desc'];
	else $desc = '';
	$upload_dir = wp_upload_dir();
	if($import == false && isset($_POST['mdocs-last-modified'])) {
		$the_date = mdocs_format_date($_POST['mdocs-last-modified']);
	} elseif($import) $the_date = mdocs_format_unix_epoch($file['modifed']);
	else $the_date = mdocs_format_unix_epoch();
	
	if($file['error'] > 0) {
		//$upload['error'] = __('An Error has occured.','memphis-documents-library');
		$upload['error'] = mdocs_file_upload_errors($file['error']);
		return $upload;
	}
	if($import == false) {
		$filename_no_comma = str_replace(',','',$file['name']);
		$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$filename_no_comma;
		$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$filename_no_comma;
		$upload['filename'] = $filename_no_comma;
		if(file_exists($upload_dir['basedir'].'/mdocs/'.$filename_no_comma)) $upload = mdocs_rename_file($upload, $file['name']);
		else {
			$name = substr($file['name'], 0, strrpos($file['name'], '.') );
			if(!isset($_POST['mdocs-name']) || $_POST['mdocs-name'] == '') $upload['name'] = $name;
			else $upload['name'] = $_POST['mdocs-name'];
		}
		
// PATT start		
		//$upload_result = move_uploaded_file($file['tmp_name'], $upload['file']);
		$upload_result = true;
// PATT end

		if($upload_result == false) $upload_result = @rename($file['tmp_name'], $upload['file']);
		if($upload_result == false) $upload['error'] = __('An Error has occurred on line 320 of the mdocs-functions.php file.','memphis-documents-library');
	} else {
		$filename_no_comma = str_replace(',','',$file['filename']);
		$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$filename_no_comma;
		$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$filename_no_comma;
		$upload['filename'] = $filename_no_comma;
		$upload['name'] = $file['name'];
	}
	if($upload_result !== false) {
		$wp_filetype = wp_check_filetype($upload['file'], null );
		if($the_date['wp-date'] > time() && $post_status === 'publish') $post_status = 'future';
		else $post_status = (string)$post_status;
		if($mdocs_type == 'mdocs-add' || $import == true) {
			$mdocs_post = array(
				'post_title' => $upload['name'],
				'post_status' => $post_status,
				'post_content' => '[mdocs_post_page new=true]',
				'post_author' => $current_user->ID,
				'post_date' => $the_date['wp-date'],
				'post_date_gmt' => $the_date['wp-gmdate'],
				'post_type' => 'mdocs-posts',
			);
			$mdocs_post_id = wp_insert_post( $mdocs_post, true );
			if(isset($_POST['mdocs-categories'])) {
				$category_as_id = array();
				if(is_array($_POST['mdocs-categories'])) {
					foreach($_POST['mdocs-categories'] as $category) array_push($category_as_id, get_cat_ID($category));
					wp_set_post_categories( $mdocs_post_id, $category_as_id );
				}
			}
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => basename($upload['name']),
				'post_content' => '[mdocs_media_attachment]',
				'post_author' => $current_user->ID,
				'post_status' => 'inherit',
				'comment_status' => 'closed',
				'post_date' => $the_date['wp-date'],
				'post_date_gmt' => $the_date['wp-gmdate'],
			 );
			$mdocs_attach_id = wp_insert_attachment( $attachment, $upload['file'], $mdocs_post_id );
			//$mdocs_attach_data = wp_generate_attachment_metadata( $mdocs_attach_id, $upload['file'] );
			//wp_update_attachment_metadata( $mdocs_attach_id, $mdocs_attach_data );
			$upload['parent_id'] = $mdocs_post_id;
			$upload['attachment_id'] = $mdocs_attach_id;

// PATT BEGIN			
			
			//wp_set_post_tags($mdocs_post_id, 'test');
			echo $folderdocinfofile_id;
			
			if($import == false && isset($_POST['mdocs-tags'])) echo $folderdocinfofile_id;
// PATT END			
		} elseif($mdocs_type == 'mdocs-update') {
			$mdocs_post = array(
				'ID' => $file['parent'],
				'post_title' => $upload['name'],
				'post_status' =>$post_status,
				'post_content' => '[mdocs_post_page]',
				'post_author' => $current_user->ID,
				'post_date' => $the_date['wp-date'],
				'post_date_gmt' => $the_date['wp-gmdate'],
			);
			$mdocs_post_id = wp_update_post( $mdocs_post );
			if(key_exists('mdocs-categories', $_POST)) {
				if(is_array($_POST['mdocs-categories'])) {
					$category_as_id = array();
					foreach($_POST['mdocs-categories'] as $category) array_push($category_as_id, get_cat_ID($category));
					wp_set_post_categories( $mdocs_post_id, $category_as_id );
				}
			}
			$attachment = array(
				'ID' => $file['id'],
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => $upload['name'],
				'post_content' => '[mdocs_media_attachment]',
				'post_status' => 'inherit',
				'post_date' => $the_date['wp-date'],
				'post_date_gmt' => $the_date['wp-gmdate'],
			 );
			update_attached_file( $file['id'], $upload['file'] );
			$mdocs_attach_id = wp_update_post( $attachment );
			$mdocs_attach_data = wp_generate_attachment_metadata( $mdocs_attach_id, $upload['file'] );
			wp_update_attachment_metadata( $mdocs_attach_id, $mdocs_attach_data );
			//wp_set_post_tags( $mdocs_post_id, $upload['name'].', memphis documents library, '.$wp_filetype['type'] );
			wp_set_post_tags($mdocs_post_id, $_POST['mdocs-tags']);
		}
		$upload['desc'] = $desc;
		$upload['modified'] = $the_date['gmdate'];
	}
	return $upload;
}
/**
 * Simple check to stop users from doing the same action more than once.
 */
/*
function mdocs_nonce() {
	if(get_option('mdocs-disable-sessions') == false && function_exists('session_status')) {
		if(session_status() == 2) session_write_close();
		session_name('mdocs-'.md5('mdocs-id-'.get_site_url()));
		if(is_ssl()) session_set_cookie_params(0, '/', '', true, true);
		else session_set_cookie_params(0, '/', '', false, true);
		session_id(md5(MDOCS_SESSION_ID.get_site_url().wp_get_current_user()->ID));
		if(session_status() == 1) session_start();
		if(isset($_SESSION['mdocs-nonce'])) define('MDOCS_NONCE',$_SESSION['mdocs-nonce']);
		if(!isset($_SESSION['mdocs-nonce']) || isset($_REQUEST['mdocs-nonce'])) $_SESSION['mdocs-nonce'] = md5(microtime().rand(0,1000000));
		if(!isset($_SESSION['mdocs-sort-type'])) $_SESSION['mdocs-sort-type'] = null;
		if(!isset($_SESSION['mdocs-order-by'])) $_SESSION['mdocs-order-by'] = null;
		session_write_close();
		if(!defined('MDOCS_NONCE')) {
			define('MDOCS_NONCE', md5(rand(0,1000000)));
		}
	} else {
		define('MDOCS_NONCE', md5(rand(0,1000000)));
		$_SESSION['mdocs-nonce'] = null;
	}
}
function mdocs_is_sessions_enabled() {
	if ( version_compare(phpversion(), '5.4.0', '>=') ) {
		$session_status = session_status();
		if($session_status === 0 || $session_status === PHP_SESSION_DISABLED) return false;
		else return true;
	} else {
		return session_id() === '' ? false : true;
	}
}
*/
function mdocs_sort_file_info() {
	$file_info = get_option('mdocs-displayed-file-info');
	
	
	$file_info = get_option('mdocs-displayed-file-info');
	foreach($file_info as $index => $type) {
		if(!isset($type['show'])) $file_info[$index]['show'] = false;
		if(!isset($type['form-data']['show-in-form'])) $file_info[$index]['form-data']['show-in-form'] = false;
		if(!isset($type['form-data']['disabled-in-form'])) $file_info[$index]['form-data']['disabled-in-form'] = false;
		if(!isset($type['form-data']['default'])) $file_info[$index]['form-data']['default'] = '';
		//if(!isset($type['form-data']['show-in-form'])) $sa[$index]['form-data']['show-in-form'] = false;
	}
	
	
	
	if(!empty($file_info)) $file_info_sort = mdocs_sort_array('order', 'asc', $file_info);
	else $file_info_sort = $file_info;
	//foreach($file_info_sort as $index => $type) $file_info_final['show-'.strtolower($type['slug'])] = $type;
	return $file_info_sort;
}
function mdocs_sort_array($orderby=null, $sort_type=null, $the_array=null) {
	if($orderby == 'description') $orderby = 'desc';
	if($orderby == 'file-size') $orderby = 'size';
	if($the_array == null) $mdocs = get_option('mdocs-list');
	else $mdocs = $the_array;
	$key_values = array();
	$orderby = str_replace('show-', '', $orderby);
	foreach($mdocs as $key => $row) {
		$key_values[$key] = strtolower($row[$orderby]);
	}
	if($sort_type == 'desc') $sort_type = SORT_DESC;
	if($sort_type == 'asc') $sort_type = SORT_ASC;
	array_multisort($key_values, $sort_type, $mdocs);
	$mdocs = array_values($mdocs);
	return $mdocs;
}
function mdocs_array_sort($the_array=null, $orderby=null, $sort_types=null, $is_widget=false) {
	if($the_array == null) $the_array = get_option('mdocs-list');
	if($orderby == null) $orderby = get_option('mdocs-sort-type');
	if($sort_types == null) $sort_types = get_option('mdocs-sort-style');
	$disable_user_sort = get_option('mdocs-disable-user-sort');
	if($disable_user_sort == false && get_option('mdocs-hide-sortbar') == false && $is_widget == false) {
		$sort_types = get_option('mdocs-sort-style');
		if(isset($_COOKIE['mdocs-sort-type'])) $orderby = $_COOKIE['mdocs-sort-type'];
		if(isset($_COOKIE['mdocs-sort-range'])) $sort_types = $_COOKIE['mdocs-sort-range'];
	}
	if($sort_types == 'desc') $sort_types = SORT_DESC;
	if($sort_types == 'asc') $sort_types = SORT_ASC;
    if($the_array != null) {
		foreach($the_array as $a){ 
			foreach($a as $key=>$value){ 
				if(!isset($sortArray[$key])){ 
					$sortArray[$key] = array(); 
				} 
				$sortArray[$key][] = $value; 
			} 
		}
		if($orderby == 'file-size') $orderby = 'size';
		elseif($orderby == 'download') $orderby = 'downloads';
		elseif($orderby == 'real-author') $orderby = 'author';
		$array_lowercase = array_map('strtolower', $sortArray[$orderby]);
		if(is_numeric($array_lowercase[0])) $sort_var_type = SORT_NUMERIC;
		else $sort_var_type = SORT_STRING;
		
		//var_dump($array_lowercase);
		@array_multisort($array_lowercase, $sort_types, $sort_var_type,$the_array);
		$the_array = array_values($the_array);
		return $the_array;
	} else return array();
}

function mdocs_errors($msg, $type='updated', $frontend=false) {
	if($type == 'error') {
		$msg = '<div class="alert alert-danger" role="alert"><strong>'.__('Memphis Error','memphis-documents-library').':</strong><p>'.$msg.'</p></div>';
	} else {
		$msg = '<div class="alert alert-info" role="alert"><strong>'.__('Memphis Info','memphis-documents-library').':</strong><p>'.$msg.'</p></div>';
	}
	if($frontend) {
		?>
	<script>
		jQuery('<?php echo $msg; ?>').insertBefore('#mdocs-upload-frontend');
	</script>
	<?php
	} else {
	?>
	<script>
		jQuery( document ).ready(function() {
			jQuery('.mdoc-navbar-container, .mdocs-error').append('<?php echo $msg; ?>');
		});
	</script>
	<?php
	}
}
/**
 * Checks whether the mdocs folder is read and writable.
 * @return boolean Returns a boolean value true for read and writable and false if not.
 */
function mdocs_check_read_write() {
	$is_read_write = false;
	$upload_dir = wp_upload_dir();
	if(is_readable($upload_dir['basedir']) && is_writable($upload_dir['basedir'])) $is_read_write = true;
	return $is_read_write;
}
/**
 * This function outputs a dropdown list of all folders created. 
 * @param array $folders The current level of the folder array.
 * @param string $current_folder The current folder being displayed.
 * @param int $depth The current depth of the folders array.
 * @param boolean $echo Weather to show or hide this dropdown menu.
 */
function mdocs_display_folder_options_menu($folders, $current_folder='', $depth=0, $echo=true) {
	$nbsp = '';
	for($i=0;$i < $depth;$i++) $nbsp .= '&nbsp;&nbsp;';
	foreach( $folders as $index => $cat ){
		if($current_folder === $cat['slug']) $is_selected = 'selected="selected"';
		else $is_selected = '';
		if($echo) echo '<option  value="'.$cat['slug'].'" '.$is_selected.'>'.$nbsp.stripcslashes($cat['name']).'</option>';
		if(count($cat['children']) > 0) { 
			mdocs_display_folder_options_menu($cat['children'], $current_folder ,$cat['depth']+1);
		}
	}
}
/**
 * Searches an array of data and returns the value.
 * @param array $array The array to search.
 * @param string $search The value to search for.
 * @return array The value searched for.
 */
function mdocs_recursive_search($array, $search='') {
    if ($array) {
        foreach ($array as $index => $value) {
            if (is_array($value)) {
                $result = mdocs_recursive_search($value, $search);
				if($result != null) return $result;
            } else {
				if($search === $value) return $array;
            }
        }
    }
}
/**
 * Returns the folder data.
 * @param string|array $current_folder The current category that the user is in.
 * @return array The folder data.
 */
function mdocs_get_the_folder($current_folder=null, $shortcode=null) {
	$cats =  get_option('mdocs-cats');
	if(is_string($current_folder) && $current_folder != '') $current_folder = $current_folder;
	elseif($current_folder != null && !isset($_GET['mdocs-cat']) && isset($current_folder['cat'])) $current_folder = $current_folder['cat'];
	elseif(isset($_GET['mdocs-cat']) && $current_folder == null) $current_folder = mdocs_sanitize_string($_GET['mdocs-cat']);
	elseif(isset($_GET['mdocs-att']) && $shortcode != $_GET['mdocs-att']) $current_folder = $shortcode;
	elseif(isset($_GET['mdocs-cat']) && $current_folder != null) $current_folder = mdocs_sanitize_string($_GET['mdocs-cat']);
	else $current_folder = $cats[0]['name'];
	return mdocs_recursive_search(get_option('mdocs-cats'),$current_folder);
}
/**
 * Returns the folder data.
 * @param string $slug The slug of the folder that is being searched for.
 * @return array The folder data.
 */
function mdocs_get_the_folder_by_slug($slug) {
	return mdocs_recursive_search(get_option('mdocs-cats'),$slug);
}
/**
 * Returns the permalink to the WordPress post.
 * @param array|string $id Either a post array or a post id which will be used to generate a permalink.
 * @return string $permalink The permalink string.
 */
function mdocs_get_permalink($id, $override_admin=false) {
	if(!is_admin() || $override_admin) {
		$permalink = get_permalink($id);
		if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink) || preg_match('/\?mdocs-posts=/',$permalink)) {
			$permalink = $permalink.'&mdocs-cat=';
		} else $permalink = $permalink.'?mdocs-cat=';
	} else $permalink = 'admin.php?page=memphis-documents.php&mdocs-cat=';
	return $permalink;
}
/**
 * Returns the number of columns in the file list table.
 * @return int The number of columns in the file list table.
 */
function mdocs_get_num_cols() {
	if(is_admin()) $num_cols = 2;
	else $num_cols = 1;
	foreach(get_option('mdocs-displayed-file-info') as $key => $option) { if(isset($option['show']) && $option['show']) $num_cols++; }
	return $num_cols;
}
/**
 * Returns a html string of all the childs parents.
 * @param string $child The child category to get the parents of.
 * @param string $html The html string.
 * @return string The html string of all the parents.
 */
function mdocs_get_tabs($child, $tabs = array('parent-tab' => '', 'current-tab' => '&emsp;&emsp;', 'child-tab' => '&emsp;&emsp;&emsp;&emsp;'), $data) {
	$parent = mdocs_get_the_folder($child);
	$emsp = '&emsp;&emsp;';
	if($parent['parent'] != '') {
		mdocs_get_tabs($parent['parent'], $tabs, $data);
		$tabs['current-tab'] = $tabs['current-tab'].$emsp;
		$tabs['child-tab'] = $tabs['child-tab'].$emsp;
		$tabs['parent-tab'] = $tabs['parent-tab'].$emsp;
		$html = '
			<tr class="mdocs-parent-cat" >
				<td colspan="'.mdocs_get_num_cols().'" id="title" class="mdocs-tooltip">
					'.$tabs['parent-tab'].
					'<a class="mdocs-sort-option" data-folder="'.$parent['slug'].'" data-sort="'.$data['orderby'].'" data-sort-type="'.$data['sort-type'].'" data-current-sort="'.$data['orderby'].'" data-current-sort-type="'.$data['sort-type'].'" data-is-dashboard="'.$data['is-dashboard'].'" data-is-folder="true" data-hide-folder="'.$data['hide-folder'].'" data-hide-main-folder="'.$data['hide-main-folder'].'" ><i class="fas fa-folder-open" aria-hidden="true"></i> '.$parent['name'].'</a>
				</td>
			</tr>';
		echo $html;
	}
	return $tabs;
}

function mdocs_custom_mime_types($existing_mimes=array()) {
	// Add file extension 'extension' with mime type 'mime/type'
	$mdocs_allowed_mime_types = get_option('mdocs-allowed-mime-types');
	foreach($mdocs_allowed_mime_types as $index => $mime) {
		$existing_mimes[$index] = $mime;
	}
	$mdocs_removed_mime_types = get_option('mdocs-removed-mime-types');
	foreach($mdocs_removed_mime_types as $index => $mime) {
		unset($existing_mimes[$mime]);
	}
	return $existing_mimes;
}

function mdocs_list_header($show=true,$sortbar_data=null) {
	$current_cat = mdocs_get_current_cat();
	if($show) {
		global $post;
		$mdocs = get_option('mdocs-list');
		$num_docs = count($mdocs);
		$cats = get_option('mdocs-cats');
		$upload_dir = wp_upload_dir();
		$message = '';
		/*
		if($sortbar_data['is-dashboard'] == false && $sortbar_data != null) {
			$permalink = get_permalink($post->ID);
			if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
				$permalink = $permalink.'&mdocs-cat=';
			} else $permalink = $permalink.'?mdocs-cat=';
		} else {*/
			$permalink = '?mdocs-cat=';
		//}
		?>
		<div class="mdocs">
			<?php /*if($sortbar_data['is-dashboard'] || is_admin() && $sortbar_data == null) { mdocs_donate_btn(); }*/ ?>
		<div class="mdocs-wrap">
			<div class="mdocs-admin-preview"></div>
			<?php
			
			if($message != "" && $type != 'update') { ?> <div id="message" class="error" ><p><?php _e($message); ?></p></div> <?php }
			
			
			if($sortbar_data['is-dashboard'] || $sortbar_data == null) { ?>
			<h2><?php _e("Folder/File Attachments",'memphis-documents-library'); ?></h2>
			<?php
			// ERRORS AND UPDATES
			if(isset($_FILES['mdocs']) && $_FILES['mdocs']['name'] == '' && $_POST['mdocs-type'] == 'mdocs-add')  { mdocs_errors(MDOCS_ERROR_1,'error');  }
			?>
			<div class="btn-group" role="group">
				<?php //PATT BEGIN
				if((preg_match('#[0-9]#',$_GET['mdocs-cat']) && $_GET['page'] == 'memphis-documents.php') || $_GET['page'] == 'filedetails') { ?>
				<button class="add-update-btn btn btn-danger" data-toggle="modal" data-target="#mdocs-add-update" data-mdocs-id="" data-is-admin="<?php echo $sortbar_data['is-dashboard']; ?>" data-action-type="add-doc"  data-current-cat="<?php echo $_GET['mdocs-cat']; ?>" href=""><i class="fa fa-upload fa-lg" aria-hidden="true"></i> <?php _e('Add New Document','memphis-documents-library'); ?></button>
				<?php
				if(current_user_can('mdocs_batch_edit')) { ?>
				<button class="btn btn-default" onclick="mdocs_batch_edit()" data-toggle="mdocs-modal" data-target="#mdocs-batch-edit"><i class="fa fa-pencil-alt" aria-hidden="true"></i> <?php _e('Batch Edit', 'memphis-documents-library'); ?></button>
				<?php }
				if(current_user_can('mdocs_batch_move')) { ?>
				<button class="btn btn-default" onclick="mdocs_batch_move()" data-toggle="mdocs-modal" data-target="#mdocs-batch-move"><i class="fa fa-sync" aria-hidden="true"></i> <?php _e('Batch Move', 'memphis-documents-library'); ?></button>
				<?php }
				if(current_user_can('mdocs_batch_delete')) { ?>
				<button class="btn btn-default" onclick="mdocs_batch_delete()" data-toggle="mdocs-modal" data-target="#mdocs-batch-delete"><i class="fa fa-trash" aria-hidden="true"></i> <?php _e('Batch Delete', 'memphis-documents-library'); ?></button>
				<?php }
				}
				if (current_user_can( 'mdocs_manage_options' )) {
				?>
				<div class="btn-group" role="group">
					<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"><i class="fa fa-wrench" aria-hidden="true"></i> <?php _e('Options','memphis-documents-library'); ?> <i class="fa fa-caret-down" aria-hidden="true"></i></button>
					<ul class="mdocs-dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
						<li role="presentation" class="dropdown-header"><?php _e('File Options','memphis-documents-library'); ?></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=cats"><?php _e('Edit Folders','memphis-documents-library'); ?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=allowed-file-types"><?php _e('Allowed File Types','memphis-documents-library'); ?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=import"><?php _e('Import','memphis-documents-library'); ?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=export"><?php _e('Export','memphis-documents-library'); ?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=batch"><?php _e('Batch Upload','memphis-documents-library'); ?></a></li>
						<li role="presentation" class="divider"></li>
						<li role="presentation" class="dropdown-header"><?php _e('Admin Options','memphis-documents-library'); ?></li>
						<?php
						if (current_user_can( 'mdocs_manage_settings' )) {
						?>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=settings"><?php _e('Settings','memphis-documents-library'); ?></a></li>
						<?php } ?>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=find-lost-files"><?php _e('Find Lost Files','memphis-documents-library'); ?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=filesystem-cleanup"><?php _e('File System Cleanup','memphis-documents-library'); ?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=restore"><?php _e('Restore To Default','memphis-documents-library'); ?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=short-codes"><?php _e('Short Codes','memphis-documents-library'); ?></a></li>
						<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=server-compatibility"><?php _e('Test Server Compatibility','memphis-documents-library'); ?></a></li>
					</ul>
				</div>
				<?php } ?>
			</div>
			<br><br>
			<?php } ?>
			<div class="mdocs-development-warning"></div>
			<div class="mdoc-navbar-container">
				<?php
				if(get_option('mdocs-hide-navbar') == false || $sortbar_data['hide-folder']) {
				?>
<div onKeyPress="return myFunction(event)">
  <form id="form01" onsubmit="return false;" >
    Enter Folder/Document ID: <input type="text" name="folderdocid">
    <input type="button" value="Submit" onclick="myFunction(0)">
  </form><br />
</div>

<script>
function myFunction(e) {
   if((e && e.keyCode == 13) || e == 0) {
     //alert("The form was submitted");
     document.forms.form01.submit();
var url = window.location.href; 
var passedcatsArray =  
    <?php echo json_encode($cats); ?>;
if(document.forms.form01.folderdocid.value != '' && passedcatsArray.some(item => item.name === document.forms.form01.folderdocid.value)){
url += '&mdocs-cat='+document.forms.form01.folderdocid.value;
window.location.href = url;
} else {
alert("Enter a valid folder/document ID.");
window.location.href = url;
}
   }
}
</script>




			<?php } ?>
			</div>
		</div>
		<?php
		
	} else {
		echo '<div class="mdocs"><div class="mdocs-wrap"></div>';
	}
	return $current_cat;
}

function mdocs_get_current_cat($atts=null) {
	$cats =  get_option('mdocs-cats');
	$current_cat = '';
	if(isset($_GET['mdocs-cat']) && !isset($atts['cat'])) $current_cat = mdocs_sanitize_string($_GET['mdocs-cat']);
	elseif(!is_string($cats) && !isset($atts['cat'])) $current_cat = $cats[0]['slug'];
	elseif(isset($_GET['mdocs-cat']) && isset($atts['cat']) && isset($_GET['mdocs-att']) &&  $atts['cat'] == mdocs_sanitize_string($_GET['mdocs-att'])) {
		$cat = mdocs_recursive_search($cats, mdocs_sanitize_string($_GET['mdocs-cat']));
		$current_cat = $cat['slug'];
	} elseif(isset($atts['cat'])) {
		$cat = mdocs_recursive_search($cats, $atts['cat']);
		$current_cat = $cat['slug'];
	} 
	return $current_cat;
}

//LOADS ALL SEARCH FILTERS
add_action( 'init', 'mdocs_load_search_filters' );
function mdocs_load_search_filters() {
	add_filter( 'pre_get_posts', 'mdocs_get_posts' );
	add_filter('pre_get_posts','mdocs_alter_searchfilters');
	if(get_option('mdocs-show-media-files') == false) add_filter( 'ajax_query_attachments_args', 'show_current_user_attachments', 10, 1 );
	if(get_option('mdocs-show-media-files') == false) add_filter( 'posts_where', 'mdocs_hide_attachments' );
	if(get_option('mdocs-show-advanced-search') == true && get_option('mdocs-hide-all-posts') == false) add_filter( 'posts_where', 'mdocs_robust_search' );
}

// GET ALL MDOCS POST AND DISPLAYS THEM ON THE MAIN PAGE.
function mdocs_get_posts( $query ) {
	$remove_post_from_homepage = get_option('mdocs-remove-posts-from-homepage');
	if($remove_post_from_homepage) {
		if ( $query->is_search == false && !is_admin() && isset($post) && has_shortcode( $post->post_content, 'mdocs' )) {
			if(get_option('mdocs-hide-all-posts') == false && get_option('mdocs-hide-all-posts-non-members') == false) {
				$query->set( 'post_type', array( 'post', 'mdocs-posts' ) );
			} elseif(is_user_logged_in() && get_option('mdocs-hide-all-posts-non-members') == true) {
				$query->set( 'post_type', array( 'post', 'mdocs-posts' ) );
			}
		}
	} else {
		if ( is_home() && $query->is_main_query() ||  $query->is_search == false && !is_admin() && isset($post) && has_shortcode( $post->post_content, 'mdocs' )) {
			if(get_option('mdocs-hide-all-posts') == false && get_option('mdocs-hide-all-posts-non-members') == false) {
				$query->set( 'post_type', array( 'post','media', 'mdocs-posts' ) );
			} elseif(is_user_logged_in() && get_option('mdocs-hide-all-posts-non-members') == true) {
				$query->set( 'post_type', array( 'post','media', 'mdocs-posts' ) );
			}
		}
	}
	
}
// HIDES OR SHOWS MDOCS POST IN SEARCH
function mdocs_alter_searchfilters($query) {
	if($query->is_search && !is_admin() && $query->is_main_query() && post_type_exists('mdocs-posts')) {
		if(get_option('mdocs-hide-all-posts') || get_option('mdocs-hide-all-posts-non-members') && is_user_logged_in() == false) {
			global $wp_post_types;
			$wp_post_types['mdocs-posts']->exclude_from_search = true;
		}
	}
	return $query;
}
// SHOW HIDE MDOCS MEDIA FILES FROM MENU USING AJAX
function show_current_user_attachments( $query = array() ) {
	$q = new WP_Query(array(
		'post_type' => 'mdocs-posts',
		'posts_per_page' => -1,
	));
	$mdocs_posts = array();
	foreach($q->posts as $post) array_push($mdocs_posts, $post->ID);
	$query['post_parent__not_in'] = $mdocs_posts;
	return $query;
}
// SHOW HIDE MDOCS MEDIA FILES FROM MENU
function mdocs_hide_attachments( $where ){
    if( is_user_logged_in() ){
		if(strpos(basename($_SERVER['REQUEST_URI']), 'upload.php') !== false) {
			$where .= ' AND post_content!="[mdocs_media_attachment]"';
		}
    }
    return $where;
}
// ADD MORE ROBUST SEARCH FUNCTIONALITY
function mdocs_robust_search( $where ) {
	$allow_robust = false;
	if(is_user_logged_in()) $allow_robust = true;
	elseif(get_option('mdocs-hide-all-posts-non-members') == false && !is_user_logged_in()) $allow_robust = true;
	if($allow_robust && !is_admin()) {
		global $wp_query, $wpdb;
		if( isset( $wp_query->query_vars['s'] ) && $wp_query->query_vars['s'] != '') {
			$mdocs = get_option('mdocs-list');
			$search_value = trim($wp_query->query_vars['s']);
			$args = array();
			foreach($mdocs as $index => $the_mdoc) {
				if( !empty($mdocs) && strpos(strtolower($the_mdoc['filename']), strtolower($search_value)) !== false) array_push($args, $the_mdoc['parent']);
				if( !empty($mdocs) && strpos(strtolower($the_mdoc['name']), strtolower($search_value)) !== false) array_push($args, $the_mdoc['parent']);
				if( !empty($mdocs) && strpos(strtolower($the_mdoc['owner']), strtolower($search_value)) !== false) array_push($args, $the_mdoc['parent']);
				if( !empty($mdocs) && strpos(strtolower($the_mdoc['version']), strtolower($search_value)) !== false) array_push($args, $the_mdoc['parent']);
				if( !empty($mdocs) && strpos(strtolower($the_mdoc['desc']), strtolower($search_value)) !== false) array_push($args, $the_mdoc['parent']);
			}
			$args = implode(',',$args);
			//if(isset($args) && $args != '') $where .= ' OR (  wp_posts.ID in (' . $args . ')  AND wp_posts.post_status = "publish" )';
			if(isset($args) && $args != '') $where .= ' OR ( '.$wpdb->prefix.'posts.ID in (' . $args . ') AND '.$wpdb->prefix.'posts.post_status = "publish" )';
		}
		
	}
	return $where;
}
function mdocs_save_list($mdocs_list, $is_empty=false) {
	if($mdocs_list != null || is_array($mdocs_list)) {
		update_option('mdocs-list', $mdocs_list, 'no');
	}
}
function mdocs_nav_size($collapse) {
	if($collapse) {
?>
<style type="text/css" media="screen" id="mdocs-nav-collapse">
	@media (max-width: 10000px) {
		.navbar-header { float: none; }
		.navbar-toggle { display: block; }
		.navbar-collapse { border-top: 1px solid transparent; box-shadow: inset 0 1px 0 rgba(255,255,255,0.1); }
		.navbar-collapse.collapse { display: none!important; }
		.navbar-nav { float: none !important; margin: 7.5px -15px; }
		.navbar-nav>li { float: none; }
		.navbar-nav>li>a { padding-top: 10px; padding-bottom: 10px; }
		.navbar-collapse.collapse.in { display: block!important; }
		.collapsing { overflow: hidden!important; }
		#mdocs-navbar .navbar-collapse ul li { margin: 0; }
	}
</style>
<?php
	} else {
?>
<style type="text/css" media="screen" id="mdocs-nav-expand">
	#mdocs-navbar .navbar-toggle { display: none !important; }
	#mdocs-navbar .navbar-header { float: left; margin: 0;  }
	#mdocs-navbar .navbar-header .navbar-brand  { margin: 0; } 
	#mdocs-navbar .navbar-collapse { display: block; margin: 0px; border: none; }
	#mdocs-navbar .navbar-collapse ul, #mdocs-navbar .navbar-collapse ul li { float: left; height: 50px;}
	#mdocs-navbar .navbar-collapse ul li a { padding: 15px; }
</style>
<?php
	}
}
function mdocs_box_view_update_v3_0() {
	?>
	<style>
		body, html { overflow: hidden; }
		.bg-3-0 { width: 100%; height: 100%; background: #000; position: absolute; top: 0; left: 0; z-index: 9999; padding: 0; margin: 0;  opacity:  0.7;}
		.container-3-0 { position: absolute; top: 50px; z-index: 10000; width: 500px; background: #fff; margin-left: 50%; left: -250px; padding: 10px;}
		.container-3-0 h1 { color: #2ea2cc; }
		.container-3-0 h3 { color: red; }
		.btn-container-3-0 { text-align: center; }
		@media (max-width: 640px) {
			.container-3-0 { width: 360px; left: -180px; z-index: 10000; margin-left: 50%}
		}
	</style>
	<div class="bg-3-0"></div>
	<div class="container-3-0">
		<h1><?php _e('Memphis Documents Library', 'memphis-documents-library'); ?></h1>
		<h2><?php _e('Document Preview Updater', 'memphis-documents-library'); ?></h2>
		<p><?php _e('Version 3.0 of Memphis Documents Library now uses a new documents preview tool Called', 'memphis-documents-library'); ?> <a href="https://box-view.readme.io" target="_blank"><?php _e('Box View', 'memphis-documents-library'); ?></a>.</p>
		<p><?php _e('This process requires an update to your Memphis Documents Library, which will be adding information needed for', 'memphis-documents-library'); ?> <a href="https://box-view.readme.io" target="_blank"><?php _e('Box View', 'memphis-documents-library'); ?></a> <?php _e('to work properly.', 'memphis-documents-library'); ?></p>
		<h3><?php _e('Important, Please Read', 'memphis-documents-library'); ?></h3>
		<p><em><?php _e('The process depending on the size of your Library can take a long time, so make sure you have the time run this updater.', 'memphis-documents-library'); ?></em></p>
		<p><em><?php _e('If you choose not to run this updater now preview will not work.', 'memphis-documents-library'); ?></em></p>
		<p><em><?php _e('You may run this process anytime by going to the Settings menu and pressing "Run Preview and Thumbnail Updater".', 'memphis-documents-library'); ?></em></p>
		<h3><?php _e('DO NOT LEAVE PAGE ONCE THIS UPDATER HAS STARTER!', 'memphis-documents-library'); ?></h3>
		<div class="btn-container-3-0">
			<button id="run-updater-3-0"><?php _e('Run Updater', 'memphis-documents-library'); ?></button>
			<button id="not-now-3-0"><?php _e('Not Right Now', 'memphis-documents-library'); ?></button>
		</div>
	</div>
	
	<?php
}
function mdocs_search_users($user_search_string, $owner, $contributors) {
	//if(!is_array($owner)) $owner = array();
	if(!is_array($contributors)) $contributors = array();
	$wp_roles = get_editable_roles();
	$found_roles = array();
	foreach($wp_roles as $index => $role) {
		if(substr( $index, 0, strlen($user_search_string) ) === strtolower($user_search_string)) $found_roles[$index] = $role['name'];
	}
	$users_filter_search = get_users( array( 'search' => $user_search_string.'*' ) );
	$found_users = array();
	foreach($users_filter_search as $index => $user) $found_users[$user->user_login] = $user->display_name;
	if(count($found_roles) > 0) {
		echo '<em>Roles</em><br>';
		echo '<div class="list-group">';
	} 
	foreach($found_roles as $index => $role) {
		if(!in_array($index, $contributors)) {
			echo '<a href="#" class="list-group-item list-group-item-warning mdocs-search-results-roles" data-value="'.$index.'">'.$role.'</a><br>';
		}
	}
	if(count($found_roles) > 0) echo '</div>';
	if(count($found_users) > 0) {
		echo '<em>Users</em><br>';
		echo '<div class="list-group">';
	}
	foreach($found_users as $index => $user) {
		if($owner != $index && !in_array($index, $contributors)) {
			echo '<a href="#" class="list-group-item list-group-item-warning mdocs-search-results-users" data-value="'.$index.'" >'.$index.' - ('. $user.')</a><br>';
		}
	}
	if(count($found_users) > 0) echo '</div>';
}

function mdocs_file_upload_max_size() {
  static $max_size = -1;

  if ($max_size < 0) {
    // Start with post_max_size.
    $max_size = mdocs_parse_size(ini_get('post_max_size'));

    // If upload_max_size is less, then reduce. Except if upload_max_size is
    // zero, which indicates no limit.
    $upload_max = mdocs_parse_size(ini_get('upload_max_filesize'));
    if ($upload_max > 0 && $upload_max < $max_size) {
      $max_size = $upload_max;
    }
  }
  return $max_size;
}

function mdocs_parse_size($size) {
  $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
  $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
  if ($unit) {
    // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
    return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
  }
  else {
    return round($size);
  }
}

function mdocs_convert_bytes($bytes) {
	$file_size_type = __('bytes','memphis-documents-library');
	if($bytes > 1000 && $bytes < 1000000) {
		$file_size_type = __('KB','memphis-documents-library');
		$bytes = round($bytes/1024,1);
	} elseif($bytes > 1000000) {
		$file_size_type = __('MB','memphis-documents-library');
		$bytes =  round($bytes/1048576,1);
	} elseif ($bytes > 1000000000) {
		$file_size_type = __('GB','memphis-documents-library');
		$bytes =  round($bytes/1073741824,1);
	} elseif ($bytes > 1000000000000) {
		$file_size_type = __('TB','memphis-documents-library');
		$bytes =  round($bytes/1099511627776,1);
	}
	return $bytes.' '.$file_size_type;
}
// CONVERT STRING BOOLEAN TO BOOLEAN
function mdocs_convert_to_boolean($val) {
	if($val == 'false'|| $val == false || $val == '' || $val = '0') return false;
	else return true;
}

function mdocs_stop_form_resubmit() {
?>
<script>
	if ( window.history.replaceState ) {
	  window.history.replaceState( null, null, window.location.href );
	}
</script>
	<?php
}
function mdocs_hide_dashboard_admin_menu() {
	if(MDOCS_DEV == true) {
	?>
	<style>
	#adminmenuback, .edit-post-header { display: none !important; }
	</style>
	<?php
	}
}
// REMOVES ALL MDOCS POSTS AND ATTACHMENTS
function mdocs_remove_posts_and_attachments() {
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
