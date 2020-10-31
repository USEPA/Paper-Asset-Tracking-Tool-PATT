<?php

require('partials/my-database-admin-admin-display.php');
require('partials/my-database-admin-role-based-access.php');

class My_Database_Admin_Admin {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	
	public function My_Database_Admin_admin_menu(){
		
		$parent = 'users.php';
		
		$addmenuforrole = 'administrator';
		
		$user = wp_get_current_user();
		if ( in_array( 'administrator', (array) $user->roles ) ) {
			//The user has the "administrator" role
		} else {
			
			foreach($user->roles as $role) {
				if(get_option("mda_role_".$role)) {
					$addmenuforrole = $role;
					break;
				}
			}
			
		}

		$page = add_menu_page( 'Database Admin ' . __( 'Database Admin', 'database_admin' ), 'Database Admin', $addmenuforrole, 'database_admin', array( $this, 'database_admin' ) , dirname(plugin_dir_url(__FILE__)) . '/images/icon.png');
		
		$page = add_submenu_page( 'database_admin', 'Database Admin ' . __('Role Based Access'), __('Role Based Access'), 'administrator', 'database_my_admin_role_based_access', array( $this,'database_my_admin_role_based_access') );
		
	}
	
	
	public function database_admin(){	
		wp_register_style('my_database_admin_css', plugins_url('css/admin-style.css', __FILE__) );
		wp_enqueue_style( 'my_database_admin_css');
		my_database_admin_admin();
	}
	
	
	public function database_my_admin_role_based_access(){	
		my_database_admin_role_based_access();
	}



}
