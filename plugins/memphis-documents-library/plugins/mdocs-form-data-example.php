<?php
// ADD THE ACTION HOOK WP LOADED TO REGISTER THE NEW FILE INFO
add_action('wp_loaded','mdocs_register_form_data_example');
function mdocs_register_form_data_example() {
	mdocs_delete_file_info('test');
	/*
	mdocs_add_file_info('test', array(
		'is-file-info' => false,
		'is-form' => true,
		'form-data' => array(
			'show-in-form' => true,
			'default' => 'hello world',
			'display-function' => 'mdocs_form_data_example_display',
			'hide-function' => 'mdocs_form_data_example_hide',
		),
	));
	*/
}
function mdocs_form_data_example_display() {
	
}
function mdocs_form_data_example_hide($default_value) {
	?>
	<input type=hidden name="mdocs-cat" value="<?php echo $default_value; ?>" >
	<?php
}
?>