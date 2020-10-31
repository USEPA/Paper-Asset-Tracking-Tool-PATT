<?php
function mdocs_default_caps() {
	mdocs_add_cap('manage-settings', 'Manage Settings', null, array('mdocs_manage_settings', 'manage_options'));
	mdocs_add_cap('manage-options', 'Manage Options', null, array('mdocs_manage_options'));
	mdocs_add_cap('allow-upload', 'Allow to Upload', null, array('mdocs_allow_upload'));
	mdocs_add_cap('private-post-viewing', 'View Private Posts and Pages', null, array('read_private_posts','read_private_pages'));
	mdocs_add_cap('batch-edit', 'Allow to Batch Edit', null, array('mdocs_batch_edit'));
	mdocs_add_cap('batch-move', 'Allow to Batch Move', null, array('mdocs_batch_move'));
	mdocs_add_cap('batch-delete', 'Allow to Batch Delete', null, array('mdocs_batch_delete'));
	mdocs_add_cap('allow-upload-frontend', 'Allow to Upload Frontend', null, array('mdocs_allow_upload_frontend'));
	mdocs_init_view_private();
}
function mdocs_init_view_private() {
	$the_caps = get_option('mdocs-caps');
	$roles = get_editable_roles();
	$view_private = array();
	foreach($roles as $index => $role) {
		if(isset($role['capabilities']['read_private_pages']) || isset($role['capabilities']['read_private_posts']) || $index == 'administrator') $view_private[$index] = true;
		else $view_private[$index] = false;
	}
	if(array_key_exists('private-post-viewing', $the_caps)) {
		$the_caps['private-post-viewing']['roles'] = array();
		foreach($view_private as $index => $view) {
			if($view == true and $index != 'administrator') array_push($the_caps['private-post-viewing']['roles'], $index);
		}
	}
	update_option('mdocs-caps', $the_caps);
}
function mdocs_manage_roles() {
	$wp_roles = get_editable_roles();
	foreach($wp_roles as $index => $role) {
		$role_object = get_role($index);
		if($index != 'administrator') {
			foreach(get_option('mdocs-caps') as $i => $cap) {
				if(isset($cap['roles'])) {
					if(is_array($cap['roles'])) {
						if(in_array($index, $cap['roles'])) {
							foreach($cap['caps'] as $name) {
								$role_object->add_cap($name);
							}
						} else {
							foreach($cap['caps'] as $name) {
								$role_object->remove_cap($name);
							}
						}
					}
				} else {
					
					foreach($cap['caps'] as $name) {
						
						$role_object->remove_cap($name);
					}
				} 
			}
		}
	}
}

function mdocs_add_cap($key, $title='', $roles=array(), $caps=array()) {
	$the_caps = get_option('mdocs-caps');
	if(!array_key_exists($key, $the_caps)) {
		$the_caps[$key] = array('title' => $title, 'roles' => $roles, 'caps' => $caps);
		$admin_role = get_role('administrator');
		if(is_object($admin_role) && is_array($admin_role->capabilities)) {
			foreach($caps as $cap) {
				$admin_role->add_cap($cap);
			}
		}
		update_option('mdocs-caps', $the_caps);
	}
}
function mdocs_update_cap($key, $title=null, $roles=null, $caps=null) {
	$the_caps = get_option('mdocs-caps');
	if($title != null) $the_caps[$key]['title'] = $title;
	if(is_array($roles)) $the_caps[$key]['roles'] = $roles;
	if(is_array($caps)) $the_caps[$key]['caps'] = $caps;
	update_option('mdocs-caps',$the_caps);
}
function mdocs_delete_cap($key) {
	$caps = get_option('mdocs-caps');
	unset($caps[$key]);
	update_option('mdocs-caps',$caps);
}
?>