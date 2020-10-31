<?php
function mdocs_display_file_info($the_mdoc, $index=0, $current_cat, $sortbar_data=null) {
/*
	// D E B U G - PATT start
	echo '<pre>';
	print_r($the_mdoc);
	echo '</pre>';
	echo 'size: ' . $the_mdoc['size'] . '<br>';
	echo 'true: ' . ( $the_mdoc['size'] == 0 );
	// PATT end
*/
	$the_mdoc_permalink = mdocs_get_permalink($the_mdoc['parent'], true);
	$the_post = get_post($the_mdoc['parent']);
	if($the_post != null) $is_new = preg_match('/new=true/',$the_post->post_content);
	else $is_new = false;
	$mdocs_show_new_banners = get_option('mdocs-show-new-banners');
	$mdocs_time_to_display_banners = get_option('mdocs-time-to-display-banners');
	$new_or_updated = '';
	
	$the_date = mdocs_format_unix_epoch($the_mdoc['modified']);
	if($the_date['gmdate'] > time()) $scheduled = '<small class="text-danger"><em> - '.__('Scheduled').'</em></small>';
	else $scheduled = '';
	
	
	$modified = floor($the_mdoc['modified']/86400)*86400;
	$today = floor(time()/86400)*86400;
	$days = (($today-$modified)/86400);
	if($mdocs_time_to_display_banners > $days) {
		if($is_new == true) {
			if($mdocs_show_new_banners) $status_class = 'mdocs-success';
			else $status_class = 'mdocs-normal';
			if(get_option('mdocs-hide-new-update-label')) $new_or_updated = '';
			else $new_or_updated = '<small class="label label-success">'.__('New', 'memphis-documents-library').'</small>';
		} else {
			if($mdocs_show_new_banners) $status_class = 'mdocs-info';
			else $status_class = 'mdocs-normal';
			if(get_option('mdocs-hide-new-update-label')) $new_or_updated = '';
			else $new_or_updated = '<small class="label label-info">'.__('Updated', 'memphis-documents-library').'</small> ';
		}
	} else  $status_class = 'mdocs-normal';
	
	if($sortbar_data['is-dashboard']) {
		if($the_mdoc['file_status'] == 'hidden' || get_option('mdocs-hide-all-files')) $file_status = '<i class="fa fa-eye-slash" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="'.__('File is Hidden', 'memphis-documents-library').'"></i>';
		else $file_status = '';
		if($the_mdoc['post_status'] != 'publish') $post_status = '&nbsp<i class="fa fa-lock" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="'.__('Post is ', 'memphis-documents-library').ucfirst($the_mdoc['post_status']).'"></i>';
		elseif(get_option('mdocs-hide-all-posts')) {
			$post_status = '&nbsp<i class="fa fa-lock" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="'.__('All Post are Hidden ', 'memphis-documents-library').'"></i>';
		} else $post_status = '';
	} else {
		$file_status = '';
		$post_status = '';
	}
	?>
		<tr class="<?php echo $status_class; ?>">
			<?php
			$title_colspan = 0;
			if($sortbar_data['is-dashboard']) {
				if(mdocs_check_file_rights($the_mdoc)) {
				    if($_GET['page'] == 'memphis-documents.php') {
					?>
					<td class="mdocs-file-checkbox"><input type="checkbox" name="mdocs-batch-checkbox" data-id="<?php echo $the_mdoc['id']; ?>"/></td>
					<?php
				    }
				} else $title_colspan = 2;
				$dropdown_class = 'mdocs-dropdown-menu';
			} else $dropdown_class = 'mdocs-dropdown-menu';
			if(get_option('mdocs-dropdown-toggle-fix')  && !$sortbar_data['is-dashboard'] ) $data_toogle = '';
			else $data_toogle = 'dropdown';
			foreach(mdocs_sort_file_info() as $key => $option) {
				if(isset($option['show']) && $option['show']) {
					$the_function = $option['function'];
					if($option['slug'] == 'name') {
						?>
						<td id="" class="mdocs-name" colspan="<?php echo $title_colspan; ?>">
								<div class="mdocs-btn-group btn-group">
									<?php
									if(get_option('mdocs-hide-name')) $name_string = $new_or_updated.$file_status.$post_status.mdocs_get_file_type_icon($the_mdoc).' '.$the_mdoc['filename'].''.$scheduled;
									elseif(get_option('mdocs-hide-filename')) $name_string = $new_or_updated.$file_status.$post_status.mdocs_get_file_type_icon($the_mdoc).' '.str_replace('\\','',$the_mdoc['name']).''.$scheduled;
									
// PATT start									
									else { 
										$name_string = $new_or_updated.$file_status.$post_status.mdocs_get_file_type_icon($the_mdoc).' '.str_replace('\\','',$the_mdoc['name']).' - <small class="text-muted">'.$the_mdoc['filename'].'</small>'.$scheduled; 
										
										$data = [
											'parent' => $the_mdoc['parent'],
											'size' => $the_mdoc['size'],
											'new_or_updated' => $new_or_updated,
											'file_status' => $file_status,
											'post_status' => $post_status,
											'file_type_icon' => mdocs_get_file_type_icon($the_mdoc),
											'display_name' => str_replace('\\','',$the_mdoc['name']),
											'file_name' => $the_mdoc['filename'],
											'scheduled' => $scheduled
										];
										
										$name_string = apply_filters( 'wppatt_mld_get_file_display', $data );
									}
// PATT end									
									?>
									<a class="mdocs-title-href" data-mdocs-id="<?php echo $index; ?>" data-toggle="<?php echo $data_toogle; ?>" href="#" data-folder="<?php echo $sortbar_data['current-folder']; ?>" data-sort="<?php echo $sortbar_data['orderby']; ?>" data-sort-type="<?php echo $sortbar_data['sort-type']; ?>" data-current-sort="<?php echo $sortbar_data['orderby']; ?>" data-current-sort-type="<?php echo $sortbar_data['sort-type']; ?>" data-is-dashboard="<?php echo $sortbar_data['is-dashboard']; ?>" data-hide-folder="<?php echo $sortbar_data['hide-folder']; ?>" data-hide-main-folder="<?php echo $sortbar_data['hide-main-folder']; ?>"><?php echo $name_string; ?></a>
									
									<ul class="<?php echo $dropdown_class; ?>" role="menu" aria-labelledby="dropdownMenu1">
										<li role="presentation" class="dropdown-header"><i class="fa fa-medium" aria-hidden="true"></i> &#187; <?php echo $the_mdoc['filename']; ?></li>
										<li role="presentation" class="divider"></li>
										<li role="presentation" class="dropdown-header"><?php _e('File Options', 'memphis-documents-library'); ?></li>
										<?php
											
// PATT start								
											$display_options_for_patt = true;
											if( $the_mdoc['size'] == 0) {
												$display_options_for_patt = false;
											}
											
											if( $display_options_for_patt ) {
												mdocs_download_rights($the_mdoc);
											}	
											mdocs_desciption_rights($the_mdoc);
											
											if( $display_options_for_patt ) {
												mdocs_preview_rights($the_mdoc);
											}
									
											mdocs_versions_rights($the_mdoc);
											mdocs_rating_rights($the_mdoc);
											mdocs_goto_post_rights($the_mdoc, $the_mdoc_permalink);
											mdocs_share_rights($the_mdoc, $the_mdoc_permalink);
											if($sortbar_data['is-dashboard']) { ?>
										<li role="presentation" class="divider"></li>
										<li role="presentation" class="dropdown-header"><?php _e('Admin Options'); ?></li>
										<?php
											mdocs_add_update_rights($the_mdoc, $current_cat);
											mdocs_manage_versions_rights($the_mdoc, $index, $current_cat);
											
											if( $display_options_for_patt ) {
												mdocs_delete_file_rights($the_mdoc, $index, $current_cat);
											}
// PATT end													
											if(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '') {
												mdocs_refresh_box_view($the_mdoc, $index);
											}
										?>
										<li role="presentation" class="divider"></li>
										<li role="presentation" class="dropdown-header"><i class="fa fa-laptop" aria-hidden="true"></i> <?php _e('File Status', 'memphis-documents-libaray'); echo ':'.' '.ucfirst($the_mdoc['file_status']); ?></li>
										<li role="presentation" class="dropdown-header"><i class="fa fa-bullhorn" aria-hidden="true"></i> <?php _e('Post Status', 'memphis-documents-libaray'); echo ':'.' '.ucfirst($the_mdoc['post_status']); ?></li>
										<?php } ?>
									  </ul>
								</div>
						</td>
						<?php
					} elseif($option['icon'] == 'mdocs-none' || $option['icon'] == '') {
						?>
						<td class="<?php echo 'mdocs-'.$option['slug']; ?>">
							<em style="<?php echo 'color: '.$option['color'].';'; ?>" ><?php if(function_exists($the_function)) $the_function($the_mdoc); else echo '"'.$the_function. '" function does not exist.'; ?></em>
						</td><?php
					} else {	?>
						<td class="<?php echo 'mdocs-'.$option['slug']; ?>">
							<i class="<?php echo $option['icon']; ?>" aria-hidden="true" title="<?php _e($option['text'], 'memphis-documents-library'); ?>"></i>
							<em style="<?php echo 'color: '.$option['color'].';'; ?>" ><?php if(function_exists($the_function)) $the_function($the_mdoc); else echo '"'.$the_function. '" function does not exist.'; ?></em>
						</td><?php
					}
				}
			}
			?>
		</tr>
<?php
}
?>