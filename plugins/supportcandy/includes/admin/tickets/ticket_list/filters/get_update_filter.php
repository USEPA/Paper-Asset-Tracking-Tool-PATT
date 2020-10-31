<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;
if (!$current_user->ID){
	die();
}

$filter_key    = isset($_POST) && isset($_POST['key']) ? intval($_POST['key']) : '' ;
$blog_id       = get_current_blog_id();
$saved_filters = get_user_meta($current_user->ID, $blog_id.'_wpsc_filter',true);
$filter        = $saved_filters[$filter_key];

$wpsc_appearance_ticket_list  = get_option('wpsc_appearance_ticket_list');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
ob_start();

?>
<form id="frm_edit_additional_custom_filters" method="post">
	<div class="form-group col-sm-12">
		<label><?php echo __('Filter label','supportcandy');?></label>
		<input type="text" class="form-control" id="filter_save_label" name="filter_save_label" value="<?php echo ($filter['save_label'])?>">
	</div>
	<?php
	if ($current_user->has_cap('wpsc_agent')) {
		$fields = get_terms([
			'taxonomy'   => 'wpsc_ticket_custom_fields',
			'hide_empty' => false,
			'orderby'    => 'meta_value_num',
			'meta_key'	 => 'wpsc_filter_agent_load_order',
			'order'    	 => 'ASC',
			'meta_query' => array(
				'relation' => 'AND',
			array(
				'key'       => 'wpsc_allow_ticket_filter',
				'value'     => '1',
				'compare'   => '='
			),
			array(
				'key'       => 'wpsc_agent_ticket_filter_status',
				'value'     => '1',
				'compare'   => '='
			)
			),
		]);
	}
	else {
		$fields = get_terms([
			'taxonomy'   => 'wpsc_ticket_custom_fields',
			'hide_empty' => false,
			'orderby'    => 'meta_value_num',
			'meta_key'	 => 'wpsc_filter_customer_load_order',
			'order'    	 => 'ASC',
			'meta_query' => array(
				'relation' => 'AND',
			array(
				'key'       => 'wpsc_allow_ticket_filter',
				'value'     => '1',
				'compare'   => '='
			),
			array(
				'key'       => 'wpsc_customer_ticket_filter_status',
				'value'     => '1',
				'compare'   => '='
			)
			),
		]);
	}
	?>
	<?php
	foreach ( $fields as $field ){
		$label       = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
		$filter_type = get_term_meta( $field->term_id, 'wpsc_ticket_filter_type', true);
		$wpsc_tf_type = get_term_meta( $field->term_id,'wpsc_tf_type', true);
		if ($filter_type=='string' || $filter_type=='number') {
		if($field->slug == 'ticket_id'){
			$field->slug = 'id';
		}
		?>
		<div id="tf_<?php echo $field->slug?>" class="form-group col-sm-12">
			<label><?php echo htmlentities($label)?></label>
			<input type="text" data-field="<?php echo $field->slug?>" class="form-control wpsc_edit_search_autocomplete" placeholder="<?php _e('Search...','supportcandy')?>">
			<ul class="wpsp_edit_filter_display_container">
			
				<?php if(isset($filter['custom_filter'][$field->slug])):
					if(isset($filter['custom_filter'][$field->slug]) && apply_filters('wpsc_add_ticket_meta',true,$field->slug)){
						$meta_query[] = array(
							'key'     => $field->slug,
							'value'   => $filter['custom_filter'][$field->slug],
							'compare' => 'IN'
						);
					}else {
						$meta_query = apply_filters('wpsc_get_tickets_meta', $meta_query,$field->slug,$filter['custom_filter'][$field->slug]);
					}
				?>
				<?php foreach ($filter['custom_filter'][$field->slug] as $key => $value):?>
					<li class="wpsp_filter_display_element">
						<div class="flex-container">
							<div class="wpsp_filter_display_text">
								<?php echo $wpscfunction->get_tf_value_filter_label($field->slug,$value)?>
								<input type="hidden" name="custom_filter[<?php echo $field->slug?>][]" value="<?php echo $value?>">
							</div>
							<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);">
								<i class="fa fa-times"></i>
							</div>
						</div>
					</li>
				<?php endforeach;?>
			<?php endif;?>								
			</ul>
		</div>
		<?php
		} elseif($filter_type=='datetime') {
			if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
				$meta_query[] = array(
					'key'     => $field->slug,
					'value'   => array( 
						$filter['custom_filter'][$field->slug]['from'],
						$filter['custom_filter'][$field->slug]['to'],
					),
					'compare' => 'BETWEEN',
					'type' 	  => 'DATE',
				);
			}
			?>
			<div class="row form-group">
				<label style="width:100%;padding-left:15px;"><?php echo htmlentities($label)?></label>
				<div class="col-sm-6">
					<input type="text" autocomplete="off" class="form-control wpsc_datetime" name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
				</div>
				<div class="col-sm-6">
					<input type="text" autocomplete="off" class="form-control wpsc_datetime" name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
				</div>
			</div>
			<?php
			
		}elseif($wpsc_tf_type=='21'){
			$time_format = get_term_meta($field->term_id, 'wpsc_time_format',true);
			if($time_format == '12'){
				$tr = 'hh:mm:ss tt';
			}elseif($time_format == '24'){
				$tr = 'HH:mm:ss';
			}
			if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
				if($time_format == '12'){
					$from = date("H:i:s",strtotime($filter['custom_filter'][$field->slug]['from']));
					$to   = date("H:i:s",strtotime($filter['custom_filter'][$field->slug]['to']));
				}elseif($time_format == '24'){
					$from = $filter['custom_filter'][$field->slug]['from'];
					$to = $filter['custom_filter'][$field->slug]['to'];
				}
				$meta_query[] = array(
					'key'     => $field->slug,
					'value'   => array( 
						$from,
						$to,
					),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				);
			}
			?>
			<div class="row form-group">
				<label style="width:100%;padding-left:15px;"><?php echo htmlentities($label)?></label>
				<div class="col-sm-6">
					<input type="text" autocomplete="off" class="form-control <?php echo $field->slug?>" name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
				</div>
				<div class="col-sm-6">
					<input type="text" autocomplete="off" class="form-control <?php echo $field->slug?>" name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
				</div>
			</div>
			<script>
				jQuery('.<?php echo $field->slug?>').timepicker({
					timeFormat : '<?php echo $tr ?>'
				}); 
			</script>
			<?php	
		}elseif($wpsc_tf_type == '6'){
			$dr = "";
			$date_range = get_term_meta($field->term_id, 'wpsc_date_range',true);
			if($date_range == 'future'){
				$dr = "minDate: 0";
			}elseif($date_range == 'past'){
				$dr = "maxDate: 0";
			}
			if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
				$meta_query[] = array(
					'key'     => $field->slug,
					'value'   => array( 
						get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['from'])),
						get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['to'])),
					),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				);
			}
			?>
			<div class="row form-group">
				<label style="width:100%;padding-left:15px;"><?php echo htmlentities($label)?></label>
				<div class="col-sm-6">
					<input type="text" autocomplete="off" class="form-control <?php echo $field->slug ?> " name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
				</div>
				<div class="col-sm-6">
					<input type="text" autocomplete="off" class="form-control <?php echo $field->slug ?> " name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
				</div>
			</div>
			<script>
				jQuery( ".<?php echo $field->slug?>").datepicker({
					dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
					showAnim : 'slideDown',
					changeMonth: true,
					changeYear: true,
					yearRange: "-100:+100",
					<?php echo $dr?>
				});
			</script>
		<?php
		}else {
			if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
				$meta_query[] = array(
					'key'     => $field->slug,
					'value'   => array( 
						get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['from'])),
						get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['to'])),
					),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				);
			}
			?>
			<div class="row form-group">
				<label style="width:100%;padding-left:15px;"><?php echo htmlentities($label)?></label>
				<div class="col-sm-6">
					<input type="text" autocomplete="off" class="form-control wpsc_date" name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
				</div>
				<div class="col-sm-6">
					<input type="text" autocomplete="off" class="form-control wpsc_date" name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
				</div>
			</div>
			<?php
		}
	} ?>
	<script>
		jQuery(document).ready(function(){
			jQuery( ".wpsc_date" ).datepicker({
				dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
				showAnim : 'slideDown',
				changeMonth: true,
				changeYear: true,
				yearRange: "-100:+100",
			});
	
			jQuery('.wpsc_datetime').datetimepicker({
				dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
				showAnim : 'slideDown',
				changeMonth: true,
				changeYear: true,
				timeFormat: 'HH:mm:ss'
			});

			jQuery( ".wpsc_edit_search_autocomplete" ).autocomplete({
				minLength: 0,
				appendTo: jQuery('.wpsc_edit_search_autocomplete').parent(),
				source: function( request, response ) {
					var term = request.term;
					request = {
						action: 'wpsc_tickets',
						setting_action : 'filter_autocomplete',
						term : term,
						field : jQuery(this.element).data('field'),
					}
				jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
					response(data);
				});
				},
				select: function (event, ui) {
				var html_str = '<li class="wpsp_filter_display_element">'
									+'<div class="flex-container">'
										+'<div class="wpsp_filter_display_text">'
											+ui.item.label
											+'<input type="hidden" name="custom_filter['+ui.item.slug+'][]" value="'+ui.item.flag_val+'">'
										+'</div>'
										+'<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></div>'
									+'</div>'
								+'</li>';
				jQuery('#tf_'+ui.item.slug+' .wpsp_edit_filter_display_container').append(html_str);
				jQuery(this).val(''); return false;
				}
		
			}).focus(function() {
				jQuery(this).autocomplete("search", "");
			});
		});
	</script>

	<input type="hidden" name="action" value="wpsc_tickets">
	<input type="hidden" name="setting_action" value="update_saved_filter">
	<input type="hidden" id="wpsc_filter_search" class="form-control" name="wpsc_custom_filter[s]" value="<?php echo trim($filter['custom_filter']['s'])?>" autocomplete="off" placeholder="<?php _e('Search...','supportcandy')?>">
	<input type="hidden" id="wpsc_th_orderby" name="orderby" value="<?php echo htmlentities($filter['orderby'])?>">
	<input type="hidden" id="wpsc_th_order" name="order" value="<?php echo htmlentities($filter['order'])?>">
	<input type="hidden" name="wpsc_edit_filter" id="wpsc_edit_filter" value="<?php echo $filter_key ?>">
	<input type="hidden" name="filter" value="all">
	
</form>
<?php

$body = ob_get_clean();
ob_start();

?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="submit" class="btn wpsc_popup_action" id="wpsc_update_ticket_filter_btn" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;"  onclick="wpsc_get_update_ticket_filter();"><?php _e('Save','supportcandy');?></button>
<?php
$footer = ob_get_clean();

$output = array(
	'body'   => $body,
	'footer' => $footer
);

echo json_encode($output);
