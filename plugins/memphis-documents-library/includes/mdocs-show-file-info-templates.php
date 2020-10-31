<?php
function mdocs_show_file_info_templates() {
	// TABLE SHOW OPTIONS
	register_setting('mdocs-global-settings', 'mdocs-displayed-file-info');
	add_option('mdocs-displayed-file-info', array());
	// DELETE ALL FILE INFO COLUMNS
	/*
	mdocs_delete_file_info('show-name');
	mdocs_delete_file_info('show-description');
	mdocs_delete_file_info('show-downloads');
	mdocs_delete_file_info('show-version');
	mdocs_delete_file_info('show-author');
	mdocs_delete_file_info('show-real-author');
	mdocs_delete_file_info('show-modified');
	mdocs_delete_file_info('show-rating');
	mdocs_delete_file_info('show-download');
	mdocs_delete_file_info('show-file-size');
	mdocs_delete_file_info('show-file-type');
	mdocs_delete_file_info('thumbnails');
	*/
	/*
	mdocs_delete_file_info(0);
	mdocs_delete_file_info(1);
	mdocs_delete_file_info(2);
	mdocs_delete_file_info(3);
	mdocs_delete_file_info(4);
	mdocs_delete_file_info(5);
	mdocs_delete_file_info(6);
	mdocs_delete_file_info(7);
	mdocs_delete_file_info(8);
	mdocs_delete_file_info(9);
	mdocs_delete_file_info(10);
	mdocs_delete_file_info(11);
	mdocs_delete_file_info(12);
	mdocs_delete_file_info(13);
	mdocs_delete_file_info(14);
	mdocs_delete_file_info(15);
	mdocs_delete_file_info(16);
	mdocs_delete_file_info(17);
	mdocs_delete_file_info(18);
	mdocs_delete_file_info(19);
	mdocs_delete_file_info(20);
	mdocs_delete_file_info(21);
	mdocs_delete_file_info(22);
	mdocs_delete_file_info(23);
	mdocs_delete_file_info(24);
	*/
	mdocs_add_file_info('show-name', array(
		'text' => 'Name',
		'func' => 'mdocs_display_name',
		'order' => 0,
		'width' => 15,
		'show' => true,
		'is-form' => true,
		'form-data' => array(
			'show-in-form' => true,
			'display-function' => 'mdocs_upload_display_name',
			'hide-function' => 'mdocs_upload_hide_name',
		),
	));
	mdocs_add_file_info('show-description', array(
		'text' => 'Description',
		'func' => 'mdocs_display_description',
		'order' => 1,
		'width' => 30,
	));
	mdocs_add_file_info('show-downloads', array(
		'text' => 'Downloads',
		'icon' => 'fas fa-cloud-download-alt',
		'color' =>  '#FF9140',
		'func' => 'mdocs_display_downloads',
		'order' => 2,
		'width' => 12,
		'show' => true,
	));
	mdocs_add_file_info('show-version', array(
		'text' => 'Version',
		'icon' => 'fa fa-history',
		'color' =>  '#0066FF',
		'func' => 'mdocs_display_version',
		'order' => 3,
		'width' => 9,
		'show' => true,
	));
	mdocs_add_file_info('show-author', array(
		'text' => 'Owner',
		'color' =>  '#779F00',
		'func' => 'mdocs_display_owner',
		'order' => 4,
		'width' => 9,
		'show' => true,
	));
	mdocs_add_file_info('show-real-author', array(
		'text' => 'Author',
		'func' => 'mdocs_display_real_author',
		'order' => 5,
		'width' => 9,
	));
	mdocs_add_file_info('show-modified', array(
		'text' => 'Last Modified',
		'icon' => 'fa fa-calendar',
		'color' =>  '#990000',
		'func' => 'mdocs_display_updated',
		'order' => 6,
		'width' => 15,
		'show' => true,
	));
	mdocs_add_file_info('show-rating', array(
		'text' => 'Rating',
		'func' => 'mdocs_display_rating',
		'order' => 7,
		'width' => 10,
		'show' => true,
	));
	mdocs_add_file_info('show-download', array(
		'text' => 'Download',
		'icon' => 'fa fa-download',
		'func' => 'mdocs_display_download_btn',
		'order' => 8,
		'width' => 12,
	));
	mdocs_add_file_info('show-file-size', array(
		'text' => 'File Size',
		'icon' => 'fa fa-database',
		'func' => 'mdocs_display_file_size',
		'order' => 9,
		'width' => 10,
	));
	mdocs_add_file_info('show-file-type', array(
		'text' => 'File Type',
		'func' => 'mdocs_display_file_type',
		'order' => 10,
		'width' => 9,
	));
	// UPDATING THE TEXT COLOR TO STANDARD HEX COLORS
	mdocs_update_file_info('show-downloads', array('color' =>  '#FF9140'));
	mdocs_update_file_info('show-version', array('color' =>  '#0066FF'));
	mdocs_update_file_info('show-author', array('color' =>  '#779F00'));
	mdocs_update_file_info('show-modified', array('color' =>  '#990000'));
}
function mdocs_add_file_info($key, $args=array(), $is_update=false) { 
	$file_info = get_option('mdocs-displayed-file-info');
	
	if(@!array_key_exists($key, $file_info) && $is_update == false ) {
		$file_info[$key]['key'] = $key;
		$file_info[$key]['slug'] = str_replace('show-','',$key);
		if(isset($args['text'])) $file_info[$key]['text'] = $args['text'];
		else $file_info[$key]['text'] = null;
		if(isset($args['icon'])) $file_info[$key]['icon'] = $args['icon'];
		else $file_info[$key]['icon'] = null;
		if(isset($args['color'])) $file_info[$key]['color'] = $args['color'];
		else $file_info[$key]['color'] = null;
		if(isset($args['func'])) $file_info[$key]['function'] = $args['func'];
		else $file_info[$key]['function'] = null;
		if(isset($args['order'])) $file_info[$key]['order'] = $args['order'];
		else $file_info[$key]['order'] = null;
		if(isset($args['width'])) $file_info[$key]['width'] = $args['width'];
		else $file_info[$key]['width'] = null;
		if(isset($args['show'])) $file_info[$key]['show'] = $args['show'];
		else $file_info[$key]['show'] = false;
		if(isset($args['is-file-info'])) $file_info[$key]['is-file-info'] = $args['is-file-info'];
		else $file_info[$key]['is-file-info'] = true;
		if(isset($args['is-form'])) $file_info[$key]['is-form'] = $args['is-form'];
		else $file_info[$key]['is-form'] = false;
		if(isset($args['form-data'])) {
			$form_args = $args['form-data'];
			
			if(isset($form_args['show-in-form']))  $file_info[$key]['form-data']['show-in-form'] = $form_args['show-in-form'];
			else $file_info[$key]['form-data']['show-in-form']= false;
			if(isset($form_args['disabled-in-form'])) $file_info[$key]['form-data']['disabled-in-form'] = $form_args['disabled-in-form'];
			else $file_info[$key]['form-data']['disabled-in-form'] = false;
			if(isset($form_args['default'])) $file_info[$key]['form-data']['default'] = $form_args['default'];
			else $file_info[$key]['form-data']['default'] = null;
			if(isset($form_args['display-function'])) $file_info[$key]['form-data']['display-function'] = $form_args['display-function'];
			else $file_info[$key]['form-data']['display-function'] = null;
			if(isset($form_args['hide-function'])) $file_info[$key]['form-data']['hide-function'] = $form_args['hide-function'];
			else $file_info[$key]['form-data']['hide-function'] = null;
			
		} else {
			$file_info[$key]['form-data'] = array(
				'show-in-form' => false,
				'disabled-in-form' => false,
				'default' => null,
				'display-function' => null,
				'hide-function' => null,
			);
		}
		update_option('mdocs-displayed-file-info',$file_info);
		return $file_info[$key];
	} elseif($is_update == true && array_key_exists($key, $file_info)) {
		if(isset($args['text'])) $file_info[$key]['text'] = $args['text'];
		if(isset($args['text-more'])) $file_info[$key]['text-more'] = $args['text-more'];
		if(isset($args['icon'])) $file_info[$key]['icon'] = $args['icon'];
		if(isset($args['color'])) $file_info[$key]['color'] = $args['color'];
		if(isset($args['func'])) $file_info[$key]['function'] = $args['func'];
		if(isset($args['order'])) $file_info[$key]['order'] = $args['order'];
		if(isset($args['width'])) $file_info[$key]['width'] = $args['width'];
		if(isset($args['show'])) $file_info[$key]['show'] = $args['show'];
		if(isset($args['form-data'])) $file_info[$key]['form-data'] = $args['form-data'];
		update_option('mdocs-displayed-file-info',$file_info);
		return $file_info[$key];
	}
}
function mdocs_update_file_info($key, $args=array()) { // $text=null, $icon=null, $color=null, $function=null, $order=null,$width=null, $show=null) {
	//return mdocs_add_file_info($key, $text, $icon, $color, $function, $order, $width, $show, true);
	return mdocs_add_file_info($key, $args, true);
}
function mdocs_delete_file_info($key='') {
	$file_info = get_option('mdocs-displayed-file-info');
	unset($file_info[$key]);
	update_option('mdocs-displayed-file-info', $file_info);
}
function mdocs_dispaly_default($the_mdoc) {
	_e('No function found.', 'memphis-documents-library');
}
function mdocs_display_name($the_mdoc) {

}
function mdocs_display_file_type($the_mdoc) {
	echo mdocs_get_file_type_icon($the_mdoc,true,true);
}
function mdocs_display_downloads($the_mdoc) {
	echo $the_mdoc['downloads'].' <small>'.__('downloads','memphis-documents-library').'</small>';
}
function mdocs_display_version($the_mdoc) {
	echo $the_mdoc['version'];
}
function mdocs_display_owner($the_mdoc) {
// PATT start	
	echo $the_mdoc['owner'];
	//echo get_user_by('login', $the_mdoc['owner'])->display_name;
// PATT end	
}
function mdocs_display_updated($the_mdoc) {
	$the_date = mdocs_format_unix_epoch($the_mdoc['modified']);
	if($the_date['gmdate'] > time()) $scheduled = '<small class="text-muted"><em>'.__('Scheduled').'</em></small>';
	else $scheduled = '';
	echo $the_date['formated-date'];
}
function mdocs_display_rating($the_mdoc) {
	$the_rating = mdocs_get_rating($the_mdoc);
	for($i=1;$i<=5;$i++) {
		if($the_rating['average'] >= $i) echo '<i class="fas fa-star fa-xs mdocs-gold" id="'.$i.'" aria-hidden="true"></i>';
		elseif(ceil($the_rating['average']) == $i ) echo '<i class="fas fa-star-half-alt fa-xs mdocs-gold" id="'.$i.'" aria-hidden="true"></i>';
		else echo '<i class="far fa-star fa-xs" id="'.$i.'" aria-hidden="true"></i>';
	}
}
function mdocs_display_download_btn($the_mdoc) {
	$show_download = false;
	if($the_mdoc['non_members'] == 'on' ) $show_download = true;
	if(get_option( 'mdocs-hide-all-files-non-members' ) == false && $the_mdoc['non_members'] == '')$show_download = true;
	if($the_mdoc['non_members'] == '' ) $show_download = false;
	if(get_option( 'mdocs-hide-all-files-non-members') == true)  $show_download = false;
	if(is_user_logged_in()) $show_download = true;

	if($show_download) {
		?><a href="<?php echo site_url().'/?mdocs-file='.$the_mdoc['id']; ?>"><?php echo __('Download','memphis-documents-library'); ?></a><?php
	} else {
		?><a href="<?php echo wp_login_url(htmlspecialchars(get_permalink($the_mdoc['parent']))); ?>"><?php echo __('Login','memphis-documents-library'); ?></a><?php
	}
}
function mdocs_display_description($the_mdoc) {
	echo stripcslashes($the_mdoc['desc']);
}
function mdocs_display_real_author($the_mdoc) {
	if(isset($the_mdoc['author']))	echo $the_mdoc['author'];
}
function mdocs_display_file_size($the_mdoc) {
	$upload_dir = wp_upload_dir();
	$mdocs_file = $upload_dir['basedir'].'/mdocs/'.$the_mdoc['filename'];
	
// PATT begin
	$file_size = apply_filters( 'wppatt_mld_get_file_size', $the_mdoc );
	echo  $file_size;	
// 	echo mdocs_convert_bytes(filesize($mdocs_file));
// PATT end	
	
	
}

?>