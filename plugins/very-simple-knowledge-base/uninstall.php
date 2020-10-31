<?php
// exit if uninstall is not called
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// delete option
delete_option( 'widget_vskb_widget' );

// delete site option in multisite
delete_site_option( 'widget_vskb_widget' );
