<?php
function mdocs_find_lost_files() {
	mdocs_list_header();
?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('Find Lost Files','memphis-documents-library'); ?></h3>
	</div>
	<div class="panel-body">
		<h4><?php _e('Sometime files may get lost, this process will allow you to find some of those lost files.','memphis-documents-library'); ?></h4>
		<p><?php _e('Click the button below to run a scan of your Memphis Documents file system.  This scan will find files that may have been lost or moved to unknown locations.','memphis-documents-library'); ?></p>
		<p><?php _e('Once the system scan is complete you will see a list of lost files,  select the folder you would like them to be transferred to and click the save button.  Once complete all the lost files will be visible again.','memphis-documents-library'); ?></p>
		<h5><?php _e('This process may take awhile so please be patient.','memphis-documents-library'); ?></h5>
		<button class="btn btn-primary" id="mdocs-find-lost-file-start"><?php _e('Start File Search', 'memphis-documents-library'); ?></button>
		<div id="mdocs-find-lost-files-results"></div>
		<div id="mdocs-find-lost-files-saved"></div>
	</div>
</div>
<?php
}
function find_lost_files() {
	global $found_cat;
	$found_at_least_one = false;
	$docs = get_option('mdocs-list');
	$cats = get_option('mdocs-cats');
	echo '<form action="#" method="POST" id="mdocs-find-lost-files-save">';
	echo '<table class="table table-hover table-striped table-bordered" >';
	?>
	<table class="table table-hover table-striped table-bordered" >
		<tr>
			<th><?php _e('Filename', 'memphis-documents-library'); ?></th>
			<th><?php _e('File Size', 'memphis-documents-library'); ?></th>
			<th><?php _e('Folder', 'memphis-documents-library'); ?></th>
		</tr>
	<?php
	$id = 0;
	foreach($docs as $index => $doc) {
		$found_cat = mdocs_find_cat($doc['cat']);
		if($found_cat === false) {
			$found_at_least_one = true;
			$filesize_mb = number_format(round($doc['size']/1024,0));
			?>
			<tr>
				<td>
					<!--
					<input type="hidden" name="mdocs-find-lost-files[<?php echo $id; ?>][index]" value="<?php echo $index; ?>"/>
					<input type="hidden" name="mdocs-find-lost-files[<?php echo $id; ?>][name]" value="<?php echo $doc['name']; ?>" />
					-->
					<input type="hidden" name="<?php echo $index; ?>" value="<?php echo $doc['name']; ?>" />
					
					<?php echo $doc['name']; ?>
				</td>
				<td><?php echo $filesize_mb.' '.__('KB','memphis-documents-library'); ?></td>
				<td>
					<select id="mdocs-find-lost-files-select" name="<?php echo $index; ?>" >
						<?php mdocs_display_folder_options_menu($cats); ?>
					</select>
				</td>
			</tr>
			<?php
			$id++;
		}
	}
	?>
		<?php if($found_at_least_one == true) { ?>
		<tr>
			<td colspan="3">
				<input type="submit" class="btn btn-primary" id="mdocs-find-lost-files-save-btn"  value="<?php _e('Save Files', 'memphis-documents-library'); ?>" />
			</td>
		</tr>
		</table>
	</form>
		<?php } ?>
		
	<?php
	if($found_at_least_one == false) {
		?>
		<tr>
			<td colspan="3" class="text-center">
				<div class="alert alert-success" role="alert" ><?php _e('Congratulations, you have no lost files.', 'memphis-documents-library'); ?></div>
			</td>
		</tr>
		</table>
	</form>
		<?php
	}
	
}
function save_lost_files() {
	if(current_user_can( 'mdocs_manage_settings' )) {
		$the_docs = mdocs_array_sort(get_option('mdocs-list'));
		mdocs_sanitize_array($_POST);
		$_POST['form-data'] =htmlspecialchars_decode($_POST['form-data']);
		$_POST['form-data'] = stripcslashes($_POST['form-data']);
		$the_missing_files = json_decode($_POST['form-data'], true);
		foreach($the_missing_files as $index => $lost_doc) {
			if( $lost_doc['name'] == $the_docs[$index]['name']) {
				$the_docs[$index]['name'] = $the_docs[$index]['name'];
				$the_docs[$index]['cat'] = $lost_doc['folder'];
			} else {
				echo '<div class="alert alert-danger" role="alert" > '.__('A name conflict has occurred with the following file', 'memphis-documents-library').': '.$lost_doc['name'].' => '.$the_docs[$lost_doc['index']]['name'].' </div>';
			}
		}
		update_option('mdocs-list',$the_docs, 'no');
		echo '<br><br><div class="alert alert-success" role="alert" > '.__('Find Lost Files Process Complete.', 'memphis-documents-library').' </div>';
	}
}
$found_cat = false;
function mdocs_find_cat($find_cat, $cats=null) {
	global $found_cat;
	$found_cat = false;
	if($cats == null) $cats = get_option('mdocs-cats');
	foreach($cats as $cat) {
		if($find_cat == $cat['slug']) {
			$found_cat = true;
		} else {
			if(count($cat['children']) > 0 && $found_cat == false) {
				mdocs_find_cat($find_cat, $cat['children'], $found_cat );
			}
		}
	}
	return $found_cat;
}
?>
