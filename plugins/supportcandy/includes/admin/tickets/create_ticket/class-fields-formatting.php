<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Ticket_Field_Formatting' ) ) :

    class WPSC_Ticket_Field_Formatting {
        
       public function get_field_val($field){
         
				 global $wpscfunction;
				 $ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0;
				 $auth_id = $wpscfunction->get_ticket_fields($ticket_id,'ticket_auth_code');
 				 $label = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
         $wpsc_tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type',true);
				 $custom_fields_localize = get_option('wpsc_custom_fields_localize');
				 $field_label = $custom_fields_localize['custom_fields_'.$field->term_id];
				 //$field_label = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
				 
				 
         if($wpsc_tf_type==10){
            $attachments = $wpscfunction->get_ticket_meta($ticket_id,$field->slug);
						?>
            <div class="wpsp_sidebar_labels">
              <strong><?php _e($field_label,'supportcandy')?>:</strong> 
              <table class="wpsc_attachment_tbl">
                <tbody>
                  <?php
                  foreach( $attachments as $attachment ):
                    $attach      = array();
                    $attach_meta = get_term_meta($attachment);
                    foreach ($attach_meta as $key => $value) {
                      $attach[$key] = $value[0];
                    }
                    $upload_dir   = wp_upload_dir();
										$wpsp_file = get_term_meta($attachment,'wpsp_file');
										
										if ($wpsp_file) {
											$file_url = $upload_dir['baseurl']  . '/wpsp/'. $attach['save_file_name'];
										}else {
                      if( isset($attach['is_restructured']) && $attach['is_restructured']){
                        $updated_time   = get_term_meta($attachment,'time_uploaded',true);
                        $time  = strtotime($updated_time);
                        $month = date("m",$time);
                        $year  = date("Y",$time);
                        $file_url = $upload_dir['baseurl'] . '/wpsc/'.$year.'/'.$month.'/'. $attach['save_file_name'];
                      }else{
                        $file_url = $upload_dir['baseurl'] . '/wpsc/'. $attach['save_file_name'];
                      }
										}
                    
                    $download_url = site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
                  ?>
                  <tr>
                    <td>
                        <input type="hidden" name="<?php echo $field->slug ?>[]" value="<?php echo htmlentities($attach['filename']) ?>">
                        <span style="padding: 7px;"><a href="<?php echo $download_url?>" target="_blank"><?php echo $attach['filename'];?></a></span>
                      </td>
  								</tr>
                  <?php	endforeach;?>
                </tbody>
              </table>
						</div>
              <?php	
          }elseif($wpsc_tf_type == 7) {
						if (!preg_match("~^(?:f|ht)tps?://~i", $label)) {
								$url = "http://" . $label;
						 }else{
							 $url = $label;
							}
              ?>
              <div class="wpsp_sidebar_labels"><strong><?php _e($field_label,'supportcandy')?>:</strong> <a href="<?php echo $url ?>" target="_blank"><?php _e('Click Here','supportcandy')?></a></div>
              <?php
          }elseif ($wpsc_tf_type == 8 ) {
            ?>
            <div class="wpsp_sidebar_labels"><strong><?php _e($field_label,'supportcandy')?>:</strong> <a href="<?php echo 'mailto:'.$label?>" ><?php echo htmlentities($label) ?></a></div>
            <?php
          }elseif ($wpsc_tf_type == 5) {
            ?>
            <div class="wpsp_sidebar_labels"><strong><?php _e($field_label,'supportcandy')?>:</strong> <br> <?php echo  nl2br( stripcslashes( $label) ); ?></div>
            <?php
          }elseif ($wpsc_tf_type == 6) {
						?>
            <div class="wpsp_sidebar_labels"><strong><?php _e($field_label,'supportcandy')?>:</strong> <?php echo $wpscfunction->datetimeToCalenderFormat($label); ?></div>
            <?php
          }elseif($wpsc_tf_type == 3){
            $ticket_meta = $wpscfunction->get_ticket_meta($ticket_id,$field->slug);
						$meta_values = implode(", ", $ticket_meta);
            ?>
            <div class="wpsp_sidebar_labels"><strong><?php _e($field_label,'supportcandy')?>:</strong> <?php echo htmlentities($meta_values)?></div>
            <?php
              
          } else if($wpsc_tf_type == 1 || $wpsc_tf_type == 2 || $wpsc_tf_type == 4 || $wpsc_tf_type == 9 ){
							?>
							<div class="wpsp_sidebar_labels"><strong><?php _e($field_label,'supportcandy')?>:</strong> <?php echo htmlentities($label)?></div>
							<?php
					}else if($wpsc_tf_type == 18 ){
							?>
							<div class="wpsp_sidebar_labels"><strong><?php _e($field_label,'supportcandy')?>:</strong> <?php echo $label?></div>
							<?php
					}elseif($wpsc_tf_type == 21){
            $time_format = get_term_meta($field->term_id,'wpsc_time_format',true);
            ?>
							<div class="wpsp_sidebar_labels"><strong><?php _e($field_label, 'supportcandy')?>:</strong> <?php  if($time_format == '12'){echo date("h:i:s a", strtotime($label));}else{ echo $label;} ?></div>
							<?php
          }else {
							 do_action( 'wpsc_widget_ticket_field_item', $field, $ticket_id,$wpsc_tf_type);
       		}
       }
  }

endif;
