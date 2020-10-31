<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSCWPCron' ) ) :
   
   final class WPSCWPCron {
       public function wpsc_cron_job() {
				 if (!(get_option('wpsc_db_version') < '2.0'))  {
					 do_action('wpsc_cron');
				}
       }    
   }
endif;