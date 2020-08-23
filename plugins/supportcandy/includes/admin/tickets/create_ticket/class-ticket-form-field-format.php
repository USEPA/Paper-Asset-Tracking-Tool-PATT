<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Ticket_Form_Field' ) ) :
    
    class WPSC_Ticket_Form_Field {
        
        var $slug;
        var $type;
        var $label;
        var $extra_info;
		var $status;
		var $options;
        var $required;
        var $width;
        var $col_class;
		var $visibility;
		var $visibility_conditions;
		var $limit;
		var $placeholder;
		var $html_content;		
		var $wpsc_appearance_create_ticket;

		function __construct(){
			$this->wpsc_appearance_create_ticket = get_option('wpsc_create_ticket');
			$this->label = get_option('wpsc_custom_fields_localize');
			$this->extra_info = get_option('wpsc_custom_fields_extra_info');
		}

        function print_field($field){
          
          	$this->slug = $field->slug;
			$this->type = get_term_meta( $field->term_id, 'wpsc_tf_type', true);
			$this->placeholder = get_term_meta($field->term_id,'wpsc_tf_placeholder_text',true);
			$this->status = get_term_meta( $field->term_id, 'wpsc_tf_status', true);
			$this->options = get_term_meta( $field->term_id, 'wpsc_tf_options', true);
			$this->required = get_term_meta( $field->term_id, 'wpsc_tf_required', true);
			$this->width = get_term_meta( $field->term_id, 'wpsc_tf_width', true);
			$this->visibility = get_term_meta( $field->term_id, 'wpsc_tf_visibility', true);
			$this->visibility_conditions = is_array($this->visibility) && $this->visibility ? implode(';;', $this->visibility) : '';
			$this->visibility_conditions = str_replace('"','&quot;',$this->visibility_conditions);
			$this->limit = get_term_meta($field->term_id,'wpsc_tf_limit',true);
			$this->limit = $this->limit ? $this->limit:"0";
		  	$this->html_content = get_term_meta($field->term_id ,'wpsc_html_content',true);			
						
			switch ($this->width) {
				case '1/3':
					$this->col_class = 'col-sm-4';
				break;
				
				case '1/2':
					$this->col_class = 'col-sm-6';
				break;
					
				case '1':
					$this->col_class = 'col-sm-12';
				break;
			}
          
			if ($this->type=='0') {
				switch ($field->slug) {
				
					case 'customer_name':
						$this->print_customer_name($field);
						break;
									
					case 'customer_email':
						$this->print_customer_email($field);
						break;
						
					case 'ticket_subject':
						if ($this->status == '1') {
							//PATT $this->print_ticket_subject($field);
						}
						break;
						
					case 'ticket_description':
						if ($this->status == '1') {
							$this->print_ticket_desc($field);
						}
						break;
						
					case 'ticket_category':
						if ($this->status == '1') {
							//PATT $this->print_ticket_category($field);
						}
						break;
										
					case 'ticket_priority':
						if ($this->status == '1') {
							$this->print_ticket_priority($field);
						}
						break;
					
					default:
						do_action('wpsc_print_default_form_field', $field, $this);
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


					case '22' :
						$this->print_html($field);
						break;		
							
					default:
						do_action('wpsc_print_custom_form_field', $field, $this);
						break;
				}
							
			}
			
		}
//PATT BEGIN
function print_ticket_desc($field){
			?>
          	<input type="hidden" id="<?php echo $this->slug;?>" name="<?php echo $this->slug;?>" value="Request Created: <?php date_default_timezone_set('US/Eastern'); echo date("m/d/Y"); ?>" />
          <?php
        }
//PATT END

		function print_text_field($field){
			$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
			$value = apply_filters('wpsc_custom_text_default_value', '', $field);
			?>
          	<div  data-fieldtype="text" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            	<label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              		<?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
           		</label>
            	<?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            	<input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_date" placeholder="<?php echo $this->placeholder; ?>" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo $value?>" onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);">
          </div>
          <?php
        }
				
		function print_drop_down($field){
			$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
			$selected_value = apply_filters('wpsc_custom_dropdown_default_value', '', $field);
			?>
          	<div  data-fieldtype="dropdown" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
				<label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
				<?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
				</label>
				<?php 
				if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
				<select id="<?php echo $this->slug;?>" class="form-control wpsc_drop_down" name="<?php echo $this->slug;?>" >
					<option value=""><?php esc_html_e( 'Please Select', 'supportcandy' ); ?></option>
					<?php
					foreach ( $this->options as $key => $value ) :
						$selected = $selected_value == $value ? 'selected="selected"' : '' ;
						if ( 'N/A' !== $value ) {
							echo '<option '.$selected. 'value="'.str_replace('"','&quot;',$value).'">'.$value.'</option>';
						}
					endforeach;
					?>
				</select>
          	</div>
          <?php
		}
				
				function print_checkbox($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					?>
          <div  data-fieldtype="checkbox" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<?php
						foreach ( $this->options as $key => $value ) :
							?>
							<div class="row">
				        <div class="col-sm-12" style="margin-bottom:10px; display:flex;">
				          <div style="width:25px;"><input type="checkbox" class="wpsc_checkbox" name="<?php echo $this->slug?>[]" value="<?php echo str_replace('"','&quot;',$value)?>"></div>
				          <div style="padding-top:3px;"><?php echo $value?></div>
				        </div>
              </div>
							<?php
						endforeach;
						?>
          </div>
          <?php
				}
				
				function print_radio_btn($field){
					$selected_value = apply_filters('wpsc_custom_radio_default_value', '', $field);
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					?>
          <div  data-fieldtype="radio" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<?php
						foreach ( $this->options as $key => $value ) :
							$selected = $selected_value == $value ? 'checked="checked"' : '' ;
							?>
							<div class="row">
				        <div class="col-sm-12" style="margin-bottom:10px; display:flex;">
				          <div style="width:25px;"><input type="radio" <?php echo $selected ?> class="wpsc_radio_btn" name="<?php echo $this->slug?>" value="<?php echo str_replace('"','&quot;',$value)?>"></div>
				          <div style="padding-top:3px;"><?php echo $value?></div>
				        </div>
              </div>
							<?php
						endforeach;
						?>
          </div>
          <?php
				}
				
				function print_textarea($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					$value = apply_filters('wpsc_custom_textarea_default_value', '', $field);
					?>
          <div  data-fieldtype="textarea" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <textarea id="<?php echo $this->slug;?>" placeholder="<?php echo $this->placeholder;?>" class="wpsc_textarea" name="<?php echo $this->slug;?>" onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);"><?php echo $value?></textarea>
          </div>
          <?php
				}
				
				function print_date($field){
					global $wpscfunction;
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					$value = apply_filters('wpsc_custom_date_default_value', '', $field);
					$value = $wpscfunction->datetimeToCalenderFormat($value);
					$date_range = get_term_meta($field->term_id, 'wpsc_date_range',true);
					$dr = "";
					if($date_range == 'future'){
						$dr = "minDate: 0";
					}elseif($date_range == 'past'){
						$dr = "maxDate: 0";
					}
				?>
          <div  data-fieldtype="date" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_date" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo $value?>" placeholder="<?php echo $this->placeholder;?>" onkeypress='return false' >
          </div>
		  <script>
		  	jQuery( "#<?php echo $this->slug?>" ).datepicker({
				dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
				showAnim : 'slideDown',
				changeMonth: true,
				changeYear: true,
				yearRange: "-50:+50",
				<?php echo $dr?>
			});
		  </script>

          <?php
				}
				
				function print_url($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					$value = apply_filters('wpsc_custom_url_default_value', '', $field);
					
					?>
          <div  data-fieldtype="url" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_url" placeholder="<?php echo $this->placeholder;?>" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo $value?>" onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);">
          </div>
          <?php
				}
				
				function print_email($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					?>
          <div  data-fieldtype="email" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_email" placeholder="<?php echo $this->placeholder;?>" name="<?php echo $this->slug;?>" autocomplete="off" value=""  onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);">
          </div>
          <?php
				}
				
				function print_numberonly($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					$value = apply_filters('wpsc_custom_number_default_value', '', $field);
					?>
          <div  data-fieldtype="number" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <input type="number" id="<?php echo $this->slug;?>" class="form-control wpsc_numberonly" placeholder="<?php echo $this->placeholder;?>" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo $value?>" onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);" >
          </div>
          <?php
				}
				
				function print_customer_name($field){
					global $current_user;
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';							
					$readonly = '';
					if(!$current_user->has_cap('wpsc_agent') && is_user_logged_in()){
						$readonly = 'readonly';
					}
					$readonly = apply_filters('wpsc_customer_name_readonly',$readonly);
          ?>
          <div  data-fieldtype="text" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_customer_name" placeholder="<?php echo $this->placeholder; ?>" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo is_user_logged_in() ? $current_user->display_name :'' ?>" <?php echo $readonly ?>  onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);">
					</div>	
					<?php
				}
				
				function print_customer_email($field){
					global $current_user;
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					$readonly = '';
					if(!$current_user->has_cap('wpsc_agent') && is_user_logged_in()){
						$readonly = 'readonly';
					}
					$readonly = apply_filters('wpsc_customer_email_readonly', $readonly);
					?>
          <div  data-fieldtype="email" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>" ><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_email" placeholder= "<?php echo $this->placeholder;?>" name="<?php echo $this->slug;?>" autocomplete="off" value="<?php echo is_user_logged_in() ? $current_user->user_email :'' ?>" 
						<?php echo $readonly ?> onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);">
					</div>
					<?php
				}
        
				function print_ticket_subject($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
				?>
          <div  data-fieldtype="text" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>" ><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_subject" placeholder="<?php echo $this->placeholder; ?>" name="<?php echo $this->slug;?>" autocomplete="off" value=""  onkeypress="wpsc_text_limit(event,this,<?php echo $this->limit ?>);">
			
          </div>
					<?php
				}
			
				function print_file_attachment($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
          ?>
          <div data-fieldtype="file_attachment" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>" ><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<div class="row attachment" style="margin-bottom:20px;">
							<div id="<?php echo 'attach_'.$field->term_id?>" class="row attachment_container"></div>
	  					<div class="row attachment_link">
	  						<span onclick="wpsc_attachment_upload('<?php echo 'attach_'.$field->term_id?>','<?php echo $this->slug?>');"><?php _e('Attach file','supportcandy')?></span>
	  					</div>
	  				</div>
          </div>
          <?php
        }
				
				function print_date_time($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					?>
          <div  data-fieldtype="date" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
            <input type="text" id="<?php echo $this->slug;?>" class="form-control wpsc_datetime" name="<?php echo $this->slug;?>" autocomplete="off" value="" placeholder="<?php echo $this->placeholder;?>" onkeypress='return false'> 
          </div>
          <?php
				}
				
				function print_ticket_category($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					$wpsc_default_ticket_category = '';
					$wpsc_default_ticket_category = apply_filters('wpsc_default_ticket_category', $wpsc_default_ticket_category);
					
					$disabled = apply_filters('wpsc_disable_ticket_category','',$field,$wpsc_default_ticket_category);

					?>
          <div  data-fieldtype="dropdown" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>" ><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<select id="<?php echo $this->slug;?>" <?php echo $disabled?> class="form-control wpsc_category" name="<?php echo $this->slug;?>">
			        <option value=""></option>
							<?php
							$categories = get_terms([
							  'taxonomy'   => 'wpsc_categories',
							  'hide_empty' => false,
								'orderby'    => 'meta_value_num',
							  'order'    	 => 'ASC',
								'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
							]);
							
							$wpsc_default_ticket_category = '';
							$wpsc_default_ticket_category = apply_filters('wpsc_default_ticket_category', $wpsc_default_ticket_category);
							
							foreach ( $categories as $category ) :
								$wpsc_custom_category_localize = get_option('wpsc_custom_category_localize');
								$selected      = $wpsc_default_ticket_category == $category->term_id ? 'selected="selected"' : '';
								$flag   = apply_filters('wpsc_hide_category_create_ticket',true,$category->term_id);
								if( $flag ){
									echo '<option '.$selected.' value="'.$category->term_id.'">'.$wpsc_custom_category_localize['custom_category_'.$category->term_id].'</option>';
								}
			        endforeach;
			        ?>
			      </select>
				  <?php  do_action('wpsc_add_hidden_category_field',$field,$wpsc_default_ticket_category);?>

          </div>
          <?php
				}
				
				function print_ticket_priority($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					?>
          <div  data-fieldtype="dropdown" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>" ><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<select id="<?php echo $this->slug;?>" class="form-control wpsc_priority" name="<?php echo $this->slug;?>">
							<option value=""></option>
							<?php
			 				$priorities = get_terms([
			 				  'taxonomy'   => 'wpsc_priorities',
			 				  'hide_empty' => false,
			 					'orderby'    => 'meta_value_num',
			 				  'order'    	 => 'ASC',
			 					'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
			 				]);
			 				foreach ( $priorities as $priority ) :
								$wpsc_custom_priority_localize = get_option('wpsc_custom_priority_localize');
			           echo '<option value="'.$priority->term_id.'">'.$wpsc_custom_priority_localize['custom_priority_'.$priority->term_id].'</option>';
		          endforeach;
			         ?>
			      </select>
          </div>
          <?php
				}
        
				function print_ticket_description($field){
					global $current_user,$wpscfunction;
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					$directionality = $wpscfunction->check_rtl();
					$wpsc_allow_attach_create_ticket = get_option('wpsc_allow_attach_create_ticket');
					?>
					<div  data-fieldtype="tinymce" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            <label class="wpsc_ct_field_label">
              <?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
            </label>
            <?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
			<div class="form-group">
					<?php					
						$wpsc_allow_attachment_type = get_option('wpsc_allow_attachment_type');
						$max_attachment_limit = get_option('wpsc_attachment_max_filesize');
						$wpsc_show_attachment_notice  = get_option('wpsc_show_attachment_notice');
					?>
			</div>
            <textarea id="<?php echo $this->slug;?>" class="wpsc_textarea" name="<?php echo $this->slug;?>" placeholder ="<?php echo $this->placeholder; ?>" ></textarea>
		
						    <div class="row attachment" style="margin-bottom:20px;">
		  					<div class="row attachment_link">
								<?php 
								$notice_flag = false;
								if( (in_array('customers',$wpsc_allow_attach_create_ticket) && is_user_logged_in() && !$current_user->has_cap('wpsc_agent')) ||
										(in_array('agents',$wpsc_allow_attach_create_ticket) && $current_user->has_cap('wpsc_agent')) || 
										(in_array('guests',$wpsc_allow_attach_create_ticket) && !is_user_logged_in()) || $current_user->has_cap('edit_published_posts')){
											$notice_flag = true;
									?>
									<span onclick="wpsc_attachment_upload('<?php echo 'attach_'.$field->term_id?>','desc_attachment');"><?php _e('Attach file','supportcandy')?></span>
									<?php
								} 
								?>
									<?php if ($current_user->has_cap('wpsc_agent')):?>
									<span id="wpsc_insert_macros" onclick="wpsc_get_templates()" ><?php _e('Insert Macros','supportcandy')?></span>
									<?php endif;?>
									<?php do_action('wpsc_add_addon_tab_after_macro');?>
									<?php 
									if($wpsc_show_attachment_notice && $notice_flag){
										$attach_notice = get_option('wpsc_attachment_notice');
										?>
										<p class="help-block"> <i> <?php echo $attach_notice ?></i></p>
										<?php 
									}
									?>
		  					</div>
		  					<div id="<?php echo 'attach_'.$field->term_id?>" class="row attachment_container"></div>
							
							<?php do_action('wpsc_after_attachment_container'); ?>
		  				</div>
						</div>
					<?php 
					$wpsc_allow_rich_text_editor = get_option('wpsc_allow_rich_text_editor');
					$rich_editing = $wpscfunction->rich_editing_status($current_user);
					
					$flag = false;
					
					 if ( is_user_logged_in() && (in_array('register_user',$wpsc_allow_rich_text_editor) && !$current_user->has_cap('wpsc_agent')) && $rich_editing) {
						 $flag = true;
					 } elseif($current_user->has_cap('wpsc_agent') && $rich_editing){
						  $flag = true;
					 } elseif ( in_array('guest_user',$wpsc_allow_rich_text_editor) && (!is_user_logged_in())) {
						$flag = true;
					}
						if($flag){
						 $wpsc_tinymce_toolbar = get_option('wpsc_tinymce_toolbar');
						 $toolbar_active = get_option('wpsc_tinymce_toolbar_active');
						 $tinymce_toolbox = array();
						 foreach ($toolbar_active as $key => $value) {
						 		$tinymce_toolbox[] = $wpsc_tinymce_toolbar[$value]['value'];
								if($value == 'blockquote' || $value == 'align' || $value == 'numbered_list' || $value == 'right_to_left'){
									$tinymce_toolbox[] = ' | ';
								}
						 }
						 ?>
						 <script>
						 		tinymce.remove();
								
		          	tinymce.init({ 
		          	  selector:'#<?php echo $this->slug;?>',
		          	  body_id: '<?php echo $this->slug;?>',
		          	  menubar: false,
						directionality : '<?php echo $directionality; ?>',
									statusbar: false,
									autoresize_min_height: 150,
									<?php
									  $wpsc_allow_html_pasting = get_option('wpsc_allow_html_pasting');
                                      if(!$wpsc_allow_html_pasting){ ?>
									    paste_as_text: true,
									    <?php
									  } 
									?>

							    wp_autoresize_on: true,
							    plugins: [
										'wpautoresize lists link image directionality paste'
								  ],
		          	  toolbar: '<?php echo implode(' ', $tinymce_toolbox) ?> | wpsc_templates ',
									file_picker_types: 'image',
									file_picker_callback: function(cb, value, meta) {
										var input = document.createElement('input');
								    input.setAttribute('type', 'file');
								    input.setAttribute('accept', 'image/*');

										input.onchange = function() {
								      var file = this.files[0];
											var form_data = new FormData();
											form_data.append('file',file);
											form_data.append('file_name',file.name);
											form_data.append('action','wpsc_tickets');
											form_data.append('setting_action','rb_upload_file');
										
											jQuery.ajax({
												type : 'post',
												url : wpsc_admin.ajax_url,
								        cache: false,
								        contentType: false,
								        processData: false,
								        data: form_data,
								        success: function(response_data){
													var responce = JSON.parse(response_data);
													var reader   = new FileReader();
													reader.onload = function () {
														var id        = 'blobid' + (new Date()).getTime();
														var blobCache = tinymce.activeEditor.editorUpload.blobCache;
														var base64    = reader.result.split(',')[1];
														var blobInfo  = blobCache.create(id, file, base64);
														blobCache.add(blobInfo);
														if (responce) {
															cb(responce, { title: 'attach' });
														} else {
															alert("<?php _e('Attached file type not allowed!','supportcandy')?>");
														}	
														
													};
													reader.readAsDataURL(file);
												}
											});
										};
							    
										input.click();
									},
									branding: false,
									autoresize_bottom_margin: 20,
	          	  	browser_spellcheck : true,
	          	  	relative_urls : false,
		          	  remove_script_host : false,
		          	  convert_urls : true
		          	});
								
							</script>
						<?php
					}
				}

				function print_time($field){
					$extra_info_css = 'color:'.$this->wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';
					$time_format = get_term_meta($field->term_id,'wpsc_time_format',true);
					?>
					<div data-fieldtype="time" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
						<label class="wpsc_ct_field_label" for="<?php echo $this->slug;?>">
						<?php echo $this->label['custom_fields_'.$field->term_id];?> <?php echo $this->required? '<span style="color:red;">*</span>':'';?>
						</label>
						<?php if($this->extra_info['custom_fields_extra_info_'.$field->term_id]){?><p class="help-block" style="<?php echo $extra_info_css?>"><?php echo $this->extra_info['custom_fields_extra_info_'.$field->term_id];?></p><?php }?>
						<input type="text" id="<?php echo $this->slug;?>" onkeypress='return false'  class="form-control wpsc_time <?php echo $this->slug?>" name="<?php echo $this->slug;?>" autocomplete="off" value="">
					</div>
					<script>
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

        
				function print_html($field){
					?>
					<div  data-fieldtype="text" data-visibility="<?php echo $this->visibility_conditions?>" class="<?php echo $this->col_class?> <?php echo $this->visibility? 'hidden':'visible'?> <?php echo $this->required? 'wpsc_required':''?> form-group wpsc_form_field <?php echo 'field_'.$field->term_id?>">
            			<?php echo html_entity_decode($this->html_content);					
						?>
					</div>
				  <?php		
				}

			}
    
endif;