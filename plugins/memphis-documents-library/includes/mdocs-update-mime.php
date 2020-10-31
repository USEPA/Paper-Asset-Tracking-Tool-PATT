<?php
function mdocs_update_mime() {
	if(isset($_POST['type'])  && $_POST['type'] == 'add-mime') {
		$file_extension = str_replace('.','',strtolower($_POST['file_extension']));
		$mime_type = $_POST['mime_type'];
		$mdocs_allowed_mime_types = get_option('mdocs-allowed-mime-types');
		$mdocs_allowed_mime_types[$file_extension] = $mime_type;
		update_option('mdocs-allowed-mime-types', $mdocs_allowed_mime_types);
		$mdocs_removed_mime_types = get_option('mdocs-removed-mime-types');
		unset($mdocs_removed_mime_types[$file_extension]);
		update_option('mdocs-removed-mime-types', $mdocs_removed_mime_types);
		add_filter('upload_mimes', 'mdocs_custom_mime_types');
		echo '<tr data-file-type="'.$file_extension.'" ><td>'.$file_extension.'</td><td>'.$mime_type.'</td>';
		echo '<td><a href="#" class="mdocs-remove-mime">remove</a></td></tr>';
	} elseif(isset($_POST['type'])  && $_POST['type'] == 'remove-mime') {
		$file_extension = $_POST['file_extension'];
		$mdocs_removed_mime_types = get_option('mdocs-removed-mime-types');
		$mdocs_removed_mime_types[strval($file_extension)] = $file_extension;
		update_option('mdocs-removed-mime-types', $mdocs_removed_mime_types);
	} elseif(isset($_POST['type'])  && $_POST['type'] == 'restore-mime') {
		update_option('mdocs-allowed-mime-types', array());
		update_option('mdocs-removed-mime-types', array());
		add_filter('upload_mimes', 'mdocs_custom_mime_types');
		$mimes = get_allowed_mime_types();
		?>
		<tbody>
		<tr>
			<th><?php _e('Extension','memphis-documents-library'); ?></th>
			<th><?php _e('Mime Type','memphis-documents-library'); ?></th>
			<th><?php _e('Options','memphis-documents-library'); ?></th>
		</tr>
		<?php
		foreach($mimes as $index => $mime) {
			echo '<tr data-file-type="'.$index.'" ><td>'.$index.'</td><td>'.$mime.'</td>';
			echo '<td><a href="#" class="mdocs-remove-mime">remove</a></td>';
			echo '</tr>';
		}
		?>
		<tr class="mdocs-mime-submit">
			<td><input type="text" placeholder="Enter File Type..." name="mdocs-file-extension" value=""/></td>
			<td><input type="text" placeholder="Enter Mime Type..." name="mdocs-mime-type" value=""/></td>
			<td><a href="#" id="mdocs-add-mime"><?php _e('add','memphis-documents-library'); ?></a></td>
		</tr>
		</tbody>
		<?php
	}
}



?>