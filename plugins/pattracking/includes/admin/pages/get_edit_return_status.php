<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

$status_id = isset($_POST) && isset($_POST['status_id']) ? intval($_POST['status_id']) : 0;
//if (!$status_id) {exit;}


$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 0; 
$return_term_id = isset($_POST) && isset($_POST['return_term_id']) ? intval($_POST['return_term_id']) : 0;
$color = get_term_meta( $status_id, 'wppatt_return_status_color', true);
$backgound_color = get_term_meta( $status_id, 'wppatt_return_status_background_color', true);

if( $type == 'edit-return-reason' ) {
	
	if( !taxonomy_exists('wppatt_return_reason') ) {
		$args = array(
			'public' => false,
			'rewrite' => false
		);
		register_taxonomy( 'wppatt_return_reason', 'wpsc_ticket', $args );
	}
	
/*
	$reasons = get_terms([
		'taxonomy'   => 'wppatt_return_reason',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'order'    	 => 'ASC',
		'meta_query' => array('order_clause' => array('key' => 'wppatt_return_reason_load_order')),
	]); 
*/
	
	
	$return_name_obj = get_term( $return_term_id, 'wppatt_return_reason' );
	$return_name = $return_name_obj->name;
	$return_description = get_term_meta( $return_term_id, 'wppatt_return_reason_description', true);
}

ob_start();
//echo 'Debug:<br>';
/*
echo 'status_id: '.$status_id.'<br>';
print_r($status);
echo '<br>status: '.$status.'<br>';
echo 'color: '.$color.'<br>';
echo 'background: '.$backgound_color.'<br>';
*/
//echo 'type: '.$type.'<br>';
//echo 'return name: '.$return_name.'<br>';



if( $type == 'edit-return-status-color' ) {
	
	
?>


	<div class="form-group">
		<label for="wpsc_status_color"><?php _e('Color','supportcandy');?></label>
		<p class="help-block"><?php _e('Text color of status.','supportcandy');?></p>
		<input id="wpsc_status_color" class="wpsc_color_picker" name="wpsc_status_color" value="<?php echo $color?>" />
	</div>
	<div class="form-group">
		<label for="wpsc_status_bg_color"><?php _e('Background Color','supportcandy');?></label>
		<p class="help-block"><?php _e('Background color of status.','supportcandy');?></p>
		<input id="wpsc_status_bg_color" class="wpsc_color_picker" name="wpsc_status_bg_color" value="<?php echo $backgound_color?>" />
	</div>
	<script>
		jQuery(document).ready(function(){
			jQuery('.wpsc_color_picker').wpColorPicker();
		});
	</script>
<?php 	
} // End if - $type == 'edit-return-status-color'

if ( $type == 'add-return-reason' || $type == 'edit-return-reason' ) {
	
?>
	<div class="form-group">
		<label for="wpsc_return_reason_name">Name</label>
		<p class="help-block"><?php _e('Name for the Decline Reason.','supportcandy');?></p>
		<input id="wpsc_return_reason_name" class="return_name" name="wpsc_return_reason_name" value="" />
	</div>
	<div class="form-group">
		<label for="wpsc_return_reason_description">Details</label>
		<p class="help-block"><?php _e('Text that describes the Decline Reason','supportcandy');?></p>
		<textarea  id="wpsc_return_reason_description" class="return_description" name="wpsc_return_reason_description" value="" />
	</div>
	
	
	<style>
		.return_name {
			width: 50%;
		}	
		
		.return_description {
			width: 100%;
		}
		
		.delete_btn {
			background-color: #FF5733 !important;
			color: #FFFFFF !important;
		}
	</style>
	
<?php	
} // End if 'add-return-reason' || 'edit-return-reason'

if ( $type == 'edit-return-reason' ) {
?>	
	<script>
		let return_name = '<?php echo $return_name ?>'; 
		let return_description = '<?php echo $return_description?>';
		//let return_obj = <?php echo json_encode($return_name_obj) ?>;

					
		jQuery('#wpsc_return_reason_name').val( return_name );
		jQuery('#wpsc_return_reason_description').val( return_description );	
		
		jQuery(document).ready(function(){
			jQuery('textarea').each(function () {
				this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
			}).on('input', function () {
				this.style.height = 'auto';
				this.style.height = (this.scrollHeight) + 'px';
			});
		});	
		
	</script>
<?php	
}

$body = ob_get_clean();
ob_start();

if( $type == 'edit-return-status-color' ) {

	?>
	<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
	<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_edit_status(<?php echo htmlentities($status_id)?>);"><?php _e('Submit','supportcandy');?></button>
	<?php 
	
} // End if 'edit-return-status-color'

if ( $type == 'add-return-reason' ) {
	
	?>
	<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
	<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_add_reason();"><?php _e('Submit','supportcandy');?></button>
	<?php 
	
} // End if 'add-return-reason'

if ( $type == 'edit-return-reason' ) {
	
	?>
	<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
	<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_edit_reason(<?php echo $return_term_id ?>);"><?php _e('Submit','supportcandy');?></button>
	<button type="button" class="btn wpsc_popup_action delete_btn" onclick="wpsc_delete_edit_reason(<?php echo $return_term_id ?>);" ><?php _e('Delete','supportcandy');?></button>	
	<?php 
	
} // End if 'edit-return-reason'
	
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
