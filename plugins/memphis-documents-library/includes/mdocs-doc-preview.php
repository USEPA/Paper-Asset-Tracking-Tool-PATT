<?php
function mdocs_load_preview() {
	if(isset($_REQUEST['type']) && isset($_REQUEST['mdocs-file'])) {
		$the_mdoc = get_the_mdoc_by( $_REQUEST['mdocs-file'], 'id');
		switch($_REQUEST['show_type']) {
			case 'desc':
				mdocs_show_description($the_mdoc['id']);
				break;
			case 'preview':
				mdocs_show_preview($the_mdoc);
				break;
			case 'versions':
				mdocs_show_versions($the_mdoc['parent']);
				break;
		}
	} else echo 'An error has occured, trying to display this preview please contact the plugin author for assistance.';
}
// PREVIEW
function mdocs_show_preview($the_mdoc) {
	$upload_dir = wp_upload_dir();
	if(mdocs_check_file_rights($the_mdoc)) {
		$is_image = @getimagesize($upload_dir['basedir'].MDOCS_DIR.$the_mdoc['filename']);
	   ?>
		<div class="mdoc-desc">
			<?php
			
			if($is_image == false) mdocs_load_preview_iframe($the_mdoc['id']);
			else mdocs_load_image_iframe($the_mdoc);
			?>
	   </div>
	   <?php
	} else {
		?>
	   <br><div class="alert alert-info text-center" role="alert"><?php _e('Preview is not available for this file.','memphis-documents-library'); ?></div class="alert alert-warning" role="alert">
	   <?php
	}
}
// PREVIEW - DOCUMENT
function mdocs_load_preview_iframe($file) {
	$the_mdoc = get_the_mdoc_by(basename(mdocs_sanitize_string($file)), 'id');
	if(get_option('mdocs-preview-type') == 'google'  || get_option('mdocs-preview-type') == '') {
		$nonce = wp_create_nonce( 'mdocs-preview-'.$file );
		$link = site_url().'/?mdocs-preview='.$file.'&_mdocs-preview='.$nonce;
		?>
		<iframe id="mdocs-box-view-iframe" src="<?php echo $link; ?>" style="border: none; width: 100%;" seamless fullscreen></iframe>
		<script> 
			var screenHeight = window.innerHeight-250;
			jQuery('#mdocs-box-view-iframe').css({'height': screenHeight});
		</script>
		<?php
	} elseif(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '') {
		$boxview = new mdocs_box_view();
		$view_file = $boxview->downloadFile($the_mdoc);
		if(isset($view_file) && $view_file['type'] != 'error') {
			if(get_option('mdocs-box-show-print-and-download')) $show_print_and_download = '?showDownload=true';
			else $show_print_and_download = '';
			?>
			<iframe id="mdocs-box-view-iframe" src="<?php echo $view_file['expiring_embed_link']['url'].$show_print_and_download; ?>" seamless fullscreen style="width: 100%; " allowfullscreen="true"></iframe>
			<script>
				var screenHeight = window.innerHeight-275;
				jQuery('#mdocs-box-view-iframe').css({'height': screenHeight})
			</script>
		<?php } else { ?>
		<div class="alert alert-warning" role="alert">
			<p><?php echo ucfirst($view_file['type']).' '. __('Status','memphis-documents-library').': '.$view_file['status'].' ( '.$view_file['message'].' ) '; ?></p>
		</div>
		<?php
		}
	} else _e('No preview type has been selected','memphis-documents-library');
}
// DOCUMENT PREVIEW CONTET
function mdocs_show_preview_iframe_content($file_id) {
    global $wpdb;
	$upload_dir = wp_upload_dir();
	$the_mdoc = get_the_mdoc_by(basename(mdocs_sanitize_string($file_id)), 'id');
		$get_post_id = wp_get_post_parent_id( $the_mdoc['id'] );
		$get_s3_link = $wpdb->get_row("SELECT object_location FROM wpqa_wpsc_epa_folderdocinfo_files WHERE post_id = '" . $get_post_id . "'");
        $file = $get_s3_link->object_location;
	//$filename = $the_mdoc['filename'];
	//global $wpdb;
	//$file = $upload_dir['basedir'].'/mdocs/'.$filename;
	global $mdocs_preview_file_types;
	if(in_array($the_mdoc['type'], $mdocs_preview_file_types['PDF'])) {
		ob_start();
		header("Content-type: application/pdf");
		header("Content-Disposition: inline; filename=".$filename);
		ob_clean();
		flush();
		@readfile($file);
		exit();
	} elseif(in_array($the_mdoc['type'], $mdocs_preview_file_types['ZIP'])) {
		?>
		<style type="text/css">
			#outer { width: 100%; margin: 0 auto; font-family: sans-serif;  -webkit-font-smoothing: antialiased; text-shadow: rgba(0,0,0,.01) 0 0 1px;}
			#inner { width: 90%; margin: 20px auto; border: solid 1px #888;}
			h2 { background: #337ab7; color: #fff;  margin: 0; padding: 5px; font-weight: normal;}
			h3 { margin: 0; padding: 5px; font-weight: normal;}
			table { width:  100%; }
			ul { margin: 0; }
			li { margin: 5px 20px;  padding: 0;}
			.odd { background:  #dee1e1; }
		</style>
		<div id="outer">
			<div id="inner">
				<h2><?php _e('Zip File Content','memphis-documents-library'); ?></h2>
				<table>
					<?php	
					$zip = zip_open($file);
					if ($zip) {
						$odd = false;
						while ($zip_entry = zip_read($zip)) {
							$entry = trim(zip_entry_name($zip_entry));
							if(strpos($entry, '__MACOSX') === false && strpos($entry, '.DS_Store') === false) {
								if (zip_entry_open($zip, $zip_entry)) {
									$contents = zip_entry_name($zip_entry);
									?>
									<tr class="<?php if($odd == false) { echo 'even'; } else { echo 'odd'; } ?>">
										<td  ><?php echo $contents; ?></td>
									</tr>
									<?php
									if($odd == false) $odd = true;
									else $odd = false;
									zip_entry_close($zip_entry);
								}
							}
						}
						zip_close($zip);
						?>
				</table>
			</div>
		</div>
		<?php
		}
	} else {
		?>
		<style type="text/css">
			#outer { width: 100%; margin: 0 auto; font-family: sans-serif;  -webkit-font-smoothing: antialiased; text-shadow: rgba(0,0,0,.01) 0 0 1px;}
			#inner { width: 90%; margin: 20px auto; }
			p { background: #fcf8e3; border: solid 1px #faebcc;  color: #8a6d3b;  margin: 0; padding: 5px; font-weight: normal; text-align: center; border-radius: 10px;}
			h3 { margin: 0; padding: 5px; font-weight: normal;}
			ul { margin: 0; }
			li { margin: 5px 20px;  padding: 0;}
		</style>
		<div id="outer">
			<div id="inner">
				<p><?php _e('Sorry the file type you are trying to preview is unsupported.','memphis-documents-library'); ?></p>
				<!--
				<h3><?php _e('Below is a list of currently supported file types', 'memphis-documents-library'); ?>:</h3>
				<ul>
					<?php
					foreach($mdocs_preview_file_types as $index => $type) {
						if(is_array($type)) {
							echo '<li>'.$index.'</li>';
							echo '<ul>';
							foreach($type as $file_type) {
								echo '<li>'.$file_type.'</li>';
							}
							echo '</ul>';
						}
					}
					?>
				</ul>
				-->
			</div>
		</div>
		<?php
	}
	exit();
}
// PREVIEW - IMAGE
function mdocs_load_image_iframe($the_mdoc) {
	?>
	<div style="text-align: center;">
		<img class="img-thumbnail mdocs-img-preview img-responsive" src="<?php echo site_url(); ?>/?mdocs-img-preview=<?php echo $the_mdoc['filename']; ?>" />
	</div>
	<?php
}
// DESCRIPTION
function mdocs_show_description($id) {
	$the_mdoc = get_the_mdoc_by(basename(mdocs_sanitize_string($id)), 'id');
	if(mdocs_check_file_rights($the_mdoc)) {
		$mdocs = get_option('mdocs-list');
		$the_mdoc = get_the_mdoc_by($id, 'id');
		$mdocs_desc = apply_filters('the_content', $the_mdoc['desc']);
		$mdocs_desc = str_replace('\\','',$mdocs_desc);
		$the_image_file = preg_replace('/ /', '%20', $the_mdoc['filename']);
		//$image_size = @getimagesize(get_site_url().'/?mdocs-img-preview='.$the_image_file);
		$image_size = false;
		if(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '' && strtolower($the_mdoc['type']) != 'zip' && strtolower($the_mdoc['type']) != 'rar' ) {
			$boxview = new mdocs_box_view();
			$thumbnail = true;
		} else {
			$thumbnail = false;
			
		}
		?>
		<div class="mdoc-desc">
		<?php
		if($thumbnail) {
			if(function_exists('imagecreatefromjpeg')) {
				?>
				<img class="mdocs-thumbnail img-thumbnail img-responsive" src="<?php $boxview->getThumbnail($the_mdoc['box-view-id'], $the_mdoc); ?>" alt="<?php echo $the_mdoc['filename']; ?>" />
				<?php
			}
		} elseif($the_mdoc['type'] == 'pdf' && class_exists('imagick')) {
			$the_image_file = preg_replace('/ /', '%20', $the_mdoc['filename']);
			$image_size = @getimagesize(get_site_url().'/?mdocs-img-preview='.$the_image_file);
			$thumbnail_size = 256;
			$upload_dir = wp_upload_dir();
			$file = $upload_dir['basedir']."/mdocs/".$the_mdoc['filename'].'[0]';
			$thumbnail = new Imagick($file);
			$thumbnail->setbackgroundcolor('rgb(64, 64, 64)');
			$thumbnail->thumbnailImage(450, 300, true);
			$thumbnail->setImageFormat('png');
			$uri = "data:image/png;base64," . base64_encode($thumbnail);
			?>
			<img class="mdocs-thumbnail img-thumbnail  img-responsive" src="<?php echo $uri; ?>" alt="<?php echo $the_mdoc['filename']; ?>" />
			<?php
		} elseif( $image_size != false) {
			$thumbnail_size = 256;
			$width = $image_size[0];
			$height = $image_size[1];
			$aspect_ratio = round($width/$height,2);
			// Width is greater than height and width is greater than thumbnail size
			if($aspect_ratio > 1&&  $width > $thumbnail_size) {
				$thumbnail_width = $thumbnail_size;
				$thumbnail_height = $thumbnail_size/$aspect_ratio;
			// Heigth is greater than width and height is greater then thumbnail size
			} elseif($aspect_ratio < 1 && $height > $thumbnail_size) {
				$aspect_ratio = round($height/$width,2);
				$thumbnail_width = $thumbnail_size/$aspect_ratio;
				$thumbnail_height = $thumbnail_size;
			// Heigth is greater than width and height is less then thumbnail size
			} elseif($aspect_ratio < 1 && $height < $thumbnail_size) {
				$aspect_ratio = round($height/$width,2);
				$thumbnail_width = $thumbnail_size/$aspect_ratio;
				$thumbnail_height = $thumbnail_size;
			// Width and height are equal
			} elseif($aspect_ratio == 1 ) {
				$thumbnail_width = $thumbnail_size;
				$thumbnail_height = $thumbnail_size;
			// Width is greater than height and width is less than thumbnail size
			} elseif($aspect_ratio > 1 && $width < $thumbnail_size) {
				$thumbnail_width = $thumbnail_size;
				$thumbnail_height = $thumbnail_size/$aspect_ratio;
			// Hieght is greater than width and height is less than thumbnail size
			} elseif($aspect_ratio > 1 && $height < $thumbnail_size) {
				$thumbnail_width = $thumbnail_size/$aspect_ratio;
				$thumbnail_height = $thumbnail_size;
			} else {
				$thumbnail_width = $thumbnail_size;
				$thumbnail_height = $thumbnail_size;
			}
			if(function_exists('imagecreatefromjpeg')) {
				ob_start();
				$upload_dir = wp_upload_dir();
				$src_image = $upload_dir['basedir'].MDOCS_DIR.$the_mdoc['filename'];
				if($image_size['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($src_image);
				elseif($image_size['mime'] == 'image/png') $image = imagecreatefrompng($src_image);
				elseif($image_size['mime'] == 'image/gif') $image = imagecreatefromgif($src_image);
				$thumnail =imagecreatetruecolor($thumbnail_width,$thumbnail_height);
				$white = imagecolorallocate($thumnail, 255, 255, 255);
				imagefill($thumnail, 0, 0, $white);
				imagecopyresampled($thumnail,$image,0,0,0,0,$thumbnail_width,$thumbnail_height,$image_size[0],$image_size[1]);
				imagepng($thumnail);
				imagedestroy($image);
				imagedestroy($thumnail);
				$png = ob_get_clean();
				$uri = "data:image/png;base64," . base64_encode($png);
				?>
				<img class="mdocs-thumbnail img-thumbnail  img-responsive" src="<?php echo $uri; ?>" alt="<?php echo $the_mdoc['filename']; ?>" />
				<?php
			}
		}
		echo $mdocs_desc; ?>
		</div>
		<div class="clearfix"></div>
		<?php
	} else {
		?>
	   <br><div class="alert alert-info text-center" role="alert"><?php _e('Description is not available for this file.','memphis-documents-library'); ?></div class="alert alert-warning" role="alert">
	   <?php
	}
}
// VERSIONS
function mdocs_show_versions($id=null) {
	if($id == null && isset($_POST['mdocs-id'])) $the_mdoc = get_the_mdoc_by($_POST['mdocs-id'], 'id');
	else $the_mdoc = get_the_mdoc_by($id, 'parent');
	if(mdocs_check_file_rights($the_mdoc)) {
		$date_format = get_option('mdocs-date-format');
		$current_date = mdocs_format_unix_epoch($the_mdoc['modified'], true);
		$upload_dir = wp_upload_dir();
		$archive_download = false;
		$download_link = '';
		$the_mdoc_permalink = @htmlspecialchars(get_permalink((int)$the_mdoc['parent']));
		if(mdocs_check_file_rights($the_mdoc) == false && is_user_logged_in()) {
			$download_link = '<span class="text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.__('Access Denied','memphis-documents-library').'</span>';
			$archive_download = false;
		}
		elseif($the_mdoc['non_members']   == '' && is_user_logged_in() == false || is_user_logged_in() == false && get_option('mdocs-hide-all-files-non-members')) {
			$download_link = '<a href="'.wp_login_url($the_mdoc_permalink).'"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.__('Please Login','memphis-documents-library').'</a>';
			$archive_download = false;
			
		} elseif($the_mdoc['non_members'] == 'on' || is_user_logged_in() ) {
			$download_link = '<a href="'.site_url().'/?mdocs-file='.$the_mdoc['id'].'"><i class="fas fa-cloud-download-alt" aria-hidden="true"></i> '.__('Download','memphis-documents-library').'</a>';
			$archive_download = true;	
		} else {
			$download_link = '<span class="text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.__('Access Denied','memphis-documents-library').'</span>';
			$archive_download = false;
		}
		?>
		<table class="table table-hover table-condensed">
			<tr>
				<th><?php _e('File', 'memphis-documents-library'); ?></th>
				<th><?php _e('Version', 'memphis-documents-library'); ?></th>
				<th><?php _e('Date Modified', 'memphis-documents-library'); ?></th>
				<th><?php _e('Download', 'memphis-documents-library'); ?></th>
				<th><?php _e('Current', 'memphis-documents-library'); ?></th>
			</tr>
			<tr>
				
				<?php
				if(get_option('mdocs-hide-name')) $name_string = $the_mdoc['filename'].'<br>';
				elseif(get_option('mdocs-hide-filename')) $name_string = str_replace('\\','',$the_mdoc['name']).'<br>';
				else $name_string = str_replace('\\','',$the_mdoc['name']).' - <small class="text-muted">'.$the_mdoc['filename'].'</small><br>';
				?>
				
				
				<td class="mdocs-orange"><?php echo $name_string; ?></td>
				<td class="mdocs-blue"><?php echo $the_mdoc['version']; ?></td>
				<td class="mdocs-red"><?php echo $current_date['formated-date']; ?></td>
				<?php
				if($archive_download) { ?>
				<td><?php echo $download_link; ?></td>
				<?php
				} else { ?>
				<td><?php echo $download_link; ?></td>
				<?php } ?>
				
				<td><input type="radio" checked name="mdocs-current-version"></td>
			<?php
			foreach(array_reverse($the_mdoc['archived']) as $index => $archive) {
				$file = stripslashes(substr($archive, 0, strrpos($archive, '-')));
				$version = substr(strrchr($archive, '-'), 2 );
				if(file_exists($upload_dir['basedir'].'/mdocs/'.strtolower($archive))) $archive = strtolower($archive);
				if(file_exists($upload_dir['basedir'].'/mdocs/'.$archive)) {
					$archive_date = date($date_format, filemtime($upload_dir['basedir'].'/mdocs/'.$archive));
					?>
					<tr>
						<td class="mdocs-orange"><?php
						if(get_option('mdocs-hide-name')) $name_string = $file;
						elseif(get_option('mdocs-hide-filename')) $the_mdoc['name'];
						else$name_string = $the_mdoc['name']. ' -  <small class="text-muted"><i>'.$file.'</i></small>';
						echo $name_string;
						?>
							
						</td>
						<td class="mdocs-blue"><?php echo $version; ?></td>
						<td class="mdocs-red"><?php echo $archive_date; ?></td>
						<?php if($archive_download) { ?>
						<td><a href="<?php echo site_url().'/?mdocs-version='.$archive.'&mdocs-file='.$the_mdoc['id'].'&mdocs-url=false'; ?>"><i class="fas fa-cloud-download-alt" aria-hidden="true"></i> <?php _e('Download','memphis-documents-library'); ?></a></td>
						<?php
						} else { ?>
						<td><?php echo $download_link; ?></a></td>
						<?php } ?>
						<td><input type="radio" name="mdocs-previous-version" disabled></td>
					</tr>
					<?php
				} 
			}?>
			</tr>
		</table>
		<?php
	} else {
		?>
	   <br><div class="alert alert-info text-center" role="alert"><?php _e('Versions are not available for this file.','memphis-documents-library'); ?></div class="alert alert-warning" role="alert">
	   <?php
	}
}