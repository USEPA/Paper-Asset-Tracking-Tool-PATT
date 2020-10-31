<?php
ini_set('display_errors', false );
set_time_limit(100);
define( 'WP_USE_THEMES', false );
/**
 * BEGIN LOAD WORDPRESS
 */
require_once __DIR__ . '/../../../../../wp-load.php';
require_once ABSPATH . WPINC . '/formatting.php';
require_once ABSPATH . WPINC . '/general-template.php';
require_once ABSPATH . WPINC . '/pluggable.php';
require_once ABSPATH . WPINC . '/link-template.php';
function wpsc_switch_to_blog_cache_clear( $blog_id, $prev_blog_id = 0 ) {
	if ( $blog_id === $prev_blog_id )
		return;
  wp_cache_delete( 'notoptions', 'options' );
	wp_cache_delete( 'alloptions', 'options' );
}
add_action( 'switch_blog', 'wpsc_switch_to_blog_cache_clear', 10, 2 );
if(is_multisite()){
  $blog_id =  isset($_REQUEST['blog_id']) ? sanitize_text_field($_REQUEST['blog_id']) : 1;
  switch_to_blog( $blog_id );
}
/**
 * END LOAD WORDPRESS
 */
$cron_job_schedule = get_option('wpsc_cron_job_schedule_setup');
if(!$cron_job_schedule) {
	if (!(get_option('wpsc_db_version') < '2.0'))  {
		do_action('wpsc_cron');
	}
}

