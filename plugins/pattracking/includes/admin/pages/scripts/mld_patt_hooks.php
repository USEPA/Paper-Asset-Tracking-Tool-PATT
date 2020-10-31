<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$subfolder_path = site_url( '', 'relative'); 





// Utliizied in mdocs-upload.php to set variables for use later in the function.
function mld_get_patt_mdocs_cat( $post_vars ) {
	
	global $wpdb;
	
	$mdocs_cats = get_option('mdocs-cats');
	$new_mdocs_cats = array();
	
	//Check if category exists
	$current_cat = array();
	foreach ($mdocs_cats as $key ){
		array_push ($current_cat, $key['name']);
    }
	                
	if (!in_array($post_vars['mdocs-cat'], $current_cat)) {	
		//Count categories and begin process of adding new category
		/*$num_cats = 0;
		foreach( $mdocs_cats as $index => $cat ){ $num_cats++;}
		
		if ($num_cats > 1) {
			array_push ($new_mdocs_cats, array('base_parent' => '', 'index' => $num_cats, 'parent_index' => 0, 'slug' => 'mdocs-cat-'.$num_cats.'', 'name' => $post_vars['mdocs-cat'], 'parent' => '', 'children' => array(), 'depth' => 0));
		} else {
			array_push ($new_mdocs_cats, array('base_parent' => '', 'index' => $num_cats, 'parent_index' => 0, 'slug' => 'mdocuments', 'name' => $post_vars['mdocs-cat'], 'parent' => '', 'children' => array(), 'depth' => 0));
		}*/
		
		array_push ($new_mdocs_cats, array('base_parent' => '', 'index' => $num_cats, 'parent_index' => 0, 'slug' => $post_vars['mdocs-cat'], 'name' => $post_vars['mdocs-cat'], 'parent' => '', 'children' => array(), 'depth' => 0));
		$upd_new_new_arr = array_merge( $mdocs_cats, $new_mdocs_cats );
		update_option( 'mdocs-cats', $upd_new_new_arr, '' , 'no' );
		
		
		update_option('mdocs-num-cats',$num_cats+1);
	}
	
	global $wpdb;
	
	$get_attachment_ids = $wpdb->get_results("SELECT folderdocinfofile_id
		FROM wpqa_wpsc_epa_folderdocinfo_files
		WHERE attachment = '1' AND folderdocinfo_id = '".$post_vars['mdocs-cat']."'");
	
	$folderdocinfofile_id_array = array();
	
	foreach($get_attachment_ids as $item) {
		$clean_folderdocinfofileid = substr($item->folderdocinfofile_id, strrpos($item->folderdocinfofile_id, '-') + 1);
		
		if (substr($clean_folderdocinfofileid, 0, 1) === 'a') {
			array_push($folderdocinfofile_id_array, substr($clean_folderdocinfofileid, 1));
		}
	}
	
	$max_value = max($folderdocinfofile_id_array);
	$filename_suffix = $max_value + 1;
	$folderdocinfofile_id = $post_vars['mdocs-cat'].'-a'.$filename_suffix;
	
	
	$new_mdocs_cats = get_option('mdocs-cats');
	$new_mdocs_cat_val = Patt_Custom_Func::searchMultiArray($post_vars['mdocs-cat'], $new_mdocs_cats);

	//$mdocs_cat = $new_mdocs_cat_val;
	$data = [
		'mdocs_cat' => $new_mdocs_cat_val,
		'folderdocinfofile_id' => $folderdocinfofile_id
	];
	
/*
	echo '<br><br>Get Mdoc vars:<br>';
	echo '<pre>';	
	print_r($data);
	echo '</pre>';
*/
	
	return $data;
	
	
}

add_filter( 'wppatt_mld_get_patt_mdocs_cat', 'mld_get_patt_mdocs_cat' );



// Saves the MLD information into the PATT table: epa_folderdocinfo_files
function mld_save_attachment_info_db( $upload, $post_vars, $folderdocinfofile_id ) {
	
	global $wpdb;
								
	$table_name = $wpdb->prefix.'wpsc_epa_folderdocinfo_files';
	
	$wpdb->insert(
		$table_name,
		array(
			'post_id' => $upload['parent_id'],
			'folderdocinfo_id'  => $post_vars['mdocs-cat'],
			'folderdocinfofile_id'   => $folderdocinfofile_id,
			'attachment' => '1',
			'file_name'  => $upload['filename'],
// 			'file_location'   => '/uploads/mdocs/',
			'object_location'   => '/uploads/mdocs/',
			'title'  => $upload['name'],
			'description'   => $upload['desc'],
			'tags' => $post_vars['mdocs-tags'],
		)
	);
	
	
	$this_insert = $wpdb->insert_id;
	
	$_POST['wppatt_files_index'] = $this_insert;
	
}

add_action( 'wppatt_mld_save_attachment_info_db', 'mld_save_attachment_info_db', 10, 3 );


// Updates the MLD information into the PATT table: epa_folderdocinfo_files
function mld_update_attachemnt_info_db( $mdocs, $post_vars, $files  ) {
	
	
	// Get $post_vars['mdocs-index']; Our key
	$index_id = $post_vars['mdocs-index'];
	
	$key = array_search( $index_id, array_column( $mdocs, 'id' ) );
	
	$file_name = $mdocs[ $key ]['filename'];
	$title = $mdocs[ $key ]['name'];
	$tags = $post_vars['mdocs-tags'];
	$post_id = $mdocs[ $key ]['parent'];
	
	
	// DEBUG START
	echo '<br>Key Data:<br>';
	echo 'index_id: '.$index_id.'<br>';
	echo 'key: '.$key.'<br>';
	echo 'file_name: '.$file_name.'<br>';
	echo 'title: '.$title.'<br>';
	echo 'tags: '.$tags.'<br>';
	echo 'post_id: '.$post_id.'<br>';			
	
/*
	echo '<br><br>Post Vars:<br>';

	foreach ($post_vars as $key => $value) {
		echo '<b>key:</b> ' . $key . ' <b>value:</b> ' . $value;
		echo '<br>';
	}
	
	echo '<br><br>mdocs vars:<br>';
	echo '<pre>';	
	print_r($mdocs);
	echo '</pre>';	
	
	echo '<br><br>files vars:<br>';
	echo '<pre>';	
	print_r($files);
	echo '</pre>';
	// DEBUG END
*/
	
	
	
	
	global $wpdb;
	
	$table_name = $wpdb->prefix.'wpsc_epa_folderdocinfo_files';

	$wpdb->update(
		$table_name,
		array(
			'file_name'  => $file_name, 
			'title'  => $title, 
			'tags' => $tags
		),
		array( 'post_id'=> $post_id )		
	);
	
}

add_action( 'wppatt_mld_update_attachment_info_db', 'mld_update_attachemnt_info_db', 10, 3 );



// Updates the MLD information into the PATT table: epa_folderdocinfo_files
function mld_delete_attachemnt_info_db( $mdocs_file, $upload_dir  ) {
	
	
	// D E B U G 
/*
	echo '<br><br>mdocs_file:<br>';
	echo '<pre>';	
	print_r($mdocs_file);
	echo '</pre>';
	
	echo '<br><br>upload_dir:<br>';
	echo '<pre>';	
	print_r($upload_dir);
	echo '</pre>';
*/
	
	// WPDB Delete
	
	global $wpdb;
	
	require WPPATT_ABSPATH . 'includes/admin/pages/scripts/vendor/autoload.php';
	include_once( WPPATT_ABSPATH . 'includes/api_authorization_strings.php' );

	$s3 = new Aws\S3\S3Client([
		'region'  => $s3_region,
		'version' => 'latest',
		'credentials' => [
			'key'    => $s3_key,
			'secret' => $s3_secret,
		]
	]);	
		
	$table_name = $wpdb->prefix.'wpsc_epa_folderdocinfo_files';
	
	$get_object_key = $wpdb->get_row(
										"SELECT object_key, file_name
										FROM ".$table_name."
										WHERE post_id = '" .  $mdocs_file['parent'] . "'");
    
    $s3_object_key = $get_object_key->object_key;
    $file_name = $get_object_key->file_name;
	
	if( $s3_object_key != NULL ) {
	
	    $result = $s3->deleteObject([
	        'Bucket' => $s3_bucket,
	        'Key'    => $s3_object_key
	    ]);
	    
	    //var_dump($result);
	    
	    if ($result['@metadata']['statusCode'] == 204)
	    {
	        echo $file_name . ' was deleted from S3.' . PHP_EOL;
	    }
	    
		
	}
	
	$wpdb->delete(
		$table_name,
		array( 'post_id'=> $mdocs_file['parent'] )		
	);
}

add_action( 'wppatt_mld_delete_attachment_info_db', 'mld_delete_attachemnt_info_db', 10, 2 );



// OLD and not used.
function mld_save_post_id( $post_id, $all_post, $folderdocinfo_id ) {
	
	global $wpdb;
	
	
/*
	foreach ($all_post as $key => $value) {
		echo '<b>key:</b> ' . $key . ' <b>value:</b> ' . $value;
		echo '<br>';
	}
*/
	
	echo 'post_id: ' . $post_id;
	echo '<br>';
	echo 'folderdocinfo_id: ' . $folderdocinfo_id;
	
	
	
	
/*
	$table_name = $wpdb->prefix . 'wpsc_epa_folderdocinfo_files';
	$data_update = array( 'post_id' => $post_id );
	$data_where = array( 'folderdocinfo_id' => $folderdocinfo_id );
	$wpdb->update( $table_name , $data_update, $data_where );
*/
	
}

//add_action( 'wppatt_mld_save_post_id', 'mld_save_post_id', 10, 3);   



// Augment the MLD uploader with an S3 multi part uploader.
function mld_s3_uploader( ) {
	
 	include( WPPATT_ABSPATH .'includes/admin/pages/scripts/index.php' );	// working 
//	include( WPPATT_ABSPATH .'includes/admin/pages/scripts/s3_upload_wrapper.php' );	
	//include( WPPATT_ABSPATH .'includes/admin/pages/scripts/page2.php' ); //  working
	//die();	
	

	
	
	$new_path = WPPATT_PLUGIN_URL . 'includes/admin/pages/scripts/s3upload.js';
	$test = WPPATT_ABSPATH . 'includes/admin/pages/scripts/s3upload.js';
	//WPPATT_PLUGIN_URL WPPATT_ABSPATH
	ob_start();
	//$test = s3();
	//echo 'This is just a test<br>';
	//echo $test;
?>
	<script>
		let new_path = '<?php echo $new_path ?>';
		let test = '<?php echo $test ?>';
		console.log({new_path:new_path});
		console.log({test:test});
	</script>




	
		
<?php 
	
	$the_panel = ob_get_clean();
	echo $the_panel;
		
	
}

add_action( 'wppatt_mld_s3_uploader', 'mld_s3_uploader');   


// Returns the stored file size with units 
function wppatt_get_file_size( $the_mdoc ) {
		
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpsc_epa_folderdocinfo_files';
	
	$sql = 'SELECT file_size FROM ' . $table_name . ' WHERE post_id = ' . $the_mdoc['parent'];
	$obj = $wpdb->get_row( $sql );
	
	//$file_size = $obj->file_size;
	 $file_size = $the_mdoc['size'];
	
	if( ($file_size / 1000000000) > 1 ) {
		$file_size = round( $file_size / 1000000000, 2);
		$file_size .= ' GB';
	} elseif( ($file_size / 1000000) > 1 ) {
		$file_size = round( $file_size / 1000000, 2);
		$file_size .= ' MB';
	} elseif( ($file_size / 1000) > 1 ) {
		$file_size = round( $file_size / 1000, 2);
		$file_size .= ' KB';
	} elseif( $file_size == 0 ) {
		$file_size = 'Blank. Upload File.';
	} else {
		$file_size .= ' Bytes';
	}
	
	return $file_size;
	//return $obj->file_size;
}
add_filter( 'wppatt_mld_get_file_size', 'wppatt_get_file_size' );


// Returns the file name, icon, and info to display the file on ?page=filedetails
function wppatt_get_file_display( $data ) {
		
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpsc_epa_folderdocinfo_files';
	
	$sql = 'SELECT source_file_location FROM ' . $table_name . ' WHERE post_id = ' . $data['parent'];
	$obj = $wpdb->get_row( $sql );
	
	$source_file_location = $obj->source_file_location;
	
	if( $data['size'] == 0 ) {
		$source_file_location_string = '<br>' . '<small class="text-muted">' . $source_file_location . '</small>'; 
	} else {
		$source_file_location_string = '';
	}
	
	
	$name_string = $data['new_or_updated'] . 
					$data['file_status'] . 
					$data['post_status'] . 
					$data['file_type_icon'] . ' ' . 
					$data['display_name'] . 
					' - ' . 
					'<small class="text-muted">' . $data['file_name'] . '</small>' . 
					$data['scheduled'] . 
					$source_file_location_string;
	
	return $name_string;

}
add_filter( 'wppatt_mld_get_file_display', 'wppatt_get_file_display' );


function test_update_option(  $option,  $old_value,  $value ) {
	
	if( $option == 'mdocs-cats') {
		echo 'option: ';
		echo '<pre>';
		print_r( $option );
		echo '</pre>';
		
		echo 'old_value: ';
		echo '<pre>';
		print_r( $old_value );
		echo '</pre>';
		
		echo 'value: ';
		echo '<pre>';
		print_r( $value );
		echo '</pre>';
		//die();
	}
}

//add_action( 'update_option', 'test_update_option', 10, 3);
