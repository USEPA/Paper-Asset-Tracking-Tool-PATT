<?php
function mdocs_add_file_block() {
	// FONT AWESOME
	wp_enqueue_style(
		'font-awesome.min.css',
		'https://use.fontawesome.com/releases/v5.2.0/css/all.css',
		array(),
		MDOCS_SESSION_ID
	);
	// ADD FILE SCRIPT
    wp_register_script(
		'mdocs-add-file-block',
		plugins_url( 'mdocs-add-file-block.js',__FILE__),
		//plugins_url( 'mdocs-example.js',__FILE__),
		array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-editor' ),
		MDOCS_SESSION_ID
	);
	// ADD FILE STYLE FRONT END
	wp_register_style(
		'mdocs-add-file-block-fe', 
		plugins_url( 'mdocs-add-file-block-fe.css', __FILE__ ), 
		array(), 
		MDOCS_SESSION_ID
	);
	// REGISTER ALL GUTENBERG SCRIPTS AND STYLES
    register_block_type( 'gutenberg/mdocs-add-file-block', array(
	   'editor_script' => 'mdocs-add-file-block',
		//'style' => 'mdocs-add-file-block-fe',
    ) );
	
	
	register_block_type( 'gutenberg/mdocs-gutenberg-get-files', array(
		'render_callback' => 'mdocs_gutenberg_get_files',
		 'attributes' => array(
			'files' => array(
				'type' => 'string',
			),
			'file_list' => array(
				'type' => 'string',
			),
		),
    ) );
    
}
//add_action( 'init', 'mdocs_add_file_block' );


function mdocs_gutenberg_get_files($attributes, $content) {
	ob_start();
	//echo '<select>';
	foreach(get_option('mdocs-list') as $index => $file) {
		//echo '<option value="'.$file['id'].'" > '.stripcslashes($file['name']).'</small></option>';
		echo '<input id="inspector-radio-control-0-0" class="components-radio-control__input" type="radio" name="inspector-radio-control-0" value="'.$file['id'].'"> '.stripcslashes($file['name']).'<br>';
	}
	//echo '</select>';
	$the_list = ob_get_clean();
	
	
	return $the_list;
}
?>