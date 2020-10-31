<?php
if(isset($_REQUEST['mdocs-file']) || isset($_REQUEST['mdocs-download-version']) || isset($_REQUEST['mdocs-export-file'])  || isset($_REQUEST['mdocs-preview'])) mdocs_download_file();
if(isset($_REQUEST['mdocs-img-preview']) ) { mdocs_preview_image(); }
function mdocs_preview_image() { add_action( 'plugins_loaded', 'mdocs_load_plugins_for_image_preview' ); }
function mdocs_download_file() { add_action( 'plugins_loaded', 'mdocs_load_plugins_for_download' ); }
function mdocs_load_plugins_for_download() {
	$mdocs = get_option('mdocs-list');
	$upload_dir = wp_upload_dir();
	// IS A PREVIEW
	if(isset($_REQUEST['show_type'])) {
		if($_REQUEST['show_type'] == 'preview' || $_REQUEST['show_type'] == 'versions' || $_REQUEST['show_type'] == 'desc') {
			mdocs_load_preview();
			exit();
		}
	}
	// IS BOXVIEW PREVIEW
	if(isset($_REQUEST['is-box-view'])) $is_box_view = true;
	else $is_box_view = false;
	// CHECK TYPE OF DOWNLOAD
	if(isset($_REQUEST['mdocs-export-file']) ) {
		$current_user = wp_get_current_user();
		if(current_user_can('mdocs_manage_settings')) $is_allowed = true;
		else $is_allowed = false;
		$filename = basename(mdocs_sanitize_string($_REQUEST['mdocs-export-file']));
		$file = sys_get_temp_dir().'/'.$filename;
		$the_mdoc = array();
		$the_mdoc['non_members'] = '';
	} elseif(isset($_REQUEST['mdocs-version']) ) {
		$the_mdoc = get_the_mdoc_by(basename(mdocs_sanitize_string($_REQUEST['mdocs-file'])), 'id');
		$is_allowed = mdocs_check_file_rights($the_mdoc);
		$file = $upload_dir['basedir'].'/mdocs/'.basename(mdocs_sanitize_string($_REQUEST['mdocs-version']));
		$filename = substr(basename(mdocs_sanitize_string($_REQUEST['mdocs-version'])), 0, strrpos(basename(mdocs_sanitize_string($_REQUEST['mdocs-version'])), '-'));
	} elseif(isset($_REQUEST['mdocs-preview'])) {
		if(wp_verify_nonce( $_REQUEST['_mdocs-preview'], 'mdocs-preview-'.$_REQUEST['mdocs-preview'])) {
			mdocs_show_preview_iframe_content($_REQUEST['mdocs-preview']);
		} else {
			$the_mdoc = get_the_mdoc_by(basename(mdocs_sanitize_string($_REQUEST['mdocs-preview'])), 'id');
			$is_allowed = false;
			$filename = $the_mdoc['filename'];
			$file = $upload_dir['basedir'].'/mdocs/'.$filename;
		}
	} elseif(isset($_REQUEST["mdocs-file"])) {
		$the_mdoc = get_the_mdoc_by(basename(mdocs_sanitize_string($_REQUEST['mdocs-file'])), 'id');
		$is_allowed = mdocs_check_file_rights($the_mdoc);
		$filename = $the_mdoc['filename'];
		$file = $upload_dir['basedir'].'/mdocs/'.$filename;
	} 
	// COUNT THE DOWNLOAD
	if($is_allowed && !isset($_REQUEST['mdocs-export-file']) && !isset($_REQUEST['mdocs-version']) && $is_box_view === false  ) {
		$mdocs[$the_mdoc['index']]['downloads'] = intval($mdocs[$the_mdoc['index']]['downloads'])+1;
		mdocs_save_list($mdocs);
	}
	$filetype = wp_check_filetype($file);
	if($is_allowed) {
		if (file_exists($file)  ) {
			ob_start();
			header('Content-Description: File Transfer');
			//header('Content-Encoding: UTF-8');
			//header('Content-Type: '.$filetype['type'].' charset=utf-8');
			header('Content-Type: '.$filetype['type']);
			if(get_locale() == 'ko_KR') header('Content-Disposition: attachment; filename='.iconv('UTF-8','euc-kr',$filename));
			else {
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				/* OPEN INLINE INSTEAD OF DOWNLOADING IT.
				//header('Content-Disposition: inline; filename="'.$filename.'"');
				*/
			}
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); 
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			//echo "\xEF\xBB\xBF";
			while (ob_get_level()) {
				ob_end_clean();
			}
			flush();
			readfile($file);
			if($_REQUEST['mdocs-export-donot-delete'] != 'on') unlink(sys_get_temp_dir().'/mdocs-export.zip');
			exit;
		} else if(!file_exists($file) && $is_box_view == true) die(__('Memphis Documents Error','memphis-documents-library').': '.basename($file).' '.__('was not found, no preview created for this file.', 'memphis-documents-library'));
		else {
			die(__('Memphis Documents Error','memphis-documents-library').': <em>'.basename($file).'</em> '.__('was not found, please contact the owner for assistance.', 'memphis-documents-library'));
		}
	} else {
		mdocs_denied_page($the_mdoc);
	}
	
}
// IMAGE PREVIEW
function mdocs_load_plugins_for_image_preview() {
	$image_file = sanitize_text_field($_REQUEST['mdocs-img-preview']);
	//$image_file = sanitize_file_name( $image_file );
	$upload_dir = wp_upload_dir();
	$image = $upload_dir['basedir'].MDOCS_DIR.basename($image_file);
	$the_mdoc = mdocs_get_file_by($image_file, 'filename');
	if(mdocs_check_file_rights($the_mdoc)) {
		if(is_array(getimagesize($image))) {
			$content = file_get_contents($image);
			header('Content-Type: image/jpeg');
			echo $content; exit();
		} else {
			//$the_mdoc = mdocs_get_file_by($image_file, 'filename');
			$icon = mdocs_get_file_type_icon($the_mdoc, true, false, true);
			$content = file_get_contents($icon);
			header('Content-Type: image/jpeg');
			echo $content; exit();
		}
	} else {
		mdocs_denied_page($the_mdoc);
	}
}
// EXPORT DOWNLOAD
function mdocs_download_export_file($file) {
	$file = sys_get_temp_dir()."/mdocs-export.zip";
	if (file_exists($file)) {
			ob_start();
			header('Content-Description: File Transfer');
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="mdocs-export.zip"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); 
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
			exit;
	} else mdocs_errors(__('Memphis Documents Error','memphis-documents-library').': '.basename($file).' '.__('was not found, file not exported.', 'memphis-documents-library'), 'error');
}

function mdocs_denied_page($the_mdoc) {
	if(isset($the_mdoc['id'])) {
		$direct_download = get_site_url().'/?mdocs-file='.$the_mdoc['id'];
		$page_redirect = @mdocs_get_permalink($the_mdoc['parent']);
	} else {
		$direct_download = '';
		$page_redirect = '';
	}
	?>
	<style>
		#outer { width: 100%; margin: 0 auto; font-family: sans-serif;  -webkit-font-smoothing: antialiased; text-shadow: rgba(0,0,0,.01) 0 0 1px;}
		#inner { width: 40%; margin: 200px auto; border: solid 1px #888;}
		h2 { background: #337ab7; color: #fff;  margin: 0; padding: 5px; font-weight: normal;}
		h3 { margin: 0; padding: 5px; font-weight: normal;}
		ul { margin: 0; }
		li { margin: 5px 20px;  padding: 0;}
	</style>
	<div id="outer">
		<div id="inner">
			<h2><?php _e('Sorry you are unauthorized to download this file.','memphis-documents-library'); ?></h1>
			<h3><?php _e('If you have an account with this site, you can try the following links to download the file', 'memphis-documents-library'); ?>:</h3>
			<?php if(isset($the_mdoc['id'])) { ?>
			<ul>
				<li>
					<a href="<?php echo wp_login_url($direct_download); ?>" title="<?php _e('Direct Download', 'memphis-documents-library'); ?>"><?php _e('Direct Download','memphis-documents-library'); ?>: </a>
					<?php _e('This link is a direct download of the file, enter you username and password and the download will start.','memphis-documents-library'); ?>
				</li>
				<li>
					<a href="<?php echo wp_login_url($page_redirect); ?>" title="<?php _e('Redirect to Page', 'memphis-documents-library'); ?>"><?php _e('Redirect to Page','memphis-documents-library'); ?>: </a>
					<?php _e('This link will redirect you to a download page, enter your username and password to be redirect to that page.','memphis-documents-library'); ?>
				</li>
			</ul>
			<?php } ?>
		</div>
	</div>
	<?php
	die();
}

?>