<?php
function mdocs_export() {
	$upload_dir = wp_upload_dir();
	$path = $upload_dir['basedir'];
	$mdocs = get_option('mdocs-list');
	$mdocs = htmlspecialchars(serialize($mdocs));
	$cats = htmlspecialchars(serialize(get_option('mdocs-cats')));
	mdocs_list_header();
?>
<div id="mdocs-export-container">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Export Files','memphis-documents-library'); ?></h3>
		</div>
		<div class="panel-body">
			<p>When you click the buttons below the document repository will create a ZIP files for you to save to your computer.</p>
			<p>This compressed data, will contain your documents, saved variables, and media posts tied to each document.</p>
			<p>Once you've saved the download file, you can use the Import function in another WordPress installation to import the content from this site.</p>
			<h4>Click the Button to Export Memphis Documents</h4>
			<form action="" method="post" id="mdocs-export">
				<input type="button" onclick="mdocs_download_zip('mdocs-export.zip');" id="mdocs-export-submit" class="btn btn-primary" value="<?php _e('Export Memphis Documents Library','memphis-documents-library'); ?>">
				<!--<input type="button" onclick="mdocs_download_zip('mdocs-export.zip');" id="mdocs-export-submit" class="btn btn-success" value="<?php _e('Export Memphis Documents Library Data Only','memphis-documents-library'); ?>">--><br><br>
				<input type="checkbox" name="mdocs-export-donot-delete"><?php _e('Check this if you are having difficulty downloading the export file.', 'memphis-documents-library'); ?>
			</form>
		</div>
	</div>
</div>
<?php
}
function mdocs_export_zip() {
	$mdocs_zip = get_option('mdocs-zip');
	$mdocs_list = get_option('mdocs-list');
	if(empty($mdocs_list)) $mdocs_list = array();
	$mdocs_cats = get_option('mdocs-cats');
	if(is_string($mdocs_cats)) $mdocs_cats = array();
	$upload_dir = wp_upload_dir();
	$mdocs_zip_file = sys_get_temp_dir().'/mdocs-export.zip';
	if(is_dir(sys_get_temp_dir().'/mdocs/')) {
		mdocs_rmdir(sys_get_temp_dir().'/mdocs/');
	}
	mkdir(sys_get_temp_dir().'/mdocs/');
	$mdocs_cats_file = sys_get_temp_dir().'/mdocs/'.MDOCS_CATS;
	$mdocs_list_file = sys_get_temp_dir().'/mdocs/'.MDOCS_LIST;
	file_put_contents($mdocs_cats_file, serialize($mdocs_cats));
	file_put_contents($mdocs_list_file, serialize($mdocs_list));
	mdocs_zip_dir($upload_dir['basedir'].'/mdocs',$mdocs_zip_file,true);
	if(file_exists($mdocs_cats_file)) unlink($mdocs_cats_file);
	if(file_exists($mdocs_list_file)) unlink($mdocs_list_file);
	mdocs_rmdir(sys_get_temp_dir().'/mdocs/');
}
function mdocs_zip_dir($sourcePath, $outZipPath)  { 
    @unlink($outZipPath);
	$pathInfo = pathInfo($sourcePath); 
    $parentPath = $pathInfo['dirname']; 
    $dirName = $pathInfo['basename'];
	if(class_exists('ZipArchive')) {
		$z = new ZipArchive(); 
		$z->open($outZipPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE); 
		$z->addEmptyDir($dirName); 
		mdocs_folder_zip($sourcePath, $z, strlen("$parentPath/")); 
		$z->close();
	} else die('ZipArchive Not Installed.');
    
}
function mdocs_folder_zip($folder, &$zipFile, $exclusiveLength) { 
	$handle = opendir($folder);
	$zipFile->addFile(sys_get_temp_dir().'/mdocs/'.MDOCS_CATS, 'mdocs/'.MDOCS_CATS); 
	$zipFile->addFile(sys_get_temp_dir().'/mdocs/'.MDOCS_LIST, 'mdocs/'.MDOCS_LIST); 
    while (false !== $f = readdir($handle)) { 
      if ($f != '.' && $f != '..' && $f != 'mdocs-files.bak') { 
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip. 
        $localPath = substr($filePath, $exclusiveLength); 
        if (is_file($filePath)) { 
          $zipFile->addFile($filePath, $localPath); 
        } elseif (is_dir($filePath)) { 
          // Add sub-directory. 
          $zipFile->addEmptyDir($localPath); 
          mdocs_folder_zip($filePath, $zipFile, $exclusiveLength); 
        } 
      } 
    } 
    closedir($handle); 
}
?>