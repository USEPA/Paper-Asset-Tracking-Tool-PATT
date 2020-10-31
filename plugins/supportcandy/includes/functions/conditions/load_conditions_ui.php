<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$condition_options = array();

$custom_fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value',
	'meta_key'	 => 'wpsc_tf_label',
	'order'    	 => 'ASC',
]);

foreach ( $custom_fields as $field ) {
		
		$label      = get_term_meta( $field->term_id, 'wpsc_tf_label', true );
		$field_type = get_term_meta( $field->term_id, 'wpsc_tf_type', true );
		
		if( $field_type == 0 ){
		
				switch ($field->slug) {
					
					case 'ticket_category':
					case 'ticket_priority':
					case 'ticket_status':
					case 'assigned_agent':
						
						$condition_options[] = array(
							'key'         => $field->term_id,
							'label'       => $label,
							'has_options' => 1,
						);
						break;
						
					case 'customer_name':
					case 'customer_email':
					case 'ticket_subject':
					case 'ticket_description':
						
						$condition_options[] = array(
							'key'         => $field->term_id,
							'label'       => $label,
							'has_options' => 0,
						);
						break;
						
				}
		
		} else {
				
				switch($field_type){
					
						case 1 :
						case 5 :
						case 7 :
						case 8 :
						case 9 :
						
							$condition_options[] = array(
								'key'         => $field->term_id,
								'label'       => $label,
								'has_options' => 0,
							);
							break;
						
						case 2:
						case 3:
						case 4:
							
							$condition_options[] = array(
								'key'         => $field->term_id,
								'label'       => $label,
								'has_options' => 1,
							);
							break;
							
						default:
							$condition_options = apply_filters( 'wpsc_cond_options_custom_fields', $condition_options, $field_type, $field );
					
				}
				
		}
		
}

$condition_options[] = array(
	'key'         => 'agent_created',
	'label'       => __('Ticket Submitted By','supportcandy'),
	'has_options' => 1,
);

$condition_options[] = array(
	'key'         => 'user_type',
	'label'       => __('User Type','supportcandy'),
	'has_options' => 1,
);

$condition_options = apply_filters( 'wpsc_condition_options', $condition_options );

$conditions = $conditions ? json_decode($conditions) : array();
?>

<div id="<?php echo $id?>" class="row" style="background-color:#CCD1D1;padding-top:30px;padding-bottom:30px;">
  
    <div class="col-sm-12 wpsc_conditions_container">
        
        <?php foreach ($conditions as $condition): 
						$current_key             = '';
						$current_condition_index = '';
						
						?>
	        	
						<div class="row wpsc_condition_element" style="margin-bottom:20px;">
							
								<div class="col-sm-4">
									
									<select class="wpsc_condition_field form-control" onchange="wpsc_condition_change(this)">
										<option value=""><?php _e('Select Condition', 'supportcandy')?></option>
										<?php foreach ( $condition_options as $index => $option ) : 
											
												$selected = '';
												if($option['key'] == $condition->field){
													$selected                = 'selected="selected"';
													$current_key             = $condition->field;
													$current_condition_index = $index;
												}
												
												?>
												
												<option <?php echo $selected?> data-hasoptions="<?php echo $option['has_options']?>" value="<?php echo $option['key']?>"><?php echo $option['label']?></option>
											
										<?php endforeach;?>
										
									</select>
									
								</div>
								
								<div class="col-sm-3 wpsc_condition_compare_container">
									
										<?php 
										if($condition_options[$current_condition_index]['has_options']){
											
												?>
												<select class="wpsc_condition_compare form-control">
													<option <?php echo $condition->compare == 'match' ? 'selected="selected"' : ''?> value="match"><?php _e('Matches', 'supportcandy')?></option>
													<option  <?php echo $condition->compare == 'not_match' ? 'selected="selected"' : ''?>value="not_match"><?php _e('Not Matches', 'supportcandy')?></option>
                                                </select>
												<?php
											
										} else {
											
												?>
												<select class="wpsc_condition_compare form-control">
													<option <?php echo $condition->compare == 'contain' ? 'selected="selected"' : ''?> value="contain"><?php _e('Contains', 'supportcandy')?></option>
													<option <?php echo $condition->compare == 'match' ? 'selected="selected"' : ''?> value="match"><?php _e('Matches', 'supportcandy')?></option>
													<option <?php echo $condition->compare == 'not_match' ? 'selected="selected"' : ''?> value="not_match"><?php _e('Not Matches', 'supportcandy')?></option>

												</select>
												<?php
											
										}
										?>
										
								</div>
								
								<div class="col-sm-4 wpsc_condition_value_container">
									
										<?php 
										if($condition_options[$current_condition_index]['has_options']){
											
												$options = $this->get_condition_options($condition->field);
												
												?>
												<select class="wpsc_condition_value form-control">
													<?php
													foreach ( $options as $option ) :
													  
														$selected = $option['value'] == $condition->cond_val ? 'selected="selected"' : '';
														?>
													  <option <?php echo $selected?> value="<?php echo str_replace('"', "&quot;", stripcslashes($option['value']))?>"><?php echo $option['label']?></option>
													  <?php 
														
													endforeach;
													?>
												</select>
												<?php
											
										} else {
											
												?>
												<input type="text" class="wpsc_condition_value form-control" placeholder="<?php _e('Search...','supportcandy')?>" value="<?php echo str_replace('"', "&quot;", stripcslashes($condition->cond_val))?>">
												<?php
											
										}
										?>
									
								</div>
								
								<div class="col-sm-1">
									<button type="button" class="btn btn-danger btn-sm" onclick="wpsc_remove_condition(this)"><i class="fas fa-times"></i></button>
								</div>
							
						</div>
					
        <?php endforeach;?>
        
    </div>
    
    <div class="col-sm-12" style="padding-left:30px;">
      <button type="button" class="btn btn-default btn-sm" onclick="wpsc_add_new_condition('<?php echo $id?>')"><?php _e('Add New','supportcandy')?></button>  
    </div>
  
</div>

<div style="display:none;" class="wpsc_condition_template">
	<div class="row wpsc_condition_element" style="margin-bottom:20px;">
		<div class="col-sm-4">
			<select class="wpsc_condition_field form-control" onchange="wpsc_condition_change(this)">
				<option value=""><?php _e('Select Condition', 'supportcandy')?></option>
				<?php foreach ( $condition_options as $option ) :?>
					<option data-hasoptions="<?php echo $option['has_options']?>" value="<?php echo $option['key']?>"><?php echo $option['label']?></option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="col-sm-3 wpsc_condition_compare_container"></div>
		<div class="col-sm-4 wpsc_condition_value_container"></div>
		<div class="col-sm-1">
			<button type="button" class="btn btn-danger btn-sm" onclick="wpsc_remove_condition(this)"><i class="fas fa-times"></i></button>
		</div>
	</div>
</div>

<div style="display:none;" class="wpsc_cond_compare_dd_template">
	<select class="wpsc_condition_compare form-control">
		<option value="match"><?php _e('Matches', 'supportcandy')?></option>
		<option value="not_match"><?php _e('Not Matches', 'supportcandy')?></option>
    </select>
</div>

<div style="display:none;" class="wpsc_cond_compare_tf_template">
	<select class="wpsc_condition_compare form-control">
		<option value="contain"><?php _e('Contains', 'supportcandy')?></option>
		<option value="match"><?php _e('Matches', 'supportcandy')?></option>
		<option value="not_match"><?php _e('Not Matches', 'supportcandy')?></option>
	</select>
</div>

<div style="display:none;" class="wpsc_cond_val_dd_template">
	<select class="wpsc_condition_value form-control" name=""></select>
</div>

<div style="display:none;" class="wpsc_cond_val_tf_template">
	<input type="text" class="wpsc_condition_value form-control" placeholder="<?php _e('Search...','supportcandy')?>" value="">
</div>
