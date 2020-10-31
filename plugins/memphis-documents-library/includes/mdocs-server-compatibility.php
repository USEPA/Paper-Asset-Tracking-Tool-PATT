<?php
function mdocs_server_compatibility() {
	mdocs_list_header();
	$success = '<i class="fa fa-check fa-2x text-success" aria-hidden="true"></i>';
	$fail = '<i class="fa fa-times fa-2x text-danger" aria-hidden="true"></i>';
	// PHP VERSION SUPPORT
	if(version_compare(PHP_VERSION, '5.5.9') >= 0)  {
		$php_version_result = $success;
		$php_version_info = __('Your PHP Version is:', 'memphis-documents-library').'<h5>'.PHP_VERSION.'</h5>';
	} else {
		$php_version_result = $fail;
		$php_version_info = __('Your PHP version is not support by Memphis Documents Library.', 'memphis-documents-library').'<br>'.__('You may experience errors will using this plugin.', 'memphis-documents-library').'<br><h5>'.PHP_VERSION.'</h5>';
	}
	// PHP IMAGE PROCESSING AND GD
	if(function_exists('imagecreatefromjpeg'))  {
		$php_gd_result = $success;
		$php_gd_info = '';
	} else {
		$php_gd_result = $fail;
		$php_gd_info = __('Document thumbnails will not work.', 'memphis-documents-library').'<br><a href="http://php.net/manual/en/image.installation.php" target="_blank">http://php.net/manual/en/image.installation.php</a>';
	}
	// MAXIM INPUT VARS
	if(intval(ini_get("max_input_vars")) >= 1000) {
		$input_vars_result = $success;
		$input_vars_info = __('The recommended value is 1000.','memphis-documents-library').'<br>'.__('If you have lots of folders it is recommend to increasing this value.', 'memphis-documents-library').'<br><a href ="http://php.net/manual/en/info.configuration.php#ini.max-input-vars" target="_blank">http://php.net/manual/en/info.configuration.php#ini.max-input-vars</a>';
	} else {
		$input_vars_result = $fail;
		$input_vars_info = __('The recommended value is 1000.','memphis-documents-library').'<br>'.__('If you have lots of folders we recommend increasing this value.', 'memphis-documents-library').'<br><a href="http://php.net/manual/en/info.configuration.php#ini.max-input-vars" target="_blank">http://php.net/manual/en/info.configuration.php#ini.max-input-vars</a>';
	}
	// DATE TIME METHOH PHP 5.3 AND HIGHER
	if(!method_exists('DateTime', 'createFromFormat')) {
		$datetime_method_result = $fail;
		$datetime_method_info = __('PHP version 5.3 or greater is required to be able to modify dates.', 'memphis-documents-library').'<br><a href="http://php.net/manual/en/migration53.php" target="_blank">http://php.net/manual/en/migration53.php</a>';
	} else {
		$datetime_method_result = $success;
		$datetime_method_info = '';
	}
	// ZIPARCHIVE INSTALLED
	if(!class_exists('ZipArchive')) {
		$zip_archive_method_result = $fail;
		$zip_archive_method_info = __('ZipArchive is used in import, export and batch upload, it needs to be install in order for these to function.', 'memphis-documents-library').'<br><a href="http://php.net/manual/en/imagick.setup.php" target="_blank">http://php.net/manual/en/imagick.setup.php</a>';
	} else {
		$zip_archive_method_result = $success;
		$zip_archive_method_info = '';
	}
	// IMAGICK INSTALL
	if(!class_exists('imagick')) {
		$imagick_method_result = $fail;
		$imagick_method_info = __('Imagick is used to create pdf thumbnails.', 'memphis-documents-library').'<br><a href="http://php.net/manual/en/imagick.setup.php" target="_blank">http://php.net/manual/en/imagick.setup.php</a>';
	} else {
		$imagick_method_result = $success;
		$imagick_method_info = '';
	}
	// WORDPRESS UPLOAD DIRECTORY READ WRITE ACCESS
	if(mdocs_check_read_write() == false) {
		$upload_dir = wp_upload_dir();
		$upload_dir_read_write_result = $fail;
		$upload_dir_read_write_info = __('The WordPress upload directory:', 'memphis-documents-library').'<h5>'.$upload_dir['basedir'].'/mdocs/</h5>'.__(' is not read/writeable.  Memphis Documents Library will not work with this access.', 'memphis-documents-library').'<br><a href="https://codex.wordpress.org/Changing_File_Permissions" target="_blank">https://codex.wordpress.org/Changing_File_Permissions</a>';
	} else {
		$upload_dir = wp_upload_dir();
		$upload_dir_read_write_result = $success;
		$upload_dir_read_write_info = __('The Current WordPress upload directory for Memphis Document Library:', 'memphis-documents-library').'<h5>'.$upload_dir['basedir'].'/mdocs/</h5>';
	}
	// CURL INSTALL
	if(function_exists('curl_version'))  {
		$php_curl_result = $success;
		$php_curl_info = '';
	} else {
		$php_curl_result = $fail;
		$php_curl_info = __('Ajax functionality will not work, problems with displaying descriptions and previews will occur.', 'memphis-documents-library').'<br><a href="http://php.net/manual/en/curl.installation.php" target="_blank">http://php.net/manual/en/curl.installation.php</a>';
	}
	// HAS SYSTEM TEMP DIRECTORY
	if(sys_get_temp_dir() != null) {
		$temp_dir_result = $success;
		$temp_dir_info = '';
	} else {
		$temp_dir_result = $fail;
		$temp_dir_info = __('Memphis Documents Library needs a system temp directory to work.<br>Please look at you php.ini files value upload_tmp_dir.<br>You can also set a system temp directory from your servers settings as well..','memphis-documents-library');
	}
?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Memphis Documents: Server Compatiability Check', 'memphis-documents-library'); ?></h3>
		</div>
		<div class="panel-body">
			<table class="table table-hover table-striped table-bordered" >
				<tr>
					<th><?php _e('System Requirements','memphis-documents-library');?></th>
					<th class="text-center"><?php _e('Results','memphis-documents-library');?></th>
					<th><?php _e('Information','memphis-documents-library');?></th>
				</tr>
				<tr>
					<td><?php _e('PHP Version Support 5.5.9 or greater', 'memphis-documents-library');?></td>
					<td class="text-center"><?php echo $php_version_result; ?></td>
					<td><?php echo $php_version_info; ?></td>
					<td></td>
				</tr>
				<tr>
					<td><?php _e('Wordpres upload direcotry read/write access', 'memphis-documents-library');?></td>
					<td class="text-center"><?php echo $upload_dir_read_write_result; ?></td>
					<td><?php echo $upload_dir_read_write_info; ?></td>
					<td></td>
				</tr>
				<tr>
					<td><?php _e('PHP Image Processing and GD', 'memphis-documents-library');?></td>
					<td class="text-center"><?php echo $php_gd_result; ?></td>
					<td><?php echo $php_gd_info; ?></td>
				</tr>
				<tr>
					<td><?php echo __('Recommended Maxim PHP Input Vars', 'memphis-documents-library').'<br><small><i>'.__('Your current max_input_vars value is','memphis-documents-library'); echo  ': <em>'.ini_get("max_input_vars").'</em></small></i>'; ?></td>
					<td class="text-center"><?php echo $input_vars_result; ?></td>
					<td><?php echo $input_vars_info; ?></td>
				</tr>
				<tr>
					<td><?php _e('Date Time Method Available', 'memphis-documents-library');?></td>
					<td class="text-center"><?php echo $datetime_method_result; ?></td>
					<td><?php echo $datetime_method_info; ?></td>
				</tr>
				<tr>
					<td><?php _e('System Temp Directory Set', 'memphis-documents-library');?></td>
					<td class="text-center"><?php echo $temp_dir_result; ?></td>
					<td><?php echo $temp_dir_info; ?></td>
				</tr>
				<tr>
					<td><?php _e('ZipArchive Installed', 'memphis-documents-library');?></td>
					<td class="text-center"><?php echo $zip_archive_method_result; ?></td>
					<td><?php echo $zip_archive_method_info; ?></td>
				</tr>
				<tr>
					<td><?php _e('Imagick Installed', 'memphis-documents-library');?></td>
					<td class="text-center"><?php echo $imagick_method_result; ?></td>
					<td><?php echo $imagick_method_info; ?></td>
				</tr>
				<tr>
					<td><?php _e('PHP5 cURL Installed', 'memphis-documents-library');?></td>
					<td class="text-center"><?php echo $php_curl_result; ?></td>
					<td><?php echo $php_curl_info; ?></td>
				</tr>
			</table>
		</div>
	</div>
<?php
}
?>