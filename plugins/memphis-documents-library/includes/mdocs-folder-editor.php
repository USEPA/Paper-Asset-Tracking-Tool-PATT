<?php
function mdocs_folder_editor() {
	$cats = get_option('mdocs-cats');
	mdocs_list_header();
	var_dump($cats);
	?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Folder Editor','memphis-documents-library'); ?></h3>
		</div>
		<div class="panel-body">
			<div class="mdocs-container">
				<table class="table table-hover" id="mdocs-list-table">
					<thead>
						<tr>
							<th>Folders</th>
							<th></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Folders</th>
							<th></th>
						</tr>
					</tfoot>
					<tr>
						<td>Hello World</td>
						<td>
							<button class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="<?php _e('Folder Permissions', 'memphis-documents-library'); ?>"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></button>
							<button class="btn btn-info" data-toggle="tooltip" data-placement="top" title="<?php _e('Add Sub Folder', 'memphis-documents-library'); ?>"><i class="fa fa-plus fa-lg" aria-hidden="true"></i></button>
							<button class="btn btn-success" data-toggle="tooltip" data-placement="top" title="<?php _e('Move Folder', 'memphis-documents-library'); ?>"><i class="fa fa-arrows fa-lg" aria-hidden="true"></i></button>
							<button class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="<?php _e('Delete Folder', 'memphis-documents-library'); ?>"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></button>
						</td>
						
					</tr>
				</table>
			</div>
		</div>
	</div>
	<?php
}
function mdocs_get_children() {
	
}
?>