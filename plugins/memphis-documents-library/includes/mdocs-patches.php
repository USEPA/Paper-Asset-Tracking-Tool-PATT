<?php
function mdocs_run_patch() {
	if(MDOCS_VERSION == '3.9.19' || MDOCS_VERSION == '3.9.20') {
		// 3.9.19 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-9-19-patch-var-1');
		add_option('mdocs-v3-9-19-patch-var-1',false);
		if(get_option('mdocs-v3-9-19-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			update_option('mdocs-displayed-file-info', array());
			update_option('mdocs-v3-9-19-patch-var-1', true);
		}
	}
}
function mdocs_patches() {
	//PATCHES
	if(!isset($_REQUEST['restore-default'])) {
		$patches = get_option('mdocs-patches');
		/*
		// 3.9.14 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-9-14-patch-var-1');
		add_option('mdocs-v3-9-14-patch-var-1',false);
		if(get_option('mdocs-v3-9-14-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			mdocs_update_file_info('show-file-type', 'File Type', null,'mdocs-black', 'mdocs_display_file_type', 10,9,false);
			update_option('mdocs-v3-9-14-patch-var-1', true);
		}
		// 3.9.11 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-9-11-patch-var-1');
		add_option('mdocs-v3-9-11-patch-var-1',false);
		if(get_option('mdocs-v3-9-11-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			// $key, $text=null, $icon=null, $color=null, $function=null, $order=null,$width=null, $show=null
			mdocs_update_file_info('show-name', 'Name', null,null, 'mdocs_display_name', '0', 15, true);
			mdocs_update_file_info('show-description', 'Description', null,null, 'mdocs_display_description', 1, 30);
			mdocs_update_file_info('show-downloads', 'Downloads', 'fas fa-cloud-download-alt','mdocs-orange', 'mdocs_display_downloads', 2, 12);
			mdocs_update_file_info('show-version', 'Version', 'fa fa-history','mdocs-blue', 'mdocs_display_version', 3,9);
			mdocs_update_file_info('show-author', 'Owner', 'fa fa-pencil','mdocs-green', 'mdocs_display_owner', 4,9);
			mdocs_update_file_info('show-real-author','Author', null,null, 'mdocs_display_real_author', 5,9);
			mdocs_update_file_info('show-modified', 'Last Modified', 'fa fa-calendar','mdocs-red', 'mdocs_display_updated', 6,15);
			mdocs_update_file_info('show-rating', 'Rating', null,null, 'mdocs_display_rating', 7,10);
			mdocs_update_file_info('show-download', 'Download', 'fa fa-download',null, 'mdocs_display_download_btn', 8,12);
			mdocs_update_file_info('show-file-size', 'File Size', 'fa fa-database',null, 'mdocs_display_file_size', 9,10);
			mdocs_delete_file_info('ratings');
			mdocs_delete_file_info('download-btn');
			update_option('mdocs-v3-9-11-patch-var-1', true);
		}
		// 3.9.10 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-9-10-patch-var-1');
		add_option('mdocs-v3-9-10-patch-var-1',false);
		if(get_option('mdocs-v3-9-10-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			mdocs_update_file_info('show-name', 'Name', null,null, 'mdocs_display_name', '0', 15, true);
			mdocs_update_file_info('show-description', 'Description', null,null, 'mdocs_display_description', null, 30);
			mdocs_update_file_info('show-downloads', 'Downloads', 'fas fa-cloud-download-alt','mdocs-orange', 'mdocs_display_downloads', null, 12);
			mdocs_update_file_info('show-version', 'Version', 'fa fa-history','mdocs-blue', 'mdocs_display_version', null,9);
			mdocs_update_file_info('show-author', 'Owner', 'fa fa-pencil','mdocs-green', 'mdocs_display_owner', null,9);
			mdocs_update_file_info('show-real-author','Author', null,null, 'mdocs_display_real_author', null,9);
			mdocs_update_file_info('show-modified', 'Last Modified', 'fa fa-calendar','mdocs-red', 'mdocs_display_updated', null,15);
			mdocs_update_file_info('show-rating', 'Rating', null,null, 'mdocs_display_rating', null,10);
			mdocs_update_file_info('show-download', 'Download', 'fa fa-download',null, 'mdocs_display_download_btn', null,12);
			mdocs_update_file_info('show-file-size', 'File Size', 'fa fa-database',null, 'mdocs_display_file_size', null,10);
			update_option('mdocs-v3-9-10-patch-var-1', true);
		}
		// 3.9.9 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-9-9-patch-var-1');
		add_option('mdocs-v3-9-9-patch-var-1',false);
		if(get_option('mdocs-v3-9-9-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			//update_option('mdocs-displayed-file-info',array());
			mdocs_update_file_info('show-description', 'Description', null,null, 'mdocs_display_description', 1);
			mdocs_update_file_info('show-downloads', 'Downloads', 'fas fa-cloud-download-alt','mdocs-orange', 'mdocs_display_downloads', 2);
			mdocs_update_file_info('show-version', 'Version', 'fa fa-history','mdocs-blue', 'mdocs_display_version', 3);
			mdocs_update_file_info('show-author', 'Owner', 'fa fa-pencil','mdocs-green', 'mdocs_display_owner', 4);
			mdocs_update_file_info('show-real-author','Author', null,null, 'mdocs_display_real_author', 5);
			mdocs_update_file_info('show-modified', 'Last Modified', 'fa fa-calendar','mdocs-red', 'mdocs_display_updated', 6);
			mdocs_update_file_info('show-rating', 'Rating', null,null, 'mdocs_display_rating', 7);
			mdocs_update_file_info('show-download', 'Download', 'fa fa-download',null, 'mdocs_display_download_btn', 8);
			mdocs_update_file_info('show-file-size', 'File Size', 'fa fa-database',null, 'mdocs_display_file_size', 9);
			update_option('mdocs-v3-9-9-patch-var-1', true);
		}
		*/
		// 3.9.2 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-9-2-patch-var-1');
		add_option('mdocs-v3-9-2-patch-var-1',false);
		if(get_option('mdocs-v3-9-2-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			$show_options = get_option('mdocs-displayed-file-info');
			$show_options['show-version']['icon'] = 'fa fa-history';
			update_option('mdocs-displayed-file-info', $show_options);
			update_option('mdocs-v3-9-2-patch-var-1', true);
		}
		
		// 3.9 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-9-patch-var-1');
		add_option('mdocs-v3-9-patch-var-1',false);
		if(get_option('mdocs-v3-9-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			mdocs_add_cap('batch-edit', 'Allow to Batch Edit', null, array('mdocs_batch_edit'));
			mdocs_add_cap('batch-move', 'Allow to Batch Move', null, array('mdocs_batch_move'));
			mdocs_add_cap('batch-delete', 'Allow to Batch Delete', null, array('mdocs_batch_delete'));
			mdocs_add_cap('allow-upload-frontend', 'Allow to Upload Frontend', null, array('mdocs_allow_upload_frontend'));
			update_option('mdocs-v3-9-patch-var-1', true);
		}
		
		// 3.8.7 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-8-7-patch-var-1');
		add_option('mdocs-v3-8-7-patch-var-1',false);
		if(get_option('mdocs-v3-8-7-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			mdocs_update_cap('manage-settings', 'Manage Settings', null, array('mdocs_manage_settings', 'manage_options'));
			mdocs_update_cap('manage-options', 'Manage Options', null, array('mdocs_manage_options'));
			mdocs_update_cap('allow-upload', 'Allow to Upload', null, array('mdocs_allow_upload'));
			mdocs_update_cap('private-post-viewing', 'View Private Posts and Pages', null, array('read_private_posts','read_private_pages'));
			/*
			mdocs_update_file_info('show-description', null, null, null, 'mdocs-none', 'mdocs-none');
			mdocs_update_file_info('show-rating', null, null, null, 'mdocs-none', 'mdocs-black');
			mdocs_update_file_info('show-download-btn', null, null, null, null, 'mdocs-black');
			mdocs_update_file_info('show-real-author', null, null, null, null, 'mdocs-black');
			mdocs_update_file_info('show-file-size', null, null, null, null, 'mdocs-black');
			*/
			update_option('mdocs-v3-8-7-patch-var-1', true);
		}
		// 3.8.6 patch 4
		register_setting('mdocs-patch-vars', 'mdocs-v3-8-6-patch-var-4');
		add_option('mdocs-v3-8-6-patch-var-4',false);
		if(get_option('mdocs-v3-8-6-patch-var-4') == false && is_array(get_option('mdocs-list'))) {
			mdocs_update_cap('manage-settings', 'Manage Settings', null, array('mdocs_manage_settings', 'manage_options'));
			mdocs_update_cap('allow-upload', 'Allow to Upload', null, array('mdocs_allow_upload'));
			mdocs_update_cap('private-post-viewing', 'View Private Posts and Pages', null, array('read_private_posts','read_private_pages'));
			$mdocs_caps = get_option('mdocs-caps');
			foreach($mdocs_caps as $index => $cap) {
				if($index == 'allow-upload') {
					if(is_array($cap['roles'])) {
						foreach($cap['roles'] as $role) {
							$role_object = get_role($role);
							if($role_object != null) $role_object->add_cap('mdocs_allow_upload');
						}
					}
				}
				if(isset($cap['name'])) {
					$mdocs_caps[$index]['caps'] = $cap['names'];
					unset($mdocs_caps[$index]['names']);
				}
			}
			update_option('mdocs-caps',$mdocs_caps);
			mdocs_add_cap('manage-options', 'Manage Options', array(), array('mdocs_manage_options'));
			/*
			mdocs_update_file_info('show-description', null, null, null, 'fa fa-text', 'mdocs-black');
			mdocs_update_file_info('show-ratings', null, null, null, 'fa fa-text', 'mdocs-black');
			mdocs_update_file_info('show-download-btn', null, null, null, null, 'mdocs-black');
			mdocs_update_file_info('show-real-author', null, null, null, 'fa fa-pencil', 'mdocs-black');
			mdocs_update_file_info('show-file-size', null, null, null, null, 'mdocs-black');
			*/
			update_option('mdocs-v3-8-6-patch-var-4', true);
		}
		// 3.8.6 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-8-6-patch-var-3');
		add_option('mdocs-v3-8-6-patch-var-3',false);
		if(get_option('mdocs-v3-8-6-patch-var-3') == false && is_array(get_option('mdocs-list'))) {
			$mdocs_caps = get_option('mdocs-caps');
			$allow_upload = get_option('mdocs-allow-upload');
			$count = 0;
			if(isset($allow_upload) && is_array($allow_upload)) {
				foreach($allow_upload as $index => $allow) {
					$mdocs_caps['allow-upload']['roles'][$count] = $index;
					$count++;
				}
			}
			$private_view = get_option('mdocs-view-private');
			$count = 0;
			foreach($private_view as $index => $private) {
				if($private && $index != 'administrator') $mdocs_caps['private-post-viewing']['roles'][$count] = $index;
				$count++;
			}
			update_option('mdocs-caps', $mdocs_caps);
			update_option('mdocs-v3-8-6-patch-var-3', true);
			//$role_object = get_role('administrator');
			//$role_object->add_cap('mdocs_manage_settings');
			//$role_object->add_cap('mdocs_allow_upload');
			//mdocs_manage_roles();
		}
		// PATCHES
		// 3.7.4 patch 1
		/*
		register_setting('mdocs-patch-vars', 'mdocs-v3-7-4-patch-var-1');
		add_option('mdocs-v3-7-4-patch-var-1',false);
		if(get_option('mdocs-v3-7-4-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			$dispay_array = array();
			$display_array['show-description'] = array(
				'slug' => 'desc',
				'text' => 'Description',
				'icon' => '',
				'color' => '',
				'function' => 'mdocs_display_description',
			);
			$display_array['show-downloads'] = array(
				'slug' =>  'downloads',
				'text' =>  'Downloads',
				'icon' =>  'fas fa-cloud-download',
				'color' =>  'mdocs-orange',
				'function' =>  'mdocs_display_downloads',
				'show' =>  '1',
			);
			$display_array['show-version'] = array(
				'slug' =>  'version',
				'text' =>  'Version',
				'icon' =>  'fa fa-history',
				'color' =>  'mdocs-blue',
				'function' =>  'mdocs_display_version',
				'show' =>  '1',
			);
			$display_array['show-author'] = array(
				'slug' =>  'owner',
				'text' =>  'Owner',
				'icon' =>  'fa fa-pencil',
				'color' =>  'mdocs-green',
				'function' =>  'mdocs_display_owner',
				'show' =>  '1',
			);
			$display_array['show-real-author'] = array(
				'slug' =>  'real-author',
				'text' =>  'Author',
				'icon' =>  '',
				'color' =>  '',
				'function' =>  'mdocs_display_real_author',
			);
			$display_array['show-update'] = array(
				'slug' =>  'modified',
				'text' =>  'Last Modified',
				'icon' =>  'fa fa-calendar',
				'color' =>  'mdocs-red',
				'function' =>  'mdocs_display_updated',
				'show' =>  '1',
			);
			$display_array['show-ratings'] = array(
				'slug' =>  'rating',
				'text' =>  'Rating',
				'icon' =>  '',
				'color' =>  '',
				'function' =>  'mdocs_display_rating',
				'show' =>  '1',
			);
			$display_array['show-download-btn'] = array(
				'slug' =>  'download',
				'text' =>  'Download',
				'icon' =>  'fa fa-download',
				'color' =>  '',
				'function' =>  'mdocs_display_download_btn',
			);
			$display_array['show-file-size'] = array(
				'slug' =>  'file-size',
				'text' =>  'File Size',
				'icon' =>  'fa fa-database',
				'color' =>  '',
				'function' =>  'mdocs_display_file_size',
			);
			$show_options = get_option('mdocs-displayed-file-info');
			foreach($display_array as $key => $show_array) {
				if(!key_exists($key, $show_options)) {
					$show_options[$key] = $show_array;
				}
			}
			update_option('mdocs-displayed-file-info', $show_options);
			update_option('mdocs-v3-7-4-patch-var-1', true);
		}
		*/
		// 3.7.3 patch 1
		/*
		register_setting('mdocs-patch-vars', 'mdocs-v3-7-3-patch-var-1');
		add_option('mdocs-v3-7-3-patch-var-1',false);
		if(get_option('mdocs-v3-7-3-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			$show_options = get_option('mdocs-displayed-file-info');
			$show_options['show-file-size'] = array('show' => false, 'slug' => 'file-size', 'text' =>  __('File Size', 'memphis-documents-library'), 'icon' => 'fa fa-file-text-o', 'color' => '', 'function' => 'mdocs_display_file_size');
			update_option('mdocs-displayed-file-info', $show_options);
			update_option('mdocs-v3-7-3-patch-var-1', true);
		}
		*/
		// PATCHES
		// 3.7.1 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-7-1-patch-var-1');
		add_option('mdocs-v3-7-1-patch-var-1',false);
		if(get_option('mdocs-v3-7-1-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			$show_options = get_option('mdocs-displayed-file-info');
			foreach(get_option('mdocs-displayed-file-info') as $key => $option) {
				if(get_option('mdocs-'.$key) != null && $key != 'show-description') {
					$old_option_value = get_option('mdocs-'.$key);
					if($old_option_value == '1') $show_options[$key]['show'] = true;
					else $show_options[$key]['show'] = false;
				}
			}
			update_option('mdocs-displayed-file-info', $show_options);
			update_option('mdocs-v3-7-1-patch-var-1', true);
		}
		// PATCHES
		// 3.6.13 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-6-13-patch-var-1');
		add_option('mdocs-v3-6-13-patch-var-1',false);
		if(get_option('mdocs-v3-6-13-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			$mdocs = mdocs_array_sort();
			foreach($mdocs as $index  => $the_mdoc) {	
				$mdocs_post = array(
					'ID' => $the_mdoc['parent'],
					'post_excerpt' => '',
				);
				wp_update_post( $mdocs_post );
				$mdocs_file = array(
					'ID' => $the_mdoc['id'],
					'post_excerpt' => '',
				);
				wp_update_post( $mdocs_file );
			}
			update_option('mdocs-v3-6-13-patch-var-1', true);
		}
		// PATCHES
		// 3.6 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-6-patch-var-1');
		add_option('mdocs-v3-6-patch-var-1',false);
		if(get_option('mdocs-v3-6-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			update_option('mdocs-hide-file-type-icon', true);
			update_option('mdocs-v3-6-patch-var-1', true);
		}
		// PATCHES
		// 3.4.1 patch 1
		register_setting('mdocs-patch-vars', 'mdocs-v3-4-patch-var-1');
		add_option('mdocs-v3-4-patch-var-1',false);
		if(get_option('mdocs-v3-4-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			$mdocs = get_option('mdocs-list');
			foreach($mdocs as $index => $the_mdoc) {
				$mdocs_media = get_post($the_mdoc['id']);
				$mdocs_media->post_content = '[mdocs_media_attachment]';
				wp_update_post($mdocs_media);
			}
			update_option('mdocs-v3-4-patch-var-1', true);
		}
		// 3.0 patch 3
		register_setting('mdocs-patch-vars', 'mdocs-v3-0-patch-var-3');
		add_option('mdocs-v3-0-patch-var-3',false);
		if(get_option('mdocs-v3-0-patch-var-3') == false && is_array(get_option('mdocs-list'))) {
			$list = get_option('mdocs-list');
			$cats = get_option('mdocs-cats');
			delete_option('mdocs-list');
			delete_option('mdocs-cats');
			add_option('mdocs-list', $list, '','no');
			add_option('mdocs-cats', $cats, '', 'no');
			update_option('mdocs-v3-0-patch-var-3', true);
		}
		// 3.0 patch 2
		register_setting('mdocs-patch-vars', 'mdocs-v3-0-patch-var-2');
		add_option('mdocs-v3-0-patch-var-2',false);
		if(get_option('mdocs-v3-0-patch-var-2') == false && is_array(get_option('mdocs-list'))) {
			$mdocs = get_option('mdocs-list');
			global $current_user;
			foreach($mdocs as $index => $the_mdoc) {
				$mdocs[$index]['owner'] = $current_user->user_login;
				$mdocs[$index]['contributors'] = array();
			}
			update_option('mdocs-list', $mdocs, '' , 'no');
			update_option('mdocs-v3-0-patch-var-2',true);
		}
		/*
		// 3.0 patch 1
		//delete_option('mdocs-v3-0-patch-var-1');
		//delete_option('mdocs-box-view-updated');
		register_setting('mdocs-patch-vars', 'mdocs-v3-0-patch-var-1');
		add_option('mdocs-v3-0-patch-var-1',false);
		register_setting('mdocs-patch-vars', 'mdocs-box-view-updated');
		add_option('mdocs-box-view-updated',false);
		if(get_option('mdocs-v3-0-patch-var-1') == false && is_array(get_option('mdocs-list')) && count(get_option('mdocs-list')) > 0) {
			add_action( 'admin_head', 'mdocs_v3_0_patch' );
			function mdocs_v3_0_patch() {
				$mdocs = get_option('mdocs-list');
				//MEMPHIS DOCS
				wp_register_script( 'mdocs-script-patch', MDOCS_URL.'memphis-documents.js');
				wp_enqueue_script('mdocs-script-patch');
				wp_register_style( 'mdocs-font-awesome2-style-patch', '//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
				wp_enqueue_style( 'mdocs-font-awesome2-style-patch' );
				wp_localize_script( 'mdocs-script-patch', 'mdocs_patch_js', array('ajaxurl' => admin_url( 'admin-ajax.php' ), 'patch_text_3_0_1' => __('UPDATE HAS STARTER, DO NOT LEAVE THIS PAGE!'),'patch_text_3_0_2' => __('Go grab a coffee this may take awhile.'),));
				?>
				<script type="application/x-javascript">
					jQuery(document).ready(function() {
						mdocs_v3_0_patch(<?php echo count($mdocs); ?>);
					});
				</script>
				<?php
			}
			wp_deregister_script('mdocs-script-patch');
			wp_deregister_style('mdocs-font-awesome2-style-patch');
		} else {
			update_option('mdocs-v3-0-patch-var-1',true);
			update_option('mdocs-box-view-updated',true);
		}
		*/
		// 2.6.6
		register_setting('mdocs-patch-vars', 'mdocs-v2-6-6-patch-var-1');
		add_action('mdocs-v2-6-6-patch-var-1',false);
		if(get_option('mdocs-v2-6-6-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			$this_query = new WP_Query('category_name=mdocs-media&posts_per_page=-1');	
			foreach($this_query->posts as $index => $post) set_post_type($post->ID,'mdocs-posts');
			update_option('mdocs-v2-6-6-patch-var-1',true);
		}
		// 2.6.7
		/*
		register_setting('mdocs-patch-vars', 'mdocs-v2-6-7-patch-var-1');
		add_action('mdocs-v2-6-7-patch-var-1',false);
		if(get_option('mdocs-v2-6-7-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			$mdocs_cat = get_category_by_slug('mdocs-media');
			wp_delete_category($mdocs_cat->cat_ID);
			update_option('mdocs-v2-6-7-patch-var-1',true);
		}
		*/
		// 2.5
		register_setting('mdocs-patch-vars', 'mdocs-v2-5-patch-var-1');
		add_action('mdocs-v2-5-patch-var-1',false);
		if(get_option('mdocs-v2-5-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
			$num_cats = 1;
			foreach( get_option('mdocs-cats') as $index => $cat ){ $num_cats++;}
			update_option('mdocs-num-cats',$num_cats);
			add_action( 'admin_notices', 'mdocs_v2_5_admin_notice_v1' );
			update_option('mdocs-v2-5-patch-var-1',true);
		} else update_option('mdocs-v2-5-patch-var-1',true);
		// 2.4
		register_setting('mdocs-patch-vars', 'mdocs-v2-4-patch-var-1');
		add_option('mdocs-v2-4-patch-var-1',false);
		if(get_option('mdocs-v2-4-patch-var-1') == false  && is_array(get_option('mdocs-list'))) {
			$mdocs_cats = get_option('mdocs-cats');
			$new_mdocs_cats = array();
			foreach($mdocs_cats as $index => $cat) array_push($new_mdocs_cats, array('slug' => $index,'name' => $cat, 'parent' => '', 'children' => array(), 'depth' => 0));
			update_option('mdocs-cats', $new_mdocs_cats, '' , 'no');
			update_option('mdocs-v2-4-patch-var-1', true);
			add_action( 'admin_notices', 'mdocs_v2_4_admin_notice_v1' );
		} else update_option('mdocs-v2-4-patch-var-1', true);
		// 2.3
		register_setting('mdocs-patch-vars', 'mdocs-v2-3-1-patch-var-1');
		add_option('mdocs-v2-3-1-patch-var-1',false);
		if(get_option('mdocs-v2-3-1-patch-var-1') == false  && is_array(get_option('mdocs-list'))) {
			$htaccess = $upload_dir['basedir'].'/mdocs/.htaccess';
			$fh = fopen($htaccess, 'w');
			update_option('mdocs-htaccess', "Deny from all\nOptions +Indexes\nAllow from .google.com");
			$mdocs_htaccess = get_option('mdocs-htaccess');
			fwrite($fh, $mdocs_htaccess);
			fclose($fh);
			chmod($htaccess, 0660);
			update_option('mdocs-v2-3-1-patch-var-1', true);
			add_action( 'admin_notices', 'mdocs_v2_2_1_admin_notice_v1' );
		} else update_option('mdocs-v2-3-1-patch-var-1', true);
		//2.1 
		register_setting('mdocs-settings', 'mdocs-2-1-patch-1');
		add_option('mdocs-2-1-patch-1',false);
		if(get_option('mdocs-2-1-patch-1') == false  && is_array(get_option('mdocs-list'))) {
			$mdocs = get_option('mdocs-list');
			foreach(get_option('mdocs-list') as $index => $the_mdoc) {
				if(!is_array($the_mdoc['ratings'])) {
					$the_mdoc['ratings'] = array();
					$the_mdoc['rating'] = 0;
					$mdocs[$index] = $the_mdoc;
				}
				if(!key_exists('rating', $mdocs)) {
					$the_mdoc['rating'] = 0;
					$mdocs[$index] = $the_mdoc;
				}
			}
			mdocs_save_list($mdocs);
			update_option('mdocs-2-1-patch-1', true);
		} else update_option('mdocs-2-1-patch-1', true);
	} else {
		update_option('mdocs-v2-6-6-patch-var-1',true);
		update_option('mdocs-v2-6-7-patch-var-1',true);
		update_option('mdocs-v2-5-patch-var-1',true);
		update_option('mdocs-v2-4-patch-var-1', true);
		update_option('mdocs-v2-3-1-patch-var-1', true);
		update_option('mdocs-2-1-patch-1', true);
		@unlink($upload_dir['basedir'].MDOCS_DIR.'mdocs-files.bak');
	}
}
function mdocs_v2_2_1_admin_notice_v1() {
    ?>
    <div class="update-nag">
        <p><?php _e('Your Memphis <em>.htaccess</em> file has been updated to allow google.com access to the system.   This step is necessary to allow documents to be previewed.','memphis-documents-library'); ?></p>
    </div>
    <?php
}
function mdocs_v2_4_admin_notice_v1() {
    ?>
    <div class="update-nag">
        <p><?php _e('Your Memphis <em>Categories</em> have been updated to handle subcategories this should not effect your current file system in anyway.  If there is any issues please post a comment in the support forum of this plugin.  It is recommended to re-export your files again due to the new way categories are structured.','memphis-documents-library'); ?></p>
    </div
    <?php
}
function mdocs_v2_5_admin_notice_v1() {
    ?>
    <div class="update-nag">
        <p><?php _e('Your Memphis <em>Categories</em> have been counted to handle subcategories this should not effect your current file system in anyway.  If there is any issues please post a comment in the support forum of this plugin.  It is recommended to re-export your files again due to the new way categories are structured.','memphis-documents-library'); ?></p>
    </div
    <?php
}
function mdocs_v3_0_patch_cancel_updater() {
	update_option('mdocs-v3-0-patch-var-1',true);
	update_option('mdocs-box-view-updated',false);
}
?>