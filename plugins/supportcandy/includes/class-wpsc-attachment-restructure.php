<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !class_exists('WPSC_Attach_Restruct') ) :
    
    final class WPSC_Attach_Restruct {
        
        /**
         * Initialize the class
         */
        public static function init() {
            
            add_action( 'wpsc_attachment_restructure', array( __CLASS__, 'wpsc_attach_restruct_job') );

        }
        
        public static function wpsc_attach_restruct_job() {
            
            $is_attachments_completed = get_option( 'wpsc_attach_migrate', 0 );

            // Migrate attachments uploaded either by reply, note or create ticket attchement action
            if( !$is_attachments_completed ) {
                
                $attachments = get_terms([
                    'taxonomy'   => 'wpsc_attachment',
                    'hide_empty' => false,
                    'number' 	 => 20,
                    'orderby'    => 'id',
                    'order'      => 'DESC',
                    'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'is_restructured',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key'     => 'active',
                        'value'   =>  1,
                        'compare' => '='
                    ),
                    ),
                ]);

                if( $attachments ){

                    foreach( $attachments as $attach ){
                        $updated_time   = get_term_meta($attach->term_id,'time_uploaded',true);
                        $time  = strtotime($updated_time);
                        $month = date("m",$time);
                        $year  = date("Y",$time);
                    
                        $upload_dir = wp_upload_dir();
                        if (!file_exists($upload_dir['basedir'] . '/wpsc/'.$year)) {
                            mkdir($upload_dir['basedir'] . '/wpsc/'.$year, 0755, true);
                        }
                        if (!file_exists($upload_dir['basedir'] . '/wpsc/'.$year.'/'.$month)) {
                            mkdir($upload_dir['basedir'] . '/wpsc/'.$year.'/'.$month, 0755, true);
                        }
                    
                        $file_name = get_term_meta($attach->term_id,'save_file_name',true);
                        $old_file_path = $upload_dir['basedir'].'/'.'wpsc'.'/'.$file_name;
                        
                        if(file_exists($old_file_path)){
                            $new_path = $upload_dir['basedir'].'/'.'wpsc'.'/'.$year.'/'.$month.'/'.$file_name;
                            $fmoved = rename($old_file_path,$new_path);
                            if($fmoved){
                                update_term_meta( $attach->term_id, 'is_restructured',1);
                            }
                        }
                    }

                } else {
                    
                    update_option( 'wpsc_attach_migrate', 1 );
                }
            }

            // Migrate description attachments uploaded via tinymce
            if( $is_attachments_completed ) {
                global $wpdb;
                
                $allowedExtensions = array('jpg','JPG','jpeg','JPEG','png','PNG','gif','GIF');
                
                $upload_dir = wp_upload_dir();

                $today = new DateTime();
                $month = $today->format('m');
                $year  = $today->format('Y');

                if (!file_exists($upload_dir['basedir'] . '/wpsc/'.$year)) {
                    mkdir($upload_dir['basedir'] . '/wpsc/'.$year, 0755, true);
                }
                if (!file_exists($upload_dir['basedir'] . '/wpsc/'.$year.'/'.$month)) {
                    mkdir($upload_dir['basedir'] . '/wpsc/'.$year.'/'.$month, 0755, true);
                }

                $counter = 0;

                $files = glob($upload_dir['basedir'] . '/wpsc/*.*');
                $files_applicable = array();

                foreach ($files as $filename) {
                    
                    $file_parts = explode( ".", $filename );

                    if( in_array( $file_parts[1], $allowedExtensions) ) {
                        $files_applicable[] = $filename;
                    }
                }

                foreach($files_applicable as $filename) {
                    
                    $counter++;

                    $path_parts = explode( "/", $filename );
                    $filename = $path_parts[count($path_parts)-1];
                    
                    $old_url = $upload_dir['baseurl'] . '/wpsc/' . $filename;
                    $old_dir = $upload_dir['basedir'] . '/wpsc/' . $filename;
                    $new_dir = $upload_dir['basedir'] . '/wpsc/'.$year.'/'.$month.'/'.$filename;

                    // Move file to new path
                    $fmoved = rename( $old_dir, $new_dir);
                    
                    if($fmoved){
                        
                        // Create new term
                        $attachment_count = get_option('wpsc_attachment_count');
                        if(!$attachment_count) $attachment_count = 1;
                        $term = wp_insert_term( 'attachment_img_'.$attachment_count, 'wpsc_image_attachment' );
                        if(!$term || is_wp_error($term)) die();
                        update_option('wpsc_attachment_count', ++$attachment_count);

                        // Create new URL
                        add_term_meta ($term['term_id'], 'file_path', $new_dir);
                        $new_url = site_url() . "?wpsc_img_attachment=" . $term['term_id'];
                        
                        $wpdb->query("UPDATE {$wpdb->prefix}posts SET post_content = REPLACE(post_content,'".$old_url."','".$new_url."')");
                    }

                    if($counter == 20) break;
                }

                if( !count($files_applicable) ){
                    update_option( 'wpsc_restructured_attach_completed', 1 );
                    delete_option( 'wpsc_attach_migrate' );
                    
                    if (!file_exists($upload_dir['basedir'] . '/wpsc/')) {
                        mkdir($upload_dir['basedir'] . '/wpsc/', 0755, true);
                    }
                    $data = 'Deny from all';

                    $fh = fopen($upload_dir['basedir'] . '/wpsc/' . "/.htaccess", "w");
                    fwrite($fh, $data);
                    fclose($fh);
                }

            }

        }    
    }

endif;

WPSC_Attach_Restruct::init();