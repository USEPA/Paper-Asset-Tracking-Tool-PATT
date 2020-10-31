<?php
function mdocs_allowed_file_types() {
mdocs_list_header();
?>


<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('Allow File Types','memphis-documents-library'); ?></h3>
	</div>
	<div class="panel-body">
		<table class="table form-table">
			<tr>
				<th><?php _e('Allowed File Types','memphis-documents-library'); ?></th>
				<?php
				$mimes = get_allowed_mime_types();
				?>
				<td>
					<table class="mdocs-mime-table table table-striped table-hover">
						<tr>
							<th><?php _e('Extension','memphis-documents-library'); ?></th>
							<th><?php _e('Mime Type','memphis-documents-library'); ?></th>
							<th><?php _e('Options','memphis-documents-library'); ?></th>
						</tr>
							<?php
							foreach($mimes as $index => $mime) {
								echo '<tr data-file-type="'.$index.'" ><td>'.$index.'</td><td>'.$mime.'</td>';
								echo '<td><a href="#" class="mdocs-remove-mime">'.__('remove','memphis-documents-library').'</a></td>';
								echo '</tr>';
							}
							?>
						<tr class="mdocs-mime-submit">
							<td><input type="text" placeholder="Enter File Type..." name="mdocs-file-extension" value=""/></td>
							<td><input type="text" placeholder="Enter Mime Type..." name="mdocs-mime-type" value=""/></td>
							<td><a href="#" id="mdocs-add-mime"><?php _e('add','memphis-documents-library'); ?></a></td>
						</tr>
					</table>
					<a href="http://www.freeformatter.com/mime-types-list.html#mime-types-list" alt="<?php _e('List of Files and Their Mime Types','memphis-documents-library'); ?>" target="_blank"><?php _e('List of Files and Their Mime Types','memphis-documents-library'); ?></a><br>
					<a href="#" id="mdocs-restore-default-file-types" alt="<?php _e('Restore Default File Types','memphis-documents-library'); ?>"><?php _e('Restore Default File Types','memphis-documents-library'); ?></a>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
}
?>