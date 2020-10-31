<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_ATC_Install' ) ) :

  final class WPSC_ATC_Install {

    public function __construct() {
      $this->check_version();
    }


    /**
     * Check version of WPSC
     */
    public function check_version(){
        $installed_version = get_option( 'wpsc_atc_current_version', 0 );
        if( $installed_version != WPSC_ATC_VERSION ){
            add_action( 'init', array($this,'upgrade'), 101 );
        }
    }

    // Upgrade
		public function upgrade(){
			
			$installed_version = get_option( 'wpsc_atc_current_version', 0 );
		  $installed_version = $installed_version ? $installed_version : 0;
			
			if ( $installed_version < '1.0.0' ) {
				
				update_option('wpsc_atc_age','0');
				update_option('wpsc_atc_waring_email_age','1');
				
				update_option('wpsc_atc_waring_email_age', '1');
				update_option('wpsc_atc_subject', 'Your ticket #{ticket_id} will be closed for inactivity','wpsc-atc');
				update_option('wpsc_atc_email_body', '<p>Dear&nbsp;{customer_name},</p><p>This is to inform that your ticket #{ticket_id} will be closed soon for your inactivity in the ticket. We are waiting for your reply for a while. You can reply on below link before it get closed -</p><p>{ticket_url}</p>');
				
			}
			update_option( 'wpsc_atc_current_version', WPSC_ATC_VERSION );
			
		}

  }
endif;

new WPSC_ATC_Install();
