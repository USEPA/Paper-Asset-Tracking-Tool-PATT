<?php
    
    //if ( ! defined( 'ABSPATH' ) ) {
    //	exit; // Exit if accessed directly
    //}
    
    $path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

global $wpdb;

 $GLOBALS['id'] = $_GET['id'];

    $box_array = Patt_Custom_Func::fetch_request_id(1);
            
    echo $box_array;
    
   // echo($GLOBALS['id']);
    
          
?>