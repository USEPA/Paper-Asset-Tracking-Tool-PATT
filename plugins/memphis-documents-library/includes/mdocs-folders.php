<?php
function mdocs_edit_cats() {
	$mdocs_cats = get_option('mdocs-cats');
	mdocs_list_header();
	$check_index = 1;
	do {
		$found = mdocs_find_cat('mdocs-cat-'.$check_index);
		$empty_index = $check_index;
		$check_index++;
	} while ($found == true);
	update_option('mdocs-num-cats', $empty_index);
	?>
	<div class="mdocs-ds-container">/home/acy3/public_html/wordpress3/wp-content/plugins/memphis-documents-library/includes/mdocs-folders.php
		<h2><?php _e('Folder Editor', 'memphis-documents-library'); ?> <button class="btn btn-success" id="mdocs-add-cat" onclick="add_main_category('<?php echo intval(get_option('mdocs-num-cats')); ?>')"><?php _e('Add Main Folder','memphis-documents-library'); ?></button></h2>
		<form  id="mdocs-cats" method="post" action="admin.php?page=memphis-documents.php&mdocs-cat=cats" data-cat-index="<?php echo get_option('mdocs-num-cats'); ?>" data-check-index="1">
			<input type="hidden" value="mdocs-update-cats" name="action"/>
			<input type="hidden" name="mdocs-update-cat-index" value="0"/>
			<table class="wp-list-table widefat plugins">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-name" ><?php _e('Folder','memphis-documents-library'); ?></th>
						<th scope="col"  class="manage-column column-name" ><?php _e('Order','memphis-documents-library'); ?></th>
						<th scope="col"  class="manage-column column-name" ><?php _e('Remove','memphis-documents-library'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Add Folder','memphis-documents-library'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col" class="manage-column column-name" ><?php _e('Folder','memphis-documents-library'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Order','memphis-documents-library'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Remove','memphis-documents-library'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Add Folder','memphis-documents-library'); ?></th>
					</tr>
				</tfoot>
				<tbody id="the-list">
			<?php
			$index = 0;
			if(!empty($mdocs_cats)) {
				$mdocs_cats = array_values($mdocs_cats);
				mdocs_build_cat_td($mdocs_cats);
			} else {
				?>
				<tr>
					<td class="mdocs-nofiles" colspan="3">
						<p><?php _e('No folders created.','memphis-documents-library'); ?></p>
					</td>
				</tr>
			<?php 
			}
			?>
				</tbody>
			</table><br>
			<input type="submit" class="btn btn-primary" id="mdocs-import-submit" onclick="mdocs_reset_onleave()" value="<?php _e('Save Folders','memphis-documents-library') ?>" />
		</form>
	</div>
	<?php
	//if(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-cats') mdocs_update_cats();
}
function mdocs_build_cat_td($mdocs_cat,$parent_index=0) {
	global $mdocs_input_text_bg_colors;
	$padding = '';
	foreach($mdocs_cat as $index => $cat) {
		if($cat['slug'] != null) {
			$parent_id = 'class="mdocs-cats-tr"';
			if($cat['depth'] == 0) $parent_id = 'class="mdocs-cats-tr parent-cat"';
			elseif($cat['depth'] > 0) $padding = 'style="padding-left: '.(40*$cat['depth']).'px; "';
			$color_scheme = 'style="background: '.$mdocs_input_text_bg_colors[($cat['depth'])].'"';
			?>
			<tr <?php echo $parent_id; ?>>
				<td  id="name" <?php echo $padding; ?>>
					<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][index]" value="<?php echo $index; ?>"/>
					<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][parent_index]" value="<?php echo $parent_index; ?>"/>
					<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][num_children]" value="<?php echo count($cat['children']); ?>"/>
					<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][depth]" value="<?php echo $cat['depth']; ?>"/>
					<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][parent]" value="<?php echo $cat['parent']; ?>"/>
					<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][slug]" value="<?php echo $cat['slug']; ?>"/>
					<input <?php echo $color_scheme; ?> type="text" name="mdocs-cats[<?php echo $cat['slug']; ?>][name]"  value="<?php echo stripcslashes($cat['name']); ?>" />
				</td>
				<td id="order">
					<input <?php echo $color_scheme; ?> type="text" name="mdocs-cats[<?php echo $cat['slug']; ?>][order]"  value="<?php echo $index+1; ?>" <?php if($cat['parent'] != '') echo ''; ?> title="Sorry this functionality is disabled"/>
					
				</td>
				<td id="remove">
					<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][remove]" value="0"/>
					<?php if(count($cat['children']) == 0) { ?> 
					<input type="button" id="mdocs-cat-remove" name="<?php echo $cat['slug']; ?>" class="btn btn-primary" value="Remove"  />
					<?php } ?>
				</td>
				<td id="add-cat">
					<input  type="button" class="mdocs-add-sub-cat btn btn-primary" value="<?php _e('Add Folder', 'memphis-documents-library'); ?>" onclick="mdocs_add_sub_cat( '<?php echo intval(get_option('mdocs-num-cats')); ?>', '<?php echo $cat['slug']; ?>','<?php echo $cat['depth']; ?>', this);" />
				</td>
			</tr>
			<?php
			$child = array_values($cat['children']);
			if(count($child) > 0) mdocs_build_cat_td($child,$index);
		} else {
			//$cats = get_option('mdocs-cats');
			//unset($cats[$parent_index]['children'][$index]);
			//update_option('mdocs-cats', $cats);
		}
	}
}

function mdocs_update_cats() {
	if(intval(ini_get("max_input_vars")) > count($_POST, COUNT_RECURSIVE)) {
		$folder_creation = true;
		$mdocs_cats = array();
		$upload_dir = wp_upload_dir();
		if(isset($_POST['mdocs-cats'])) {
			$mdocs_cats_post = $_POST['mdocs-cats'];
// PATT start debug			
		echo '<pre>';
		print_r($mdocs_cats_post);
		echo '</pre>';	
		die();	
// PATT end debug			
			$parent_id = 0;
			$parent_ids = array();
			$depth = 0;
			$prev_depth = 0;
			foreach($mdocs_cats_post as $index => $cat) {
				if($cat['slug'] != null) {
					$cat['index'] = intval($cat['index']);
					$cat['parent_index'] = intval($cat['parent_index']);
					$cat['depth'] = intval($cat['depth']);
					//$cat['order'] = intval($cat['order']);
					$test = '';
					$curr_depth = intval($cat['depth']);
					$depth = intval($cat['depth']);
					if($cat['parent'] == '')  {
						$parent_ids = array();
						$base_parent_id = intval($cat['order'])-1;
						$mdocs_cats[$base_parent_id] = array('base_parent'=>'','index' => $cat['index'], 'parent_index'=>$cat['parent_index'], 'slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => '', 'children' => array(), 'depth' => 0);
						if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]);
					} else {
						$order = intval($cat['order'])-1;
						if($depth == 1) {
							$mdocs_cats[$base_parent_id]['children'][$order] = array('base_parent'=>$base_parent_id,'index' => $cat['index'], 'parent_index'=>$cat['parent_index'], 'slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' => 1);
							if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]['children'][$order]);
							$parent1_id = $order;
						} elseif($depth == 2) {
							$mdocs_cats[$base_parent_id]['children'][$parent1_id]['children'][$order] = array('base_parent'=>$base_parent_id,'index' => $cat['index'], 'parent_index'=>$cat['parent_index'],'slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' => 2);
							if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]['children'][$parent1_id]['children'][$order]);
							$parent2_id = $order;
						}
						/* Work in Progress
						} elseif($depth == 3) {
							$mdocs_cats[$base_parent_id]['children'][$index1]['children'][$cat['parent_index']]['children'][$cat['index']] = array('slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' =>3);
							if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]['children'][$index1]['children'][$cat['parent_index']]['children'][$cat['index']]);
							ksort($mdocs_cats[$base_parent_id]['children'][$index1]['children'][$cat['parent_index']]['children']);
						} elseif($depth == 4) {
							$mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1]['children'][$id_2]['children'][$id_3] = array('slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' => 4);
							ksort($mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1]['children'][$id_2]['children']);
						}
						*/
					}
					$parent_slug = $cat['slug'];
					if($cat['remove'] == 1) mdocs_cleanup_cats($cat);
				} else  $folder_creation = false;
			}
			if($folder_creation) {
				foreach($mdocs_cats as $index_1 => $cat1) {
					ksort($cat1['children']);
					$cat1 = array_values($cat1['children']);
					$mdocs_cats[$index_1]['children'] = $cat1;
					foreach($cat1 as $index_2 => $cat2) {
						ksort($cat2['children']);
						$cat2 = array_values($cat2['children']);
						$mdocs_cats[$index_1]['children'][$index_2]['children'] = $cat2;
					}
				}
				
				ksort($mdocs_cats);
				$mdocs_cats = array_values($mdocs_cats);
				update_option('mdocs-cats',$mdocs_cats, '' , 'no');
			} else mdocs_errors(MDOCS_ERROR_8, 'error');
		}
	} else {
		mdocs_errors('max_input_vars'.': '.ini_get("max_input_vars").' '.__('Input variables sent','memphis-documents-library').': '.count($_POST, COUNT_RECURSIVE), 'error');
		mdocs_errors(MDOCS_ERROR_9, 'error');
	}
}
function mdocs_get_parents($child, $data, $att=null, $tabs = array('parent-tab' => '', 'current-tab' => '&emsp;&emsp;', 'child-tab' => '&emsp;&emsp;&emsp;&emsp;')) {
	$parent = mdocs_get_the_folder($child);
	$emsp = '&emsp;&emsp;';
	if($parent['parent'] != '') {
		mdocs_get_parents($parent['parent'], $data);
		$tabs['current-tab'] = $tabs['current-tab'].$emsp;
		$tabs['child-tab'] = $tabs['child-tab'].$emsp;
		$tabs['parent-tab'] = $tabs['parent-tab'].$emsp;
	}
	$html = '
			<tr class="mdocs-parent-cat" >
				<td colspan="'.mdocs_get_num_cols().'" id="title" class="mdocs-tooltip">
					'.$tabs['parent-tab'].
					'<a class="mdocs-folders" data-folder="'.$parent['slug'].'" data-sort="'.$data['orderby'].'" data-sort-type="'.$data['sort-type'].'" data-current-sort="'.$data['orderby'].'" data-current-sort-type="'.$data['sort-type'].'" data-is-dashboard="'.$data['is-dashboard'].'" data-is-folder="true" data-hide-folder="'.$data['hide-folder'].'" data-hide-main-folder="'.$data['hide-main-folder'].'" ><i class="fas fa-folder" aria-hidden="true"></i> '.stripcslashes($parent['name']).'</a>
				</td>
			</tr>';
	echo $html;
	return $tabs;
}
function mdocs_folder_loop($current_cat, $data, $children=null) {
	$num_cols = mdocs_get_num_cols();
	$current_cat_string = '';
	$child_cats_string = '';
	if($current_cat['parent'] != '') {
		$tabs = mdocs_get_parents($current_cat['parent'],$data);	
	} else {
		$tabs = array('current-tab' => '', 'child-tab' => '&emsp;&emsp;');
	}
	$current_cat_string = '
		<tr class="mdocs-current-cat" >
			<td colspan="'.$num_cols.'" id="title" class="mdocs-tooltip">
				'.$tabs['current-tab'].
				'<b class="text-muted"><i class="fas fa-folder-open" aria-hidden="true"></i> '.stripcslashes($current_cat['name']).'</b>
			</td>
		</tr>';
	if(isset($current_cat['children']) && count($current_cat['children']) > 0) {
		foreach($current_cat['children'] as $index => $child) {
			$child_cats_string .= '
			<tr class="mdocs-sub-cats" >
				<td colspan="'.$num_cols.'" id="title" class="mdocs-tooltip">
					'.$tabs['child-tab'].
					'<a class="mdocs-folders" data-folder="'.$child['slug'].'" data-sort="'.$data['orderby'].'" data-sort-type="'.$data['sort-type'].'" data-current-sort="'.$data['orderby'].'" data-current-sort-type="'.$data['sort-type'].'" data-is-dashboard="'.$data['is-dashboard'].'" data-is-folder="true" data-hide-folder="'.$data['hide-folder'].'" data-hide-main-folder="'.$data['hide-main-folder'].'" ><i class="fas fa-folder" aria-hidden="true"></i> '.stripcslashes($child['name']).'</a>
				</td>
			</tr>';
			
		}
	}
	echo $current_cat_string.$child_cats_string;
}
/**
 * Displays the sub folders of a parent folder, also returns the number of columns to display.
 * @param string $current_cat The current category that the user is in.
 * @return int The number of columns to display.
 */
function mdocs_display_folders($current_cat, $data=null) {
	//if(is_numeric($current_cat['base_parent'])) $base_folder = $current_cat['base_parent'];
	//else $base_folder = $current_cat['index'];
	//$data['base-folder-index'] = $base_folder;
	$the_folders = mdocs_folder_loop($current_cat, $data);
}
function mdocs_cleanup_cats($value) {
	$upload_dir = wp_upload_dir();
	$mdocs = get_option('mdocs-list');
	$mdocs_cats = get_option('mdocs-cats');
	foreach($mdocs as $k => $v) {
		if($v['cat'] == $value['slug']) {
			wp_delete_attachment( intval($v['id']), true );
			wp_delete_post( intval($v['parent']), true );
			$name = substr($v['filename'], 0, strrpos($v['filename'], '.') );
			if(file_exists($upload_dir['basedir'].'/mdocs/'.$v['filename'])) @unlink($upload_dir['basedir'].'/mdocs/'.$v['filename']);
			foreach($v['archived'] as $a) @unlink($upload_dir['basedir'].'/mdocs/'.$a);
			$thumbnails = glob($upload_dir['basedir'].'/mdocs/'.$name.'-150x55*');
			foreach($thumbnails as $t) unlink($t);
			unset($mdocs[$k]);
		}
	}
	if(isset($value['children'])) {
		if(count($value['children']) > 0) {
			foreach($value['children'] as $key) {
				mdocs_cleanup_cats($key);	
			}
		}
	}

	mdocs_save_list($mdocs);
}
?>