<?php
// Shortcode: [mdocs_upload_btn] - Creates a upload button for the front end.
add_shortcode( 'mdocs_upload_btn', 'mdocs_shortcode_upload' );
function mdocs_shortcode_upload($att, $content=null) { return mdocs_upload_button($att); }
// Shortcode: [mdocs] - Used in list.
add_shortcode( 'mdocs', 'mdocs_shortcode' );
function mdocs_shortcode($att, $content=null) { return mdocs_the_list($att); }
// Shortcode: [mdocs_post_page] - Used with a mdocs post page.
add_shortcode( 'mdocs_post_page', 'mdocs_post_page_shortcode' );
function mdocs_post_page_shortcode($att, $content=null) { return mdocs_post_page($att); }
// Shortcode: [mdocs_upload_btn] - Creates a upload button for the front end.
add_shortcode( 'mdocs_media_attachment', 'mdocs_shortcode_media_attachment' );
function mdocs_shortcode_media_attachment($att, $content=null) { return null; }
// Shortcode Setting Page
function mdocs_shortcodes($current_cat) {
	mdocs_list_header();
	?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Short Codes','memphis-documents-library'); ?></h3>
		</div>
		<div class="panel-body">
			<table class="table table-hover table-striped table-bordered" >
				<tr>
					<th><?php _e('Short Codes','memphis-documents-library');?></th>
					<th><?php _e('Description','memphis-documents-library');?></th>
				</tr>
				<tr>
					<td>[mdocs]</td>
					<td><?php _e('Adds the default Memphis Documents Library file list to any page, post or widget.','memphis-documents-library');?></td>
				</tr>
				<tr>
					<td>[mdocs cat="<?php _e('The Category Name','memphis-documents-library');?>"]</td>
					<td><?php _e('This still works but you are better off using the "Add mDocs Folder" button, found at the top of the page editor.', 'memphis-documents-library'); echo '<br><br>'; _e('Adds files from  a specific folder of the Memphis Documents Library on any page, post or widget.','memphis-documents-library');?></td>
				</tr>
				<tr>
					<td>[mdocs cat="All Files"]</td>
					<td><?php _e('This still works but you are better off using the "Add mDocs Folder" button, found at the top of the page editor.', 'memphis-documents-library'); echo '<br><br>'; _e('Adds a list of all files of the Memphis Documents Library on any page, post or widget.','memphis-documents-library');?></td>
				</tr>
				<tr>
					<td>[mdocs single-file="<?php _e('Enter the file name.','memphis-documents-library'); ?>"]</td>
					<td><?php _e('This still works but you are better off using the "Add mDocs File" button, found at the top of the page editor.', 'memphis-documents-library'); echo '<br><br>'; _e('Adds a single file to any post, page or widget.','memphis-documents-library');?></td>
				</tr>
				<tr>
					<td>[mdocs_upload_btn]</td>
					<td><?php _e('For more details on options for this button please check out','memphis-documents-library');?> <a href="http://kingofnothing.net/frontend-upload-button-help" target="_blank">http://kingofnothing.net/frontend-upload-button-help/</a>.</td>
				</tr>
			</table>
		</div>
	</div>
	<?php
}
//add_action('media_buttons', 'mdocs_add_media_buttons', 15);
add_action('admin_footer', 'mdocs_add_media_buttons', 15);
function mdocs_add_media_buttons() {
	add_thickbox();
    ?>
	<!--
	<a href="#TB_inline?&width=400&height=300&inlineId=mdocs-add-folder-modal" class="thickbox button"><?php _e('Add mDocs Folder','memphis-documents-library'); ?></a>
	-->
	<div id="mdocs-add-folder-modal" style="display:none;">
		<form name="mdocs-insert-shortcode-form" class="mdocs-insert-shortcode-form " method="post">
			<button type="button" class="button mdocs-insert-shortcode" data-button-type="insert-folder"><?php _e('Insert Short Code','memphis-documents-library'); ?></button><br><br>
			<label>Select the folder:</label>
			<select name="mdocs-folders">
			<?php
			echo '<option value="all" >All Files</option>';
			$folders = get_option('mdocs-cats');
			mdocs_display_folder_options_menu($folders);
			?>
			</select><br><br>
			<label>Select the sort type:</label>
			<select name="mdocs-sort-values" >
			<?php
			$show_options = get_option('mdocs-displayed-file-info');
			echo '<option value="default" >Default</option>';
			foreach($show_options as $index => $the_option) {
				if(isset($the_option['show']) && $the_option['show'] == '1') {
					echo '<option value='.$the_option['slug'].' >'.$the_option['text'],'</option>';
				}
			}
			?>
			</select>
			<label>Descending:</label> <input type="checkbox" name="mdocs-add-folder-is-descending" checked><br><br>
			<label>Hide Folders:</label> <input type="checkbox" name="mdocs-add-folder-hide-folders" checked><br><br>
			<button type="button" class="button mdocs-insert-shortcode" data-button-type="insert-folder"><?php _e('Insert Short Code','memphis-documents-library'); ?></button>
		</form>
	</div>
	<!--
	<a href="#TB_inline?&width=400&height=300&inlineId=mdocs-add-file-modal" class="thickbox button"><?php _e('Add mDocs Folder','memphis-documents-library'); ?></a>
	-->
	<div id="mdocs-add-file-modal" style="display:none;">
		<form name="mdocs-insert-shortcode-form" class="mdocs-insert-shortcode-form" method="post">
			<br>
			<button type="button" class="button mdocs-insert-shortcode" data-button-type="insert-file"><?php _e('Insert Short Code','memphis-documents-library'); ?></button>
			  <h2><?php _e('Select a file', 'memphis-documents-library'); ?></h2>
			  <?php
			  foreach(get_option('mdocs-list') as $index => $file) {
				echo '<input type="radio" name="mdocs-file" value="'.$file['id'].'" > '.stripcslashes($file['name']).' - <small>'.stripcslashes($file['filename']).'</small><br>';
			  }
			  ?>
			<br>
			<button type="button" class="button mdocs-insert-shortcode" data-button-type="insert-file"><?php _e('Insert Short Code','memphis-documents-library'); ?></button>
		 </form>
	</div>
		
	
	<!--
	<button id="mdocs-add-upload-btn" class="button" data-toggle="modal" data-target="#mdocs-add-upload-btn-modal" data-backdrop="false"><i class="fa fa-folder-open-o" aria-hidden="true"></i> <?php _e('Add mDocs Upload Button','memphis-documents-library'); ?></button>
	<div class="modal fade" id="mdocs-add-upload-btn-modal" tabindex="-1" role="dialog" aria-labelledby="mdocs-add-upload-btn-label">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal" aria-label="<?php _e('Close','memphis-documents-library'); ?>"><span aria-hidden="true">&times;</span></button>
				  <h4 class="modal-title" id="mdocs-add-file-label"><?php _e('Add Upload Button','memphis-documents-library'); ?></h4>
				</div>
				<div class="modal-body">
				  <form name="mdocs-insert-shortcode-form" class="mdocs-insert-shortcode-form" method="post">
					  <h2><?php _e('Select a file', 'memphis-documents-library'); ?></h2>
					  <?php
					  foreach(get_option('mdocs-list') as $index => $file) {
						echo '<input type="radio" name="mdocs-files" value="'.$file['id'].'" > '.$file['name'].' - <small>'.$file['filename'].'</small><br>';
					  }
					  ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','memphis-documents-library'); ?></button>
					<button type="button" class="btn btn-primary mdocs-insert-shortcode" data-button-type="insert-file"><?php _e('Insert Short Code','memphis-documents-library'); ?></button>
				</div>
				</form>
			</div>
		</div>
	</div>
	-->
	<?php
}
/*
add_action('wp_enqueue_media', 'mdocs_media_buttons_js');
function mdocs_media_buttons_js() {
	if(MDOCS_DEV) {
		$media_buttons_js = 'mdocs-post-editor.js';
	} else {
		$media_buttons_js = 'mdocs-post-editor.min.js';
	}
    wp_enqueue_script('mdocs-media-buttons.js', MDOCS_URL.$media_buttons_js, array('jquery'), '1.0', true);
	mdocs_js_handle('mdocs-media-buttons.js');
}
*/

add_action( 'admin_init', 'mdocs_editor_buttons' );
function mdocs_editor_buttons() {
	if(get_user_option( 'rich_editing' ) == 'true' ) {
		add_action( "admin_head", 'mdocs_tinymce_localization' );
		add_filter( 'mce_buttons', 'mdocs_register_tinymce_buttons' );
		add_filter( 'mce_external_plugins', 'mdocs_add_tinymce_buttons' );
    }
}
function mdocs_register_tinymce_buttons( $buttons ) {
	array_push( $buttons, 'mdocs_shortcode_dropdown' );
	return $buttons;
}
function mdocs_add_tinymce_buttons( $plugin_array ) {
	if(MDOCS_DEV) $media_buttons_js = 'mdocs-post-editor.js';
	else $media_buttons_js = 'mdocs-post-editor.min.js';
	$plugin_array['mdocs_shortcode_dropdown'] = MDOCS_URL.'/'.$media_buttons_js;
	return $plugin_array;
}
function mdocs_tinymce_localization() {
	$localization = mdocs_js_handle();
	$mdocs_js = "";
	foreach($localization as $index => $var) {
		$var = trim(preg_replace('/\s\s+/', ' ', $var));
		$mdocs_js .= "\t".$index.": '".addslashes($var)."',\n";
	}
?>
<script type='text/javascript'>
var mdocs_js = {
<?php echo $mdocs_js; ?>
};
</script>
<?php
}
?>