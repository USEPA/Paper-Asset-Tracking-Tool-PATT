<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       My Database Admin
 * Plugin URI:        my-database-admin
 * Description:       Allows Read, Write, Update operations on database from Admin Dashboard
 * Version:           1.1.21
 * Author:            wpshrike
 * Author URI:        https://profiles.wordpress.org/wpshrike
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       my-database-admin
 */


define( 'MY_DATABASE_ADMIN_VERSION', '1.1.1' );


function activate_my_database_admin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-my-database-admin-activator.php';
	My_Database_Admin_Activator::activate();
}

function deactivate_my_database_admin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-my-database-admin-deactivator.php';
	My_Database_Admin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_my_database_admin' );
register_deactivation_hook( __FILE__, 'deactivate_my_database_admin' );

require plugin_dir_path( __FILE__ ) . 'includes/class-my-database-admin.php';

function run_my_database_admin() {

	$plugin = new my_database_admin();
	$plugin->run();

}
run_my_database_admin();
