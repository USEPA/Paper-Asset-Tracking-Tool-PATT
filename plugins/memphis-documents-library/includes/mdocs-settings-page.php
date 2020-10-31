<?php
function mdocs_settings() {
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_show_post_menu = get_option('mdocs-show-post-menu');
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$mdocs_hide_all_posts = get_option( 'mdocs-hide-all-posts' );
	$mdocs_hide_all_posts_default = get_option( 'mdocs-hide-all-posts-default' );
	$mdocs_hide_all_posts_non_members = get_option( 'mdocs-hide-all-posts-non-members' );
	$mdocs_hide_all_posts_non_members_default = get_option( 'mdocs-hide-all-posts-non-members-default' );
	$mdocs_show_version = get_option( 'mdocs-show-version' );
	$mdocs_show_social = get_option( 'mdocs-show-social' );
	$mdocs_show_versions = get_option('mdocs-show-versions');
	$mdocs_show_share = get_option('mdocs-show-share');
	$mdocs_show_new_banners = get_option('mdocs-show-new-banners');
	$mdocs_time_to_display_banners = strval(get_option('mdocs-time-to-display-banners'));
	$mdocs_sort_type = get_option('mdocs-sort-type');
	$mdocs_sort_style = get_option('mdocs-sort-style');
	$mdocs_default_content = get_option('mdocs-default-content');
	$mdocs_show_description = get_option('mdocs-show-description');
	$mdocs_show_preview = get_option('mdocs-show-preview');
	$mdocs_htaccess = get_option('mdocs-htaccess');
	$mdocs_font_size = get_option('mdocs-font-size');
	$mdocs_post_title_font_size = get_option('mdocs-post-title-font-size');
	$mdocs_post_show_title = get_option('mdocs-post-show-title');
	$mdocs_override_post_title_font_size = get_option('mdocs-override-post-title-font-size');
	$mdocs_hide_subfolders = get_option('mdocs-hide-subfolders');
	$mdocs_disable_bootstrap = get_option('mdocs-disable-bootstrap');
	$mdocs_disable_jquery = get_option('mdocs-disable-jquery');
	$mdocs_disable_fontawesome = get_option('mdocs-disable-fontawesome');
	$mdocs_show_no_file_found = get_option('mdocs-show-no-file-found');
	$mdocs_preview_type = get_option('mdocs-preview-type');
	$mdocs_remove_posts_from_homepage = get_option('mdocs-remove-posts-from-homepage');
	$mdocs_dropdown_toggle_fix = get_option('mdocs-dropdown-toggle-fix');
	$mdocs_show_upload_folder = get_option('mdocs-show-upload-folder');
	$mdocs_show_upload_version = get_option('mdocs-show-upload-version');
	$mdocs_show_upload_date = get_option('mdocs-show-upload-date');
	$mdocs_show_upload_file_status = get_option('mdocs-show-upload-file-status');
	$mdocs_show_upload_post_status = get_option('mdocs-show-upload-post-status');
	$mdocs_show_upload_social = get_option('mdocs-show-upload-social');
	$mdocs_show_upload_contributors = get_option('mdocs-show-upload-contributors');
	$mdocs_show_upload_tags = get_option('mdocs-show-upload-tags');
	$mdocs_show_upload_description = get_option('mdocs-show-upload-description');
	$mdocs_show_current_folder_on_top = get_option('mdocs-show-current-folder-on-top');
	$mdocs_show_upload_button_on_normal_page = get_option('mdocs-show-upload-button-on-normal-page');
	$mdocs_show_media_files = get_option('mdocs-show-media-files');
	$mdocs_hide_file_type_icon = get_option('mdocs-hide-file-type-icon');
	$mdocs_hide_navbar = get_option('mdocs-hide-navbar');
	$mdocs_hide_sortbar = get_option('mdocs-hide-sortbar');
	$mdocs_hide_entry_div = get_option('mdocs-hide-entry-div');
	$mdocs_override_time_offset = get_option('mdocs-override-time-offset');
	$mdocs_override_time_offset_value = get_option('mdocs-override-time-offset-value');
	$mdocs_disable_sessions = get_option('mdocs-disable-sessions');
	//mdocs_manage_roles();
	mdocs_list_header();
?>
<h2><?php _e('Library Settings','memphis-documents-library'); ?></h2>
<form enctype="multipart/form-data" method="post" action="options.php" class="mdocs-setting-form">
	<?php settings_fields( 'mdocs-global-settings' ); ?>
	<input type="hidden" name="mdocs-view-private[administrator]" value="1" />
	<input type="hidden" name="mdocs-download-color-normal" value="<?php echo get_option( 'mdocs-download-color-normal' ); ?>" />
	<input type="hidden" name="mdocs-download-color-hover" value="<?php echo get_option( 'mdocs-download-color-hover' ); ?>" />
	<input type="hidden" name="mdocs-hide-all-posts-default" value="<?php echo get_option( 'mdocs-hide-all-posts-default' ); ?>" />
	<input type="hidden" name="mdocs-hide-all-posts-non-members-default" value="<?php echo get_option( 'mdocs-hide-all-posts-non-members-default' ); ?>" />
<table class="table form-table mdocs-settings-table">
	<tr>
		<th><?php _e('System Settings', 'memphis-documents-library'); ?></th>
		<td>
			<!--<h5><?php _e('Disable System Settings', 'memphis-documents-library'); ?></h5>
			<input type="checkbox" name="mdocs-disable-sessions" value="1"  <?php checked(1, $mdocs_disable_sessions) ?> /> <span><?php _e('Disable Sessions', 'memphis-documents-library'); ?></span><br>
			<small><?php _e('This setting might result in duplicate files being added, deleted, or other actions.', 'memphis-documents-library'); ?></small>-->
			<h5><?php _e('Disable Thrid Party Includes'); ?></h5>
			<input type="checkbox" name="mdocs-disable-bootstrap" value="1"  <?php checked(1,$mdocs_disable_bootstrap) ?> /> <span><?php _e('Bootstrap - Frontend', 'memphis-documents-library'); ?></span>
			<input type="checkbox" name="mdocs-disable-bootstrap-admin" value="1"  <?php checked(1,get_option('mdocs-disable-bootstrap-admin')) ?> /> <span><?php _e('Bootstrap - Dashboard', 'memphis-documents-library'); ?></span>
			<input type="checkbox" name="mdocs-dropdown-toggle-fix" value="1"  <?php checked(1,$mdocs_dropdown_toggle_fix) ?> /> <span><?php _e('Bootstrap Dropdown Fix', 'memphis-documents-library'); ?></span><br>
			<small><?php _e('Using one, all or none of these Bootstrap fixes, may help.', 'memphis-documents-library'); ?></small><br>
			<input type="checkbox" name="mdocs-disable-jquery" value="1"  <?php checked(1,$mdocs_disable_jquery) ?> /> <span><?php _e('jQuery', 'memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-disable-fontawesome" value="1"  <?php checked(1,$mdocs_disable_fontawesome) ?> /> <span><?php _e('Fontawesome', 'memphis-documents-library'); ?></span><br>
			<h5><?php _e('Document Preview Settings', 'memphis-documents-library'); ?></h5>
			<input type="radio" name="mdocs-preview-type" value="google" <?php checked('google', $mdocs_preview_type) ?>> <?php _e('Use Local Document Preview', 'memphis-documents-library'); ?> - <small><i>
			<?php _e('Local preview allows you to preview PDF, Image files, and ZIP files only, if you are looking for capabilities try using Box View.', 'memphis-documents-library'); ?></i></small><br>	
			<input type="radio" name="mdocs-preview-type" value="box" <?php checked('box', $mdocs_preview_type) ?>> <?php _e('Use Box Document Preview', 'memphis-documents-library'); ?> - <small><i><?php _e('A Box development key is needed to use Box document preview.', 'memphis-documents-library'); ?><br><?php _e('To get a Box developers key please click the link below and create an account.', 'memphis-documents-library'); ?></i></small><br>
			<label><?php _e('Box View Key','memphis-documents-library'); ?></label><br>
			<input style="width: 80%;" type="text" value="<?php echo get_option('mdocs-box-view-key'); ?>" name="mdocs-box-view-key"  placeholder="<?php _e('Enter your key here', 'memphis-documents-library'); ?>"/><br>
			<a href="https://developers.box.com" target="_blank" alt="<?php _e('Login to Box Developer site to get your key.', 'memphis-documents-library'); ?>"><?php _e('Login to Box Developer site to get your key.', 'memphis-documents-library'); ?></a><br>
			<a href="https://kingofnothing.net/creating-a-box-api-developer-key-for-memphis-documents-library/" target="_blank" alt="<?php _e('Creating a Box API Developer Key for Memphis Documents Library.', 'memphis-documents-library'); ?>"><?php _e('Creating a Box API Developer Key for Memphis Documents Library.'); ?></a><br>
			<h5><?php _e('Box View Options', 'memphis-documents-library'); ?></h5>
			<input type="checkbox" name="mdocs-box-show-print-and-download" value="1"  <?php checked(1,get_option('mdocs-box-show-print-and-download')) ?> /> <span><?php _e('Show Print and Download Buttons', 'memphis-documents-library'); ?></span><br>
			<h5><?php _e('Frontend Upload Options', 'memphis-documents-library'); ?></h5>
			<input type="checkbox" name="mdocs-frontend-upload-redirect" value="1"  <?php checked(1,get_option('mdocs-frontend-upload-redirect')) ?> /> <span><?php _e('Redirect is Blank', 'memphis-documents-library'); ?></span><br>
		</td>
		<th></th>
		<td>
			<h5><?php _e('Date/Time Format','memphis-documents-library'); ?></h5>
			<input type="text" name="mdocs-date-format" value="<?php echo get_option('mdocs-date-format');?>"  /><br>
			<a href="http://php.net/manual/en/function.date.php" target="_blank" alt="<?php _e('Date/Time Format Reference'); ?>"><?php _e('Date/Time Format Reference'); ?></a><br>
			<h5><?php _e('.htaccess File Editor','memphis-documents-library'); ?></h5>
			<?php
			if(isset($_GET['settings-updated']) && $_GET['page'] == 'memphis-documents.php') {
				$upload_dir = wp_upload_dir();
				$htaccess = file_put_contents($upload_dir['basedir'].MDOCS_DIR.'.htaccess', $mdocs_htaccess);
			}
			?>
			<textarea cols="30" rows="10" name="mdocs-htaccess"><?php echo $mdocs_htaccess; ?></textarea><br>
			<h5><?php _e('Language Settings','memphis-documents-library'); ?></h5>
			<input type="checkbox" name="mdocs-convert-to-latin" value="1"  <?php checked(1,get_option('mdocs-convert-to-latin')) ?> /> <span><?php _e('Convert Filenames to Latin', 'memphis-documents-library'); ?></span><br>
			<h5><?php _e('Search Settings','memphis-documents-library'); ?></h5>
			<input type="checkbox" name="mdocs-show-advanced-search" value="1"  <?php checked(1,get_option('mdocs-show-advanced-search')) ?> /> <span><?php _e('Turn on Advance Search', 'memphis-documents-library'); ?></span><br>
		</td>
	</tr>
	
	
	
	<tr>
		<td colspan="4">
			<h2><?php _e('Roles and Capabilities', 'memphis-documents-library'); ?></h2>
			<table class="table table-striped">
				<tr >
					<th></th>
					<?php
					$wp_roles = get_editable_roles();
					foreach($wp_roles as $index => $role) {
						if($index != 'administrator') {
						?>
						<th style="font-size: 10px;"><?php echo $role['name']; ?></th>
						<?php
						}
					}
					?>
				</tr>
				
					<?php
					$mdocs_caps = get_option('mdocs-caps');
					ksort($mdocs_caps);
					foreach($mdocs_caps as $index => $cap) {
						if(!isset($cap['roles'])) $cap['roles'] = array();
						?>
						<tr>
						<td style="font-size: 11px;"><?php mdocs_local($cap['title']); ?></td>
						<?php
						foreach($wp_roles as $i => $role) {
							if($i != 'administrator') {
								echo '<td>';
								/*
								foreach($role['capabilities'] as $cap_var => $cap_value) {
									//var_dump($cap_var);
									if(in_array($cap_var, $cap['caps'])) {
										echo  'found: '. $cap_value;
									?>
									<input type="hidden" name="<?php echo $cap_var; ?>" value="<?php echo $cap_value; ?>">
									
									<?php
									}
								}
								*/
								?>
								
									<!--<input type="hidden" name="<?php echo @$cap_var; ?>" value="<?php echo $cap['title']; ?>">-->
									<input type="hidden" name="mdocs-caps[<?php echo $index; ?>][title]" value="<?php echo $cap['title']; ?>">
									<?php
									for($j=0; $j < count($cap['caps']); $j++) { ?>
										<input type="hidden" name="mdocs-caps[<?php echo $index; ?>][caps][<?php echo $j; ?>]" value="<?php echo $cap['caps'][$j]; ?>">
									<?php } ?>
									<input type="checkbox" name="mdocs-caps[<?php echo $index; ?>][roles][]" value="<?php echo $i; ?>" <?php checked(in_array($i, $cap['roles'])) ?> >
									
								</td>
								<?php
							}
						}
						?></tr><?php
					}
					?>
			</table>
		</td>
	</tr>
	
	<tr>
		<th><?php _e('Style Configuration','memphis-documents-library'); ?></th>
		<td>
			<h5><?php _e('Download Button Options','memphis-documents-library'); ?></h5>
			<label><?php _e('Background Color','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-color-normal'); ?>" name="mdocs-download-color-normal" id="bg-color-mdocs-picker" data-default-color="#d14836" /><br>
			<label><?php _e('Background Hover Color','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-color-hover'); ?>" name="mdocs-download-color-hover" id="bg-hover-color-mdocs-picker" data-default-color="#c34131" /><br>
			<label><?php _e('Text Color','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-text-color-normal'); ?>" name="mdocs-download-text-color-normal" id="bg-text-color-mdocs-picker" data-default-color="#ffffff" /><br>
			<label><?php _e('Text Hover Color','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-text-color-hover'); ?>" name="mdocs-download-text-color-hover" id="bg-text-hover-color-mdocs-picker" data-default-color="#ffffff" /><br>
			<h5><?php _e('Download Button Preview','memphis-documents-library'); ?></h5>
			<button class="btn btn-primary mdocs-download-btn-config"><?php echo __('Download','memphis-documents-library');?></button>
			
			
			<h5><?php _e('File Highlight Colours','memphis-documents-library'); ?></h5>
			<label><?php _e('Highlight Color New Normal','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-file-highlight-color-new-normal'); ?>" name="mdocs-file-highlight-color-new-normal" id="highlight-color-new-normal-mdocs-picker" data-default-color="#dff0d8" /><br>
			<label><?php _e('Highlight Color New Hover','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-file-highlight-color-new-hover'); ?>" name="mdocs-file-highlight-color-new-hover" id="highlight-color-new-hover-mdocs-picker" data-default-color="#d0e9c6" /><br>
			
			<label><?php _e('Highlight Color Updated Normal','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-file-highlight-color-updated-normal'); ?>" name="mdocs-file-highlight-color-updated-normal" id="highlight-color-updated-normal-mdocs-picker" data-default-color="#d9edf7" /><br>
			<label><?php _e('Highlight Color Updated Hover','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-file-highlight-color-updated-hover'); ?>" name="mdocs-file-highlight-color-updated-hover" id="highlight-color-updated-hoverl-mdocs-picker" data-default-color="#c4e3f3" /><br>
			
			
		</td>
		<th><?php _e('Navbar Style','memphis-documents-library'); ?></th>
		<td>
			<h5><?php _e('Navbar Options','memphis-documents-library'); ?></h5>
			<label><?php _e('Background Color','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-navbar-bgcolor'); ?>" name="mdocs-navbar-bgcolor" id="navbar-bg-color-mdocs-picker" data-default-color="#f8f8f8" /><br>
			<label><?php _e('Border Color','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-navbar-bordercolor'); ?>" name="mdocs-navbar-bordercolor" id="navbar-border-color-mdocs-picker" data-default-color="#c4c4c4" /><br>
			<label><?php _e('Text Color','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-navbar-text-color-normal'); ?>" name="mdocs-navbar-text-color-normal" id="navbar-text-color-normal-mdocs-picker" data-default-color="#777777" /><br>
			<label><?php _e('Text Hover Color','memphis-documents-library'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-navbar-text-color-hover'); ?>" name="mdocs-navbar-text-color-hover" id="navbar-text-color-hover-mdocs-picker" data-default-color="#333333" /><br>
			<h5><?php _e('Navbar Preview','memphis-documents-library'); ?></h5>
			<?php
			$nav_class = 'mdocs-navbar-default';
			$cats = get_option('mdocs-cats');
			?>
			<div class="mdoc-navbar-container">
				<nav class="navbar mdocs-navbar-default mdocs-navbar-default-config" role="navigation" id="mdocs-navbar">
					<div class="container-fluid">
						<div class="navbar-header">
							<span class="navbar-brand mdocs-navbar-a-config"><?php _e('Folders','memphis-documents-library'); ?></span>
						</div>
						<div class="collapse navbar-collapse" id="mdocs-navbar-collapse">
							<ul class="nav navbar-nav">
								<li><a href="" class="mdocs-navbar-a-config">Folder 1</a></li>
								<li><a href="" class="mdocs-navbar-a-config">Folder 2</a></li>
							</ul>
						</div>
					</div>
				</nav>
			</div>
		</td>
	</tr>
	<tr>
		<th><?php _e('Configuration Settings','memphis-documents-library'); ?></th>
		<td>
			<h5><?php _e('UI Options','memphis-documents-library'); ?></h5>
			<input type="checkbox" name="mdocs-hide-footer" value="1"  <?php checked(1, get_option('mdocs-hide-footer')) ?> /> <span><?php _e('Hide the Footer of the Documents List', 'memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-hide-navbar" value="1"  <?php checked(1,$mdocs_hide_navbar) ?>/> <span><?php _e('Hide Navbar', 'memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-hide-file-type-icon" value="1"  <?php checked(1,$mdocs_hide_file_type_icon) ?>/> <span><?php _e('Hide File Type Icons', 'memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-hide-name" value="1"  <?php checked(1,get_option('mdocs-hide-name')) ?>/> <span><?php _e('Hide Name', 'memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-hide-filename" value="1"  <?php checked(1,get_option('mdocs-hide-filename')) ?>/> <span><?php _e('Hide File Name', 'memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-hide-all-subfolders" value="1"  <?php checked(1,get_option('mdocs-hide-all-subfolders')) ?>/> <span><?php _e('Hide Sub Folder Main Page - [mdocs]', 'memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-hide-subfolders" value="1"  <?php checked(1,$mdocs_hide_subfolders) ?>/> <span><?php _e('Hide Sub Folders Category Page - [mdocs cat="A Cat"]', 'memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-show-no-file-found" value="1"  <?php checked(1,$mdocs_show_no_file_found) ?> /> <span><?php _e('Show No Files Found', 'memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-hide-entry-div" value="1"  <?php checked(1,$mdocs_hide_entry_div) ?> /> <span><?php _e('Hide the WordPress Entry Summary Div', 'memphis-documents-library'); ?></span><br>
			<span><?php _e('Document List Font Size', 'memphis-documents-library'); ?></span>
			<select name="mdocs-font-size" id="mdocs-font-size" >
				<?php
				for($i=3; $i < 51; $i++) {
					if($mdocs_font_size == $i) $selected = 'selected';
					else $selected = '';
					echo '<option value="'.$i.'" '.$selected.' >'.$i.'px</option>';
				}
				?>
			</select><br>
			
			<input type="checkbox" name="mdocs-post-show-title" value="1"  <?php checked(1,$mdocs_post_show_title) ?> /> <span><?php _e('Show Post Title', 'memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-override-post-title-font-size" value="1"  <?php checked(1,$mdocs_override_post_title_font_size) ?> /> <span><?php _e('Override Post Title Font Size', 'memphis-documents-library'); ?></span><br>
			<span><?php _e('Post Page Title Font Size', 'memphis-documents-library'); ?></span>
			<select name="mdocs-post-title-font-size" id="mdocs-post-title-font-size" disabled>
				<?php
				for($i=3; $i < 51; $i++) {
					if($mdocs_post_title_font_size == $i) $selected = 'selected';
					else $selected = '';
					echo '<option value="'.$i.'" '.$selected.' >'.$i.'px</option>';
				}
				?>
			</select><br>
			<h5><?php _e('Post Options','memphis-documents-library'); ?></h5>
			<input type="checkbox" id="mdocs-hide-all-posts-non-members" name="mdocs-hide-all-posts-non-members" value="1"  <?php checked(1,$mdocs_hide_all_posts_non_members) ?>/> <?php _e('Hide All Posts: (Non Members)','memphis-documents-library'); ?><br>
			<input type="checkbox" id="mdocs-hide-all-posts" name="mdocs-hide-all-posts" value="1"  <?php checked(1,$mdocs_hide_all_posts) ?>/> <?php _e('Hide All Posts','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-remove-posts-from-homepage" value="1"  <?php checked(1,$mdocs_remove_posts_from_homepage) ?> /> <span><?php _e('Hide All Post From Homepage', 'memphis-documents-library'); ?></span><br>
			<span><?php _e('Post Page Target Type', 'memphis-documents-library'); ?></span>
			<select name="mdocs-post-target-type" id="mdocs-post-target-type" >
				<option value="" <?php if(get_option('mdocs-post-target-type') == '') echo 'selected'; ?> ><?php _e('not set', 'memphis-documents-library'); ?></option>
				<option value="_blank" <?php if(get_option('mdocs-post-target-type') == '_blank') echo 'selected'; ?> >_blank</option>
				<option value="_self" <?php if(get_option('mdocs-post-target-type') == '_self') echo 'selected'; ?> >_self</option>
				<option value="_parent" <?php if(get_option('mdocs-post-target-type') == '_parent') echo 'selected'; ?> >_parent</option>
				<option value="_top" <?php if(get_option('mdocs-post-target-type') == '_top') echo 'selected'; ?>>_top</option>
			</select>
		</td>
		<th>
		</th>
		<td>
			<h5><?php _e('Dashboard Options','memphis-documents-library'); ?></h5>
			<input type="checkbox" id="mdocs-show-post-menu" name="mdocs-show-post-menu" value="1"  <?php checked(1,$mdocs_show_post_menu) ?>/> <?php _e('Show Memphis Posts Menu','memphis-documents-library'); ?><br>
			<input type="checkbox" id="mdocs-show-media-files" name="mdocs-show-media-files" value="1"  <?php checked(1,$mdocs_show_media_files) ?>/> <?php _e('Show Memphis Files in Media Menu','memphis-documents-library'); ?><br>
			<h5><?php _e('File Options','memphis-documents-library'); ?></h5>
			<input type="checkbox" id="mdocs-hide-all-files-non-members" name="mdocs-hide-all-files-non-members" value="1"  <?php checked(1,$mdocs_hide_all_files_non_members) ?>/> <?php _e('Hide All Files: (Non Members)','memphis-documents-library'); ?><br>
			<input type="checkbox" id="mdocs-hide-all-files" name="mdocs-hide-all-files" value="1"  <?php checked(1,$mdocs_hide_all_files) ?>/> <?php _e('Hide All Files','memphis-documents-library'); ?><br>
			<h5><?php _e('Document Post Page Settings','memphis-documents-library'); ?></h5>
			<input type="checkbox" id="mdocs-show-description" name="mdocs-show-description" value="1"  <?php checked(1,$mdocs_show_description) ?>/> <?php _e('Show Description Tab','memphis-documents-library'); ?><br>
			<input type="checkbox" id="mdocs-show-preview" name="mdocs-show-preview" value="1"  <?php checked(1,$mdocs_show_preview) ?>/> <?php _e('Show Preview Tab','memphis-documents-library'); ?><br>
			<input type="checkbox" id="mdocs-show-versions" name="mdocs-show-versions" value="1"  <?php checked(1,$mdocs_show_versions) ?>/> <?php _e('Show Versions Tab','memphis-documents-library'); ?><br>
			<label><?php _e('Default Content:','memphis-documents-library'); ?></label>
			<select name="mdocs-default-content" id="mdocs-default-content">
				<option value=""><?php _e('None', 'memphis-documents-library'); ?></option>
				<option id="mdocs-post-content-description" value="description" <?php if($mdocs_default_content == 'description') echo 'selected'; ?> <?php if($mdocs_show_description == false) echo 'disabled'; ?> ><?php _e('Description Tab','memphis-documents-library'); ?></option>
				<option id="mdocs-post-content-preview" value="preview" <?php if($mdocs_default_content == 'preview') echo 'selected'; ?> <?php if($mdocs_show_preview == false) echo 'disabled'; ?> ><?php _e('Preview Tab','memphis-documents-library'); ?></option>
				<option id="mdocs-post-content-versions" value="versions" <?php if($mdocs_default_content == 'versions') echo 'selected'; ?> <?php if($mdocs_show_versions == false) echo 'disabled'; ?> ><?php _e('Versions Tab','memphis-documents-library'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr>
		<th><?php _e('New & Updated Banner','memphis-documents-library'); ?></th>
		<td>
			<input type="checkbox" id="mdocs-show-new-banners" name="mdocs-show-new-banners" value="1"  <?php checked(1,$mdocs_show_new_banners) ?>/> <?php _e('Show New & Updated Banner','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-hide-new-update-label" value="1"  <?php checked(1,get_option('mdocs-hide-new-update-label')) ?>/> <span><?php _e('Hide New and Updated Label', 'memphis-documents-library'); ?></span><br>
			<input class="width-30" type="text" id="mdocs-time-to-display-banners" name="mdocs-time-to-display-banners" value="<?php echo $mdocs_time_to_display_banners; ?>"/> <?php _e('days - Time to Displayed','memphis-documents-library'); ?><br>
		</td>
		<th><?php _e('Default Sort Options','memphis-documents-library'); ?></th>
		<td>
			<label><?php _e('Order Types:','memphis-documents-library'); ?>
				<select name="mdocs-sort-type" id="mdocs-sort-type" >
					<option value="name" <?php if($mdocs_sort_type == 'name') echo 'selected'; ?>><?php _e('File Name','memphis-documents-library'); ?></option>
					<option value="downloads" <?php if($mdocs_sort_type == 'downloads') echo 'selected'; ?>><?php _e('Number of Downloads','memphis-documents-library'); ?></option>
					<option value="version" <?php if($mdocs_sort_type == 'version') echo 'selected'; ?>><?php _e('Version','memphis-documents-library'); ?></option>
					<option value="owner" <?php if($mdocs_sort_type == 'owner') echo 'selected'; ?>><?php _e('Author','memphis-documents-library'); ?></option>
					<option value="modified" <?php if($mdocs_sort_type == 'modified') echo 'selected'; ?>><?php _e('Last Updated','memphis-documents-library'); ?></option>
					<option value="rating" <?php if($mdocs_sort_type == 'rating') echo 'selected'; ?>><?php _e('Rating','memphis-documents-library'); ?></option>
				</select>
			</label><br><br>
			<label><?php _e('Order Style:','memphis-documents-library'); ?>
				<select name="mdocs-sort-style" id="mdocs-sort-style" >
					<option value="desc" <?php if($mdocs_sort_style == 'desc') echo 'selected'; ?>><?php _e('Sort Descending','memphis-documents-library'); ?></option>
					<option value="asc" <?php if($mdocs_sort_style == 'asc') echo 'selected'; ?>><?php _e('Sort Ascending','memphis-documents-library'); ?></option>
				</select>
			</label><br><br>
			<input type="checkbox" id="mdocs-disable-user-sort" name="mdocs-disable-user-sort" value="1"  <?php checked(1,get_option('mdocs-disable-user-sort')) ?>/><span><?php _e('Disable User Sort','memphis-documents-library'); ?></span><br>
			<input type="checkbox" name="mdocs-hide-sortbar" value="1"  <?php checked(1,$mdocs_hide_sortbar) ?>/> <span><?php _e('Hide Sortbar', 'memphis-documents-library'); ?></span><br>
		</td>
	</tr>
	<tr>
		
		<td colspan="4">
			<h2><?php _e('Document List Configuration','memphis-documents-library'); ?> | <small><?php _e('Controls the style of the document list table', 'memphis-documents-library'); ?></small></h2>
			<table class="table table-striped">
				<tr>
					<th style="font-size: 11px;"><?php _e('Column Type', 'memphis-documents-library'); ?></th>
					<th style="font-size: 11px;"><?php _e('Order', 'memphis-documents-library'); ?></th>
					<th><?php _e('Width in %', 'memphis-documents-library'); ?></th>
					<th style="text-align: center;"><?php _e('Show in Table', 'memphis-documents-library'); ?></th>
					<!--
					<th style="text-align: center;"><?php _e('Show in Upload Form', 'memphis-documents-library'); ?></th>
					<th style="text-align: center;"><?php _e('Disabled in Upload Form', 'memphis-documents-library'); ?></th>
					<th><?php _e('Default Value', 'memphis-documents-library'); ?></th>
					-->
				</tr>
					<?php
					//get_option('mdocs-displayed-file-info')
					//mdocs_sort_file_info()
					foreach(mdocs_sort_file_info() as $key => $option) {
						//if($option['is-file-info']) {
							if($option['show']) $show_in_table_checked = "checked='checked'";
							else $show_in_table_checked = '';
							if($option['form-data']['show-in-form']) $show_in_form_checked = "checked='checked'";
							else $show_in_form_checked = '';
							if($option['form-data']['disabled-in-form']) $disabled_in_form_checked = "checked='checked'";
							else $disabled_in_form_checked = '';
							?>
							<tr>
								<td >
									<input type="hidden" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][key]" value="<?php echo $option['key']; ?>" />
									<input type="hidden" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][slug]" value="<?php echo $option['slug']; ?>" />
									<input type="hidden" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][text]" value="<?php echo $option['text']; ?>" />
									<input type="hidden" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][icon]" value="<?php echo $option['icon']; ?>" />
									<input type="hidden" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][color]" value="<?php echo $option['color']; ?>" />
									<input type="hidden" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][function]" value="<?php echo $option['function']; ?>" />
									<input type="hidden" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][is-form]" value="<?php echo $option['is-form']; ?>" />
									<input type="hidden" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][form-data][display-function]" value="<?php echo $option['form-data']['display-function']; ?>" />
									<input type="hidden" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][form-data][hide-function]" value="<?php echo $option['form-data']['hide-function']; ?>" />
									<?php
									if($option['text'] == null) echo ucfirst($option['slug']);
									elseif(isset($option['text-more'])) echo $option['text-more'];
									else echo ucfirst($option['text']);
									?>
								</td>
								<td><input type="text" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][order]" value="<?php echo $option['order']; ?>" /></td>
								<td><input type="text" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][width]" value="<?php echo $option['width']; ?>" /></td>
								<td style="text-align: center;"><input type="checkbox" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][show]" value="1"  <?php echo $show_in_table_checked; ?>/></td>
								<!--
								<td style="text-align: center;"><input type="checkbox" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][form-data][show-in-form]" value="1"  <?php echo $show_in_form_checked; ?>/></td>
								<td style="text-align: center;"><input type="checkbox" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][form-data][disabled-in-form]" value="1"  <?php echo $disabled_in_form_checked; ?>/></td>
								<td><input type="text" name="mdocs-displayed-file-info[<?php echo $option['key']; ?>][form-data][deafult]" value="<?php echo $option['form-data']['default']; ?>" /></td>
								-->
							</tr>
							<?php
						//}
					}
					?>
			</table>
		</td>
	</tr>
	<tr>
		<th><?php _e('Displayed File Information','memphis-documents-library'); ?><br><br><small><?php _e('Dropdown and Post Page Settings', 'memphis-documents-library'); ?></small></th>
		<td>
			<input type="checkbox" name="mdocs-show-social" value="1"  <?php checked( $mdocs_show_social, 1) ?>/> <?php _e('Show Social','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-share" value="1"  <?php checked( $mdocs_show_share, 1) ?>/>  <?php _e('Show Sharing Button/Link','memphis-documents-library'); ?>
		</td>
		<th><?php _e('Displayed File Properties','memphis-documents-library'); ?><br><br><small><?php _e('This hides input fields from users when they are uploading a file.','memphis-documents-library'); ?></small></th>
		<td>
			<input type="checkbox" name="mdocs-show-upload-folder" value="1"  <?php checked(1,$mdocs_show_upload_folder) ?>/> <?php _e('Show upload folder','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-version" value="1"  <?php checked(1,$mdocs_show_upload_version) ?>/> <?php _e('Show upload version','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-date" value="1"  <?php checked(1,$mdocs_show_upload_date) ?>/> <?php _e('Show upload date','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-file-status" value="1"  <?php checked(1,$mdocs_show_upload_file_status) ?>/> <?php _e('Show upload file status','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-post-status" value="1"  <?php checked(1,$mdocs_show_upload_post_status) ?>/> <?php _e('Show upload post status','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-show-social-apps" value="1"  <?php checked(1,get_option('mdocs-show-upload-show-social-apps')) ?>/> <?php _e('Show upload social','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-downloadable-by-non-members" value="1"  <?php checked(1,get_option('mdocs-show-upload-downloadable-by-non-members')) ?>/> <?php _e('Show upload non members','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-contributors" value="1"  <?php checked(1,$mdocs_show_upload_contributors) ?>/> <?php _e('Show upload contributors','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-tags" value="1"  <?php checked(1,$mdocs_show_upload_tags) ?>/> <?php _e('Show upload tags','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-categories" value="1"  <?php checked(1,get_option('mdocs-show-upload-categories')) ?>/> <?php _e('Show upload categories','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-description" value="1"  <?php checked(1,$mdocs_show_upload_description) ?>/> <?php _e('Show upload description','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-show-upload-real-author" value="1"  <?php checked(1,get_option('mdocs-show-upload-real-author')) ?>/> <?php _e('Show upload author','memphis-documents-library'); ?><br>
		</td>
	</tr>
	<tr>
		<th><?php _e('Widget Settings','memphis-documents-library'); ?></th>
		<td>
			<input type="checkbox" name="mdocs-hide-widget-titles" value="1"  <?php checked(1,get_option('mdocs-hide-widget-titles')) ?>/> <?php _e('Hide Widget Titles','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-hide-widget-column-titles" value="1"  <?php checked(1,get_option('mdocs-hide-widget-column-titles')) ?>/> <?php _e('Hide Widget Column Titles','memphis-documents-library'); ?><br>
			<input type="checkbox" name="mdocs-hide-widget-numbers" value="1"  <?php checked(1,get_option('mdocs-hide-widget-numbers')) ?>/> <?php _e('Hide Widget Numbers','memphis-documents-library'); ?><br>
		</td>
		<th></th>
		<td></td>
	</tr>
</table>

<input style="margin:15px;" type="submit" class="btn btn-primary" value="<?php _e('Save Changes','memphis-documents-library') ?>" />
</form>
<?php
}
?>