<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Ticket_Form_Field' ) ) :
    
    class WPSC_Ticket_List {
        
        var $slug;
        var $type;
		var $placeholder_text;
        var $label;
        var $extra_info;
		var $status;
		var $options;
        var $required;
        var $width;
        var $col_class;
		var $visibility;
		var $visibility_conditions;
		var $field_label;
		var $ticket_id;
		
		function __construct(){
			$this->field_label 	= get_option('wpsc_custom_fields_localize');
			$this->extra_info 	= get_option('wpsc_custom_fields_extra_info');
			$this->ticket_id 	= isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0;
		}

        function print_field($field){
          
			$this->slug = $field->slug;
			$this->type = get_term_meta( $field->term_id, 'wpsc_tf_type', true);
			$this->placeholder_text = get_term_meta( $field->term_id, 'wpsc_tf_placeholder_text', true);
			$this->status = get_term_meta( $field->term_id, 'wpsc_tf_status', true);
			$this->options = get_term_meta( $field->term_id, 'wpsc_tf_options', true);
			$this->required = get_term_meta( $field->term_id, 'wpsc_tf_required', true);
			$this->width = get_term_meta( $field->term_id, 'wpsc_tf_width', true);
			$this->visibility = get_term_meta( $field->term_id, 'wpsc_tf_visibility', true);
			$this->visibility_conditions = is_array($this->visibility) && $this->visibility ? implode(';;', $this->visibility) : '';
			$this->visibility_conditions = str_replace('"','&quot;',$this->visibility_conditions);
			$this->limit = get_term_meta($field->term_id,'wpsc_tf_limit',true);
			$this->limit = $this->limit ? $this->limit:"0";
			$this->col_class = 'col-sm-12';
          
          	if ($this->type=='0') {
            	switch ($field->slug) {
              		default:
						do_action('wpsc_print_edit_default_form_field', $field, $this);
						break;
            	}
						
          	} else {
						
				switch ($this->type) {
					
					case '1':
						$this->print_text_field($field);
						break;
						
					case '2':
						$this->print_drop_down($field);
						break;
						
					case '3':
						$this->print_checkbox($field);
						break;
						
					case '4':
						$this->print_radio_btn($field);
						break;
						
					case '5':
						$this->print_textarea($field);
						break;
						
					case '6':
						$this->print_date($field);
						break;
						
					case '7':
						$this->print_url($field);
						break;
						
					case '8':
						$this->print_email($field);
						break;
						
					case '9':
						$this->print_numberonly($field);
						break;
						
					case '10': 
						$this->print_file_attachment($field);
						break;
						
					case '18':
						$this->print_date_time($field);
						break;
					
					case '21':
						$this->print_time($field);
						break;


					default:
						do_action('wpsc_print_edit_custom_form_field', $field, $this);
						break;
				}
						
			}
          
        }
        
				function print_text_field($field){
					global $wpscfunction;
					$label = $wpscfunction->get_ticket_meta($this->ticket_id,$field->slug, true);
          ?>
          <div  data-fieldtype="text" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_date" placeholder=" <?php echo $this->placeholder_text; ?>" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo $label?>"  onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);">
          </div>
          <?php
        }
				
				function print_drop_down($field){
					global $wpscfunction;
					$label = $wpscfunction->get_ticket_meta($this->ticket_id, $field->slug, true );
					?>
          <div  data-fieldtype="dropdown" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<select id="<?php echo $this->slug;?>" class="form-control wpsc_drop_down" name="<?php echo $this->slug;?>">
			        <option value=""></option>
							<?php
							foreach ( $this->options as $key => $val ) :
									$value = trim(stripcslashes($val));
									$selected = $value == $label ? 'selected="selected"' : '';
                    ?>
                    
										<option <?php echo $selected?> value="<?php echo $value?>"><?php echo stripcslashes($val)?></option>
										
                    <?php
              endforeach;
			        ?>
			      </select>
          </div>
          <?php
				}
				
				function print_checkbox($field){
					global $wpscfunction;
					$label = $wpscfunction->get_ticket_meta($this->ticket_id, $field->slug);
					?>
          <div  data-fieldtype="checkbox" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<input type="hidden" name="<?php echo $field->slug ?>" value="">
						<?php
						foreach ( $this->options as $key => $value ) :
							$checked =in_array( $value, $label) ? 'checked="checked"' : '';
							?>
							<div class="row">
				        <div class="col-sm-12" style="margin-bottom:10px; display:flex;">
				          <div style="width:25px;"><input <?php echo $checked ?> type="checkbox" class="wpsc_checkbox" name="<?php echo $this->slug?>[]" value="<?php echo str_replace('"','&quot;',$value)?>"></div>
				          <div style="padding-top:3px;"><?php echo htmlentities($value)?></div>
				        </div>
              </div>
							<?php
						endforeach;
						?>
          </div>
          <?php
				}
				
				function print_radio_btn($field){
					global $wpscfunction;
					$label = $wpscfunction->get_ticket_meta($this->ticket_id, $field->slug, true );
					?>
          <div  data-fieldtype="radio" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<input type="hidden" name="<?php echo $field->slug ?>" value="">
						<?php
						foreach ( $this->options as $key => $value ) :
							$checked = $value == $label ? 'checked="checked"' : '';
              ?>
							<div class="row">
				        <div class="col-sm-12" style="margin-bottom:10px; display:flex;">
				          <div style="width:25px;"><input <?php echo $checked ?>type="radio" class="wpsc_radio_btn" name="<?php echo $this->slug?>" value="<?php echo str_replace('"','&quot;',$value)?>"></div>
				          <div style="padding-top:3px;"><?php echo htmlentities($value)?></div>
				        </div>
              </div>
							<?php
						endforeach;
						?>
          </div>
          <?php
				}
				
				function print_textarea($field){
					global $wpscfunction;
					$label = $wpscfunction->get_ticket_meta($this->ticket_id, $field->slug, true );
					$data  = stripslashes($label);
					?>
          <div  data-fieldtype="textarea" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <textarea id="<?php echo $this->slug;?>" class="wpsc_textarea" placeholder=" <?php echo $this->placeholder_text; ?>" name="<?php echo $this->slug;?>" onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);"><?php echo htmlentities($data) ?></textarea>
          </div>
          <?php
				}
				
				function print_date($field){
					global $wpscfunction;
					$value = $wpscfunction->get_ticket_meta($this->ticket_id, $field->slug,true);
					$date='';
					$date_range = get_term_meta ($field->term_id, 'wpsc_date_range',true);
					if( strlen($value) != 0 ){
						$date = $wpscfunction->datetimeToCalenderFormat($value);
					}
					$dr = "";
					if($date_range == 'future'){
						$dr = "minDate: 0";
					}elseif($date_range == 'past'){
						$dr = "maxDate: 0";
					}
				?>
          <div  data-fieldtype="date" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_date" name="<?php echo $this->slug;?>" autocomplete="off" placeholder="<?php echo $this->placeholder_text; ?>" value="<?php  echo $date;?>" onkeypress='return false'>
          </div>
					<script type="text/javascript">
						jQuery(document).ready(function(){
							jQuery("#<?php echo $this->slug?>").datepicker({
						    dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
					        showAnim : 'slideDown',
					        changeMonth: true,
					        changeYear: true,
					        yearRange: "-100:+100",
							<?php echo $dr?>

					    });
						});
					</script>
          <?php
				}
				
			function print_url($field){
					global $wpscfunction;
					$label = $wpscfunction->get_ticket_meta($this->ticket_id, $field->slug, true );
					?>
          <div  data-fieldtype="url" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_url" placeholder=" <?php echo $this->placeholder_text; ?>" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo $label ?>"  onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);">
          </div>
          <?php
				}
				
			function print_email($field){
					global $wpscfunction;
					$label = $wpscfunction->get_ticket_meta($this->ticket_id, $field->slug, true );
					?>
          <div  data-fieldtype="email" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_email" placeholder=" <?php echo $this->placeholder_text; ?>" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo $label ?>"  onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);">
          </div>
          <?php
				}
				
			function print_numberonly($field){
					global $wpscfunction;
					$label = $wpscfunction->get_ticket_meta($this->ticket_id, $field->slug, true );
					?>
          <div  data-fieldtype="number" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <input type="number" id="<?php echo $this->slug;?>" class="form-control wpsc_numberonly" placeholder="<?php echo $this->placeholder_text; ?>" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo $label ?>" onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);">
          </div>
          <?php
				}
				
			function print_file_attachment($field){
					global $wpscfunction;
					$ticket_auth_code = $wpscfunction->get_ticket_fields($this->ticket_id,'ticket_auth_code');
					$auth_id          = $ticket_auth_code;
					$attachments = $wpscfunction->get_ticket_meta($this->ticket_id,$field->slug);
					?>
          <div data-fieldtype="file_attachment" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?>
            </label>
            <div class="row attachment" style="margin-bottom:10px;">
	  				<div class="row attachment_link">
	  					<span onclick="wpsc_attachment_upload('<?php echo 'attach_'.$field->term_id?>','<?php echo $field->slug ?>');"><?php _e('Attach file','supportcandy')?></span>
						   <div id="<?php  echo 'attach_'.$field->term_id?>" class="row attachment_container" ></div>
							    <?php		
							      $notice_flag = true;
			                      $wpsc_allow_attachment_type = get_option('wpsc_allow_attachment_type');
			                      $max_attachment_limit = get_option('wpsc_attachment_max_filesize');
			                      $wpsc_show_attachment_notice  = get_option('wpsc_show_attachment_notice');
			                         if($wpsc_show_attachment_notice && $notice_flag){
				                         $attach_notice = get_option('wpsc_attachment_notice');
				                ?> 
				                     <p class="help-block"> <i> <?php echo $attach_notice;?></i></p>
		                           <?php 
			                         }
		                            ?>
                    </div>
			</div>
				<?php if($attachments): ?>
						<table class="wpsc_attachment_tbl">
							<tbody>
								<?php
								foreach( $attachments as $attachment):
									$attach      = array();
									$attach_meta = get_term_meta($attachment);
									foreach ($attach_meta as $key => $value) {
										$attach[$key] = $value[0];
									}
									$upload_dir   = wp_upload_dir();
									
									$wpsp_file = get_term_meta($attachment,'wpsp_file');
									
									if ($wpsp_file) {
										$file_url = $upload_dir['basedir']  . '/wpsp/'. $attach['save_file_name'];
									}else {
										if( isset($attach['is_restructured']) && $attach['is_restructured']){
											$updated_time   = get_term_meta($attachment,'time_uploaded',true);
											$time  = strtotime($updated_time);
											$month = date("m",$time);
											$year  = date("Y",$time);
											$file_url = $upload_dir['basedir'] . '/wpsc/'.$year.'/'.$month.'/'. $attach['save_file_name'];
										}else{
											$file_url = $upload_dir['basedir'] . '/wpsc/'. $attach['save_file_name'];
										}
									}
								
									$download_url = site_url().'?wpsc_attachment='.$attachment.'&tid='.$this->ticket_id.'&tac='.$auth_id;
									?>
									<tr>
										<td>
											<div class="" id="<?php echo 'attach_'.$attachment; ?>">
												<input type="hidden" name="<?php echo $field->slug ?>[]" value="<?php echo htmlentities($attachment) ?>">
												<span style="padding: 7px;"><a href="<?php echo $download_url?>" target="_blank"><?php echo $attach['filename'];?></a></span>
												<span onclick="wpsc_delete_attached_files('<?php echo $attachment ?>','<?php echo $this->ticket_id ?>','<?php echo $field->slug?>');" class="fa fa-trash" ></span>
											</div>
										</td>
									</tr>
									<div>
								<?php	endforeach;?>
							</tbody>
						</table>
					<?php endif;  ?>
					</div>
				
					
          <?php
		}
		
				
				function print_date_time($field){
					global $wpscfunction;
					$value = $wpscfunction->get_ticket_meta($this->ticket_id, $field->slug,true);
					$date='';
					if( strlen($value) != 0 ){
						$date = $value;
					}
					?>
          <div  data-fieldtype="date" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_datetime" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo $date;?>" placeholder="<?php echo $this->placeholder_text; ?>" onkeypress='return false'>
          </div>
					<script type="text/javascript">
						jQuery(document).ready(function(){
							jQuery('.wpsc_datetime').datetimepicker({
								dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
								 showAnim : 'slideDown',
								 changeMonth: true,
								 changeYear: true,
								timeFormat: 'HH:mm:ss'
							})
						});
					</script>
          <?php
				}

        function print_time($field){
			global $wpscfunction;
			$time_format = get_term_meta($field->term_id,'wpsc_time_format',true);
			$value = $wpscfunction->get_ticket_meta($this->ticket_id, $field->slug,true);
			$time  = '';

			if( strlen($value) != 0 ){
				if($time_format == '12'){
					$time = date("h:i:s a", strtotime($value));

				}else{
					$time = $value;
				}
			}
			$time_format = get_term_meta($field->term_id,'wpsc_time_format',true);
			?>
			<div  data-fieldtype="time" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?>  <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
				<label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
				<?php echo $this->field_label['custom_fields_'.$field->term_id];?> 
				</label>
				<?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
				<input type="text" id="<?php echo $this->slug;?>" onkeypress='return false' class="form-control <?php echo $this->slug ?>" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo $time;?>">
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery('.<?php echo $this->slug ?>').timepicker({
						<?php 
						if($time_format == '12'){
							?>
							timeFormat:'hh:mm:ss tt',
							<?php	
						}else{
							?>
							timeFormat:'HH:mm:ss'
							<?php
						}
						?>
					});	
				});
				
			</script>
			<?php
		}

	}
		
    
endif;