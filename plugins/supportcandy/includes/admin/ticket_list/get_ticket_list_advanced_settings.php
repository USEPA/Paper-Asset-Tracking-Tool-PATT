<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
	<form id="wpsc_frm_ticket_list_advanced_settings" method="post" action="javascript:set_ticket_list_advanced_settings();">

	<div class="from-group" id="wpsc_agent_close_statuses">
		<label for="wpsc_close_ticket_group"><?php _e('Closed Status group','supportcandy');?></label>
		<p class="help-block"><?php _e('Select statuses you refer as closed. It will be applicable only to Closed filter on ticket list.','supportcandy');?></p>
		<div class="row">
		<?php

			$statuses = get_terms([
			'taxonomy'   => 'wpsc_statuses',
			'hide_empty' => false,
			'orderby'    => 'meta_value_num',
			'order'    	 => 'ASC',
			'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
			]);
			$wpsc_close_ticket_group = get_option('wpsc_close_ticket_group');
			$wpsc_close_ticket_group = $wpsc_close_ticket_group ? $wpsc_close_ticket_group : array();
			foreach ( $statuses as $status ) :
				$checked = in_array($status->term_id,$wpsc_close_ticket_group) ? 'checked="checked"' : '';
				?>
				<div class="col-sm-4" style="margin-bottom:10px; display:flex;">
					<div style="width:25px;"><input type="checkbox" name="wpsc_close_ticket_group[]" <?php echo $checked?> value="<?php echo $status->term_id?>" /></div>
					<div style="padding-top:3px;"><?php echo $status->name?></div>
				</div>
				<?php
	        endforeach;
			?>
		</div>
	</div>
	<hr>
    <button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
    <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
    <input type="hidden" name="action" value="wpsc_ticket_list" />
    <input type="hidden" name="setting_action" value="set_ticket_list_advanced_settings" />
		
    </form>