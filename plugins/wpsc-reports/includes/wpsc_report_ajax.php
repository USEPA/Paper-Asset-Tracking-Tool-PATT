<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Report_Ajax' ) ) :
    
    /**
     * Ajax class for WPSC.
     * @class WPSC_Ajax
     */
    class WPSC_Report_Ajax {
        
        /**
         * Constructor
         */
        public function __construct(){
          $ajax_events = array(
						'ticket_reports'			=> false
					);
          
           foreach ($ajax_events as $ajax_event => $nopriv) {
							add_action('wp_ajax_wpsc_' . $ajax_event, array($this, $ajax_event));
              if ($nopriv) {
                  add_action('wp_ajax_nopriv_wpsc_' . $ajax_event, array($this, $ajax_event));
              }
           }
        }
        
        /**
         * Ticket Reports Ajax query
         */
        public function ticket_reports(){
          
            include WPSC_RP_ABSPATH . 'includes/ticket_reports.php';
            die();
        }
				
		}
    
endif;

new WPSC_Report_Ajax();