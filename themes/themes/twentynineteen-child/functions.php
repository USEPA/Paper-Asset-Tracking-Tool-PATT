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
add_db_table_editor('title=Term&table=wpqa_terms');
add_db_table_editor('title=Term Meta&table=wpqa_termmeta');
add_db_table_editor('title=User&table=wpqa_users');
add_db_table_editor('title=User Meta&table=wpqa_usermeta');
add_db_table_editor('title=Email Notification&table=wpqa_wpsc_email_notification');
add_db_table_editor('title=EPA Box Info&table=wpqa_wpsc_epa_boxinfo');
add_db_table_editor('title=EPA Folder Doc Info&table=wpqa_wpsc_epa_folderdocinfo');
add_db_table_editor('title=EPA Location Status&table=wpqa_wpsc_epa_location_status');
add_db_table_editor('title=EPA Progrm Office&table=wpqa_wpsc_epa_program_office');
add_db_table_editor('title=EPA Recall Request&table=wpqa_wpsc_epa_recallrequest');
add_db_table_editor('title=EPA Recall Request Users&table=wpqa_wpsc_epa_recallrequest_users');
add_db_table_editor('title=EPA Return&table=wpqa_wpsc_epa_return');
add_db_table_editor('title=EPA Return Users&table=wpqa_wpsc_epa_return_users');
add_db_table_editor('title=EPA RFID Data&table=wpqa_wpsc_epa_rfid_data');
add_db_table_editor('title=EPA Scan List&table=wpqa_wpsc_epa_scan_list');
add_db_table_editor('title=EPA Shipping Tracking&table=wpqa_wpsc_epa_shipping_tracking');
add_db_table_editor('title=EPA Storage Location&table=wpqa_wpsc_epa_storage_location');
add_db_table_editor('title=EPA Storage Status&table=wpqa_wpsc_epa_storage_status');
add_db_table_editor('title=EPA Reports&table=wpqa_wpsc_reports');
add_db_table_editor('title=EPA SLA Reports&table=wpqa_wpsc_sla_reports');
add_db_table_editor('title=EPA Ticket&table=wpqa_wpsc_ticket');
add_db_table_editor('title=EPA Ticket Meta&table=wpqa_wpsc_ticketmeta');
}