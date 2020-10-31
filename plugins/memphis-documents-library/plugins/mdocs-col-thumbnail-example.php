<?php
// ADD THE ACTION HOOK WP LOADED TO REGISTER THE NEW FILE INFO
add_action('wp_loaded','mdocs_register_example');
// THE FUNCTION TIED TO THE HOOK
function mdocs_register_example() {
	// EXAMPLE ADD, UPDATE AND DELETE
	// ADD FILE INFO
	// Add Function Parameters (Array Key, Args)
	/*Arg Values:
	 *text => (string) Column Title,
	 *icon => (string) Font Awesome Icon,
	 *color => (string) Text Color,
	 *func => (string) Callback Function,
	 *order => (int) Position in table,
	 *width => (int) Width in Percentage,
	 *show => (boolean) Show By Default,
	 */
	mdocs_add_file_info('thumbnails', array(
		'text' => 'Thumbnails',
		'func' => 'mdocs_display_thumbnail',
		'order' =>11,
		'width' => 9
	));
	// UPDATE FILE INFO
	// Update Function Parameters (Array Key, Args)
	/*Arg Values:
	 *text => (string) Column Title,
	 *icon => (string) Font Awesome Icon,
	 *color => (string) Text Color,
	 *func => (string) Callback Function,
	 *order => (int) Position in table,
	 *width => (int) Width in Percentage,
	 *show => (boolean) Show By Default,
	 */
	/*
	mdocs_update_file_info('thumbnails', array(
		'text' => 'Thumbnails',
		'func' => 'mdocs_display_thumbnail',
		'order' =>11,
		'width' => 9
	));
	// DELETE FILE INFO
	mdocs_delete_file_info('thumbnails');
	*/
	mdocs_update_file_info('thumbnails', array('text' => '', 'text-more' => 'Thumbnails (experimental may cause system slowness.)',) );
}
// CALLBACK FOR THE NEW COLUMN
function mdocs_display_thumbnail($the_mdoc) {
	if(get_option('mdocs-box-view-key') != '' && get_option('mdocs-preview-type') == 'box') {
		$boxview = new mdocs_box_view();
		?>
		<img class="img-thumbnail mdocs-img-preview img-responsive" src="<?php $boxview->getThumbnail($the_mdoc['box-view-id'], $the_mdoc); ?>" alt="<?php echo $the_mdoc['filename']; ?>" />
		<?php
	} else {
		
		if($the_mdoc['type'] == 'pdf' && class_exists('imagick')) {
			//var_dump($the_mdoc);
			$upload_dir = wp_upload_dir();
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
		} else {
		?>
		<img class="img-thumbnail mdocs-img-preview img-responsive" src="<?php echo site_url(); ?>/?mdocs-img-preview=<?php echo $the_mdoc['filename']; ?>" />
		<?php
		}
	}
}
?>