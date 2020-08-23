<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}


$ticket_widget_id = isset($_POST) && isset($_POST['ticket_widget_id']) ? intval($_POST['ticket_widget_id']) : 0;
if (!$ticket_widget_id) {exit;}
$ticket_widget = get_term_by('id', $ticket_widget_id, 'wpsc_ticket_widget');
$ticket_widget_name = get_term_meta($ticket_widget_id, 'wpsc_label', true);
$agent_role = get_option('wpsc_agent_role');
ob_start();
?>
<form id="wpsc_frm_general_settings" method="post">
		<div class="form-group">
		  <label for="wpsc_ticket_widget_name"><?php _e('Edit Ticket Widget','supportcandy');?></label>
		  <p class="help-block"><?php _e('Title','supportcandy');?></p>
		  <input id="wpsc_ticket_widget_name" class="form-control" name="wpsc_ticket_widget_name" value="<?php echo $ticket_widget_name;?>" />
		</div>

		<div class="form-group">
			  <label for="wpsc_ticket_widget_type"><?php _e('Type','supportcandy');?></label>
				<p class="help-block"><?php _e('Show/hide ticket widget in open ticket.','supportcandy');?></p>
				<select class="form-control" name="wpsc_ticket_widget_type" id="wpsc_ticket_widget_type">
			    <?php
			   	$wpsc_ticket_widget_type = get_term_meta( $ticket_widget->term_id, 'wpsc_ticket_widget_type', true);
			    $selected = $wpsc_ticket_widget_type == '1' ? 'selected="selected"' : '';
			    echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
				  $selected = $wpsc_ticket_widget_type == '0' ? 'selected="selected"' : '';
			    echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>'; 
			   ?>
		   </select>
		</div>

		<div class="form-group" >
				<?php 	
				$wpsc_ticket_widget_role = get_term_meta( $ticket_widget->term_id, 'wpsc_ticket_widget_role', true);
				?>
				<label for="wpsc_ticket_widget_role"><?php _e('Role','supportcandy');?></label>
			  <p class="help-block"><?php _e('Selected users can see the widget.','supportcandy');?></p>
				<div class="row" >
						<?php foreach ( $agent_role as $key => $role ) {
						$checked = in_array($key,$wpsc_ticket_widget_role) ? 'checked="checked"':'';
						?>
					  <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
						 	<div style="width:25px;"><input id="wpsc_ticket_widget_role" type="checkbox" <?php echo $checked?> name="ticket_widget_role" value="<?php echo htmlentities($key) ?>" /></div>
							<div style="padding-top:3px;"><?php echo ($role['label'])?></div>
			      </div>
					<?php 
					}
					?>
				  <div class="col-sm-4" style="margin-bottom:10px; display:flex;"> 
				    <div style="width:25px;"><input id="wpsc_ticket_widget_role" type="checkbox" <?php echo in_array('customer',$wpsc_ticket_widget_role)?'checked="checked"':''?> name="ticket_widget_role" value="<?php echo 'customer'?>" /></div>
					  <div style="padding-top:3px;"><?php _e('Customer','supportcandy')?></div>
			    </div>
		   </div>
		</div>		
		<?php do_action('wpsc_get_edit_ticket_widget');?>

		<button type="submit" class="btn btn-success" onclick="javascript:wpsc_set_edit_ticket_widget(<?php echo $ticket_widget_id?>,event);"><?php _e('Save Changes','supportcandy');?></button>
		<button type="cancel" class="btn btn-default"  onclick="javascript:wpsc_get_ticket_widget_settings()"><?php _e('Cancel','supportcandy');?></button>
		<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
		<input type="hidden" name="action" value="settings.php" />
		<input type="hidden" name="setting_action" value="wpsc_set_edit_ticket_widget" />
</form>