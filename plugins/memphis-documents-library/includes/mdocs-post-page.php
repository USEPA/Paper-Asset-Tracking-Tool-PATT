<?php

/**
 * Creates the Memphis Documents Library post type.
 */
function mdocs_init_post_pages() {
	$labels = array(
		'name'               => __( 'Memphis Documents Posts', 'mdocs' ),
		'singular_name'      => _x( 'mdocs', 'mdocs' ),
		'add_new'            => __( 'Add New', 'mdocs' ),
		'add_new_item'       => __( 'Add New Documents', 'mdocs' ),
		'edit_item'          => __( 'Edit Documents', 'mdocs' ),
		'new_item'           => __( 'New Documents', 'mdocs' ),
		'all_items'          => __( 'All Documents', 'mdocs' ),
		'view_item'          => __( 'View Documents', 'mdocs' ),
		'search_items'       => __( 'Search Documents', 'mdocs' ),
		'not_found'          => __( 'No documents found', 'mdocs' ),
		'not_found_in_trash' => __( 'No documents found in the Trash', 'mdocs' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'mDocs Posts'
	);
	$show_mdocs_post_menu = get_option('mdocs-show-post-menu');
	$supports = array( 'title', 'editor','author','comments','revisions','page-attributes','post-formats', 'excerpt', 'trackbacks', 'custom-fields', 'thumbnail'  );
	$args = array(
		'labels'              		=> $labels,
		'public'              		=> true,
		'publicly_queryable'  => true,
		'show_ui'             	=> $show_mdocs_post_menu,
		'show_in_menu' 		=> true,
		'query_var'           	=> true,
		'rewrite'             		=> array( 'slug' => 'mdocs-posts' ),
		'capability_type'     	=> 'page',
		'has_archive'         	=> true,
		'hierarchical'        	=> false,
		'menu_position'       => 5,
		'taxonomies' 			=> array('post_tag'),
		'supports'            		=> $supports,
	 );
	register_post_type( 'mdocs-posts', $args );
}
function mdocs_post_page($att=null) {
	global $post;
	if(@$post->post_type = 'mdocs-posts') {
		ob_start();
		$the_mdoc = get_the_mdoc_by($post->ID, 'parent');
		$the_page = '<div class="mdocs mdocs-post mdocs-post-current-file">';
		$the_page .= mdocs_display_file_info_large($the_mdoc, $att);
		$the_page .= '<div class="mdocs-clear-both"></div>';
		$the_page .= mdocs_social($the_mdoc);
		$the_page .= '</div>';
		$the_page .= '<div class="mdocs-clear-both"></div>';
		$the_page .= mdocs_display_tabs($the_mdoc);
		$the_page .= '<div class="mdocs-clear-both"></div>';
		$the_page .= '</div>';
		$the_page .= ob_get_clean();
		if(mdocs_check_post_rights($the_mdoc)) return $the_page;
		else return '<p class="alert alert-info text-center clearfix">'.__('Sorry you can\'t see this Memphis Documents file you don\'t have the proper permission.','memphis-documents-library').'</p>';
	} 
}
function mdocs_display_file_info_large($the_mdoc, $att=null) {
	global $post;
	ob_start();
	$the_mdoc_permalink = mdocs_sanitize_string(get_permalink($the_mdoc['parent']));
	$the_post = get_post($the_mdoc['parent']);
	if(isset($att['new']) && $att['new']) $is_new = true;
	else $is_new = false;
	$date_format = get_option('mdocs-date-format');
	$the_date = mdocs_format_unix_epoch($the_mdoc['modified']);
	$user_logged_in = is_user_logged_in();
	$mdocs_show_non_members = $the_mdoc['non_members'];
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_hide_all_posts = get_option( 'mdocs-hide-all-posts' );
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$sa = mdocs_get_table_atts();
	if($sa['show-downloads']['show']) {
		$the_array = get_option('mdocs-displayed-file-info');
		$mdocs_show_downloads =  $the_array['show-downloads']['show'];
	} else $mdocs_show_downloads = '';
	if($sa['show-author']['show']) {
		$the_array = get_option('mdocs-displayed-file-info');
		$mdocs_show_author = $the_array['show-author']['show'];
	} else $mdocs_show_author = '';
	if($sa['show-version']['show']) {
		$the_array = get_option('mdocs-displayed-file-info');
		$mdocs_show_version = $the_array['show-version']['show'];
	} else $mdocs_show_version = '';
	if($sa['show-modified']['show']) {
		$the_array = get_option('mdocs-displayed-file-info');
		$mdocs_show_update = $the_array['show-modified']['show'];
	} else $mdocs_show_update = '';
	if($sa['show-rating']['show']) {
		$the_array = get_option('mdocs-displayed-file-info');
		$mdocs_show_ratings = $the_array['show-rating']['show'];
	} else $mdocs_show_ratings = '';
	$mdocs_show_new_banners = get_option('mdocs-show-new-banners');
	$mdocs_time_to_display_banners = get_option('mdocs-time-to-display-banners');
	$post_status = $the_post->post_status;
	$permalink = mdocs_get_permalink($post);
	if($the_mdoc['show_social'] ==='on' && get_option('mdocs-show-social') ) mdocs_social_scripts();
	$the_rating = mdocs_get_rating($the_mdoc);
	
	
	
	$modified = floor($the_mdoc['modified']/86400)*86400;
	$today = floor(time()/86400)*86400;
	$days = (($today-$modified)/86400);
	$new_or_updated = '';
	if($mdocs_time_to_display_banners > $days) {
		if($is_new == true) {
			if($mdocs_show_new_banners) echo '<div class="alert alert-success clearfix"><p class="text-center">'.__('New','memphis-documents-library').'</p></div>';
			if(get_option('mdocs-hide-new-update-label')) $new_or_updated = '';
			else $new_or_updated = '<small class="label label-success">'.__('New', 'memphis-documents-library').'</small> ';
		} else {
			if($mdocs_show_new_banners) echo '<div class="alert alert-info clearfix"><p class="text-center">'.__('Updated','memphis-documents-library').'</p></div>';
			if(get_option('mdocs-hide-new-update-label')) $new_or_updated = '';
			else $new_or_updated = '<small class="label label-info">'.__('Updated', 'memphis-documents-library').'</small> ';
		}
	} else  $status_class = 'mdocs-normal';

	?>
	<div class="mdocs-development-warning"></div>
	<div class="mdocs-post-header" data-mdocs-id="<?php echo $the_mdoc['id']; ?>">
	<div class="mdocs-post-button-box">
		<?php
		echo $new_or_updated;
		if(mdocs_check_file_rights($the_mdoc) == false && $user_logged_in) { ?><a class="mdocs-login-msg btn btn-primary"><small><?php _e('This file can not<br>be downloaded.','memphis-documents-library'); ?></small></a><?php }
		else if($mdocs_show_non_members  == 'off' && $user_logged_in == false || $user_logged_in == false && $mdocs_hide_all_files_non_members) { ?>
			<a class="mdocs-download-btn mdocs-float-right btn btn-primary" href="<?php echo wp_login_url($permalink); ?>" ><small><?php echo __('Please Login to download this file','memphis-documents-library'); ?></small></a>
		<?php } elseif($the_mdoc['non_members'] == 'on' || $user_logged_in ) { ?>
			<a onclick="mdocs_download_file('<?php echo $the_mdoc['id']; ?>','<?php  echo $the_post->ID; ?>');" class="mdocs-download-btn mdocs-float-right btn btn-primary" value=""><?php echo __('Download','memphis-documents-library'); ?></a>
		<?php } else { ?>
			<a class="mdocs-download-btn mdocs-float-right btn btn-primary" href="<?php echo wp_login_url($permalink); ?>" ><small><?php echo __('Please Login to download this file','memphis-documents-library'); ?></small></a>
		<?php } ?>
	</div>
	<?php
	if($user_logged_in && $mdocs_show_ratings) {
			if($the_rating['your_rating'] == 0) $text = __("Rate Me!");
			else $text = __("Your Rating");
			echo '<div class="mdocs-rating-container-small">';
			echo '<div class="mdocs-green">'.$text.'</div><div id="mdocs-star-container">';
			echo '<div class="mdocs-ratings-stars" data-my-rating="'.$the_rating['your_rating'].'">';
			for($i=1;$i<=5;$i++) {
				if($the_rating['your_rating'] >= $i) echo '<i class="fas fa-star fa-xs  mdocs-gold mdocs-my-rating" id="'.$i.'" aria-hidden="true"></i>';
				else echo '<i class="far fa-star fa-xs mdocs-my-rating" id="'.$i.'" aria-hidden="true"></i>';
			}
			echo '</div></div></div>';
		}
	
		?>
	<div class="mdocs-post-file-info">
		<!--<p><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Back to Document Library</p>-->
		<?php if($mdocs_show_ratings) { ?><p><i class="fas fa-star fa-xs" aria-hidden="true"></i> <?php echo $the_rating['average']; ?> <?php _e('Stars', 'memphis-documents-library'); ?> (<?php echo $the_rating['total']; ?>)</p> <?php } ?>
		<?php if($mdocs_show_downloads) { ?><p class="mdocs-file-info"><i class="fas fa-cloud-download-alt" aria-hidden="true"></i> <em class="mdocs-orange"><?php echo $the_mdoc['downloads'].' '.__('Downloads','memphis-documents-library'); ?></em></p> <?php } ?>
		<?php if($mdocs_show_author) { ?><p><i class="fa fa-pencil-alt" aria-hidden="true"></i> <?php _e('Owner','memphis-documents-library'); ?>: <i class="mdocs-green"><?php echo get_user_by('login', $the_mdoc['owner'])->display_name; ?></i></p> <?php } ?>
		<?php if($mdocs_show_version) { ?><p><i class="fa fa-history" aria-hidden="true"></i> <?php _e('Version','memphis-documents-library') ?>:  <em class="mdocs-blue"><?php echo $the_mdoc['version']; ?></em>
		</p><?php } ?>
		<?php if($mdocs_show_update) { ?><p><i class="fa fa-calendar" aria-hidden="true"></i> <?php _e('Last Updated','memphis-documents-library'); ?>: <em class="mdocs-red"><?php echo date($date_format, $the_date['date']); ?></em></p><?php } ?>
		<?php if(is_admin()) { ?>
		<p><i class="fa fa-file " aria-hidden="true"></i> <?php echo __('File Status','memphis-documents-library').': <em class="mdocs-olive">'.strtoupper($the_mdoc['file_status']).'</em>'; ?></p>
		<p><i class="fa fa-file-text" aria-hidden="true"></i> <?php echo __('Post Status','memphis-documents-library').': <em class="mdocs-salmon">'.strtoupper($post_status).'</em>'; ?></p>
		<?php } ?>
	</div>
	</div>
<?php
		$the_page = ob_get_clean();
		return $the_page;
}
?>