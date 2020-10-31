<?php
function mdocs_sort() {
	if(isset($_POST['type'])) {
		$mdocs_sort_type = get_option('mdocs-sort-type');
		$mdocs_sort_style = get_option('mdocs-sort-style');
		$mdocs = get_option('mdocs-list');
		if(isset($_POST['sort_type'])) {
			$sort_type = $_POST['sort_type'];
			setcookie('mdocs-sort-type', $sort_type,null,'/'); 
		} elseif(isset($_COOKIE['mdocs-sort-type'])) $sort_type = $_COOKIE['mdocs-sort-type'];
		else $sort_type = $mdocs_sort_type;
		if(isset($_POST['sort_range'])) {
			$sort_range = $_POST['sort_range'];
			setcookie('mdocs-sort-range', $sort_range,null,'/'); 
		} elseif(isset($_COOKIE['mdocs-sort-range'])) $sort_range = $_COOKIE['mdocs-sort-range'];
		else $sort_range = $mdocs_sort_style;
	}
}
?>