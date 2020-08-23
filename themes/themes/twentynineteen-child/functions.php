<?php
/**
 * Functions - Twentynineteen Child theme custom functions
 */


/**
 * Loads the parent stylesheet.
 */

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

/**
 * Change Admin Media Menu Label
 */
function patt_admin_media_menu_rename() {
     global $menu; // Global to get menu array
     global $submenu; // Global to get submenu array
     $menu[10][0] = 'Documents'; // Change name of Media to Documents
     $submenu['upload.php'][5][0] = 'All Document Items'; // Change name of Library to All Document Items
}
add_action( 'admin_menu', 'patt_admin_media_menu_rename' );

/****************************************************************************************************************/










/**
 * Remove Wordpress Logo.
 */
function example_admin_bar_remove_logo() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );
}
add_action( 'wp_before_admin_bar_render', 'example_admin_bar_remove_logo', 0 );

/**
 * Add ERMD Footer.
 */
  
function remove_footer_admin () 
{
    echo '<span id="footer-thankyou">For technical support please contact ERMD: <a href="mailto:ecms@epa.gov">ecms@epa.gov</a></span>';
}
 
add_filter('admin_footer_text', 'remove_footer_admin');


if(function_exists('add_db_table_editor')){
add_db_table_editor('title=Record Schedule Editor&table=wpqa_epa_record_schedule');
add_db_table_editor('title=File Folder Details&table=wpqa_wpsc_epa_folderdocinfo');
add_db_table_editor('title=Error Log&table=wpqa_epa_error_log');
add_db_table_editor('title=Shipping Table&table=wpqa_wpsc_epa_shipping_tracking');
}