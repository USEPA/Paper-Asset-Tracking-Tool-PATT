<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Rest_Child' ) ) :
  
  final class WPSC_Rest_Child extends WP_REST_Request {
    
      /**
       * Set rest post data
       */
      public function setApiParams($postdata){
        
          $this->params['GET'] = $postdata;
        
      }
    
  }
  
endif;