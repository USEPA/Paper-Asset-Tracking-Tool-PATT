<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$statuses = get_terms([
  'taxonomy'   => 'wpsc_statuses',
  'hide_empty' => false,
  'orderby'    => 'meta_value_num',
  'order'    	 => 'ASC',
  'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
]);

$orderby_fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'meta_query' => array(
		array(
      'key'       => 'wpsc_allow_orderby',
      'value'     => '1',
      'compare'   => '='
    )
	),
]);

?>
<form id="wpsc_frm_general_settings" method="post" action="javascript:set_ticket_list_additional_settings();">
    
    <h4><?php _e('Agent View','supportcandy');?></h4>
    <div class="wpsc_padding_space"></div>
		
		<div class="form-group">
      <label for="wpsc_support_page_id"><?php _e('Default order by','supportcandy');?></label>
      <p class="help-block"><?php _e('Tickets are loaded by selected order for agent view by default.','supportcandy');?></p>
			<div class="row">
				<div class="col-sm-4" style="">
					<select class="form-control" name="wpsc_tl_agent_orderby">
		        <?php
						$agent_orderby = get_option('wpsc_tl_agent_orderby');
						foreach ($orderby_fields as $field) :
							$selected = $agent_orderby == $field->slug ? 'selected="selected"' : '';
							$label = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
							?>
							<option <?php echo $selected?> value="<?php echo $field->slug?>"><?php echo $label?></option>
							<?php 
					  endforeach;
						?>
		      </select>
				</div>
				<div class="col-sm-4" style="">
					<select class="form-control" name="wpsc_tl_agent_orderby_order">
		        <?php $agent_orderby_order = get_option('wpsc_tl_agent_orderby_order');?>
						<option <?php echo $agent_orderby_order == 'ASC'? 'selected="selected"':''?> value="ASC">ASC</option>
						<option <?php echo $agent_orderby_order == 'DESC'? 'selected="selected"':''?> value="DESC">DESC</option>
		      </select>
				</div>
      </div>
    </div>
	
		<div class="form-group">
      <label for="wpsc_support_page_id"><?php _e('Number of tickets','supportcandy');?></label>
      <p class="help-block"><?php _e('Number of Tickets shown per page for agent view.','supportcandy');?></p>
			<div class="row">
				<div class="col-sm-4" style="">
					<select class="form-control" name="agent_no_of_tickets">
		        <?php $agent_no_of_tickets = get_option('wpsc_tl_agent_no_of_tickets');?>
						<option <?php echo $agent_no_of_tickets == '10'? 'selected="selected"':''?> value="10"><?php _e('10','supportcandy')?></option>
						<option <?php echo $agent_no_of_tickets == '20'? 'selected="selected"':''?> value="20"><?php _e('20','supportcandy')?></option>
						<option <?php echo $agent_no_of_tickets == '30'? 'selected="selected"':''?> value="30"><?php _e('30','supportcandy')?></option>
						<option <?php echo $agent_no_of_tickets == '40'? 'selected="selected"':''?> value="40"><?php _e('40','supportcandy')?></option>
						<option <?php echo $agent_no_of_tickets == '50'? 'selected="selected"':''?> value="50"><?php _e('50','supportcandy')?></option>
						<option <?php echo $agent_no_of_tickets == '100'? 'selected="selected"':''?> value="100"><?php _e('100','supportcandy')?></option>
						<option <?php echo $agent_no_of_tickets == '500'? 'selected="selected"':''?> value="500"><?php _e('500','supportcandy')?></option>
		      </select>
				</div>
      </div>
    </div>
		
		<div class="from-group" id="wpsc_agent_unresolved_statuses">
			  <label for="wpsc_support_page_id"><?php _e('Unresolved Statuses','supportcandy');?></label>
				<p class="help-block"><?php _e('Select statuses for unresolved filter in ticket list.','supportcandy');?></p>
			   <div class="row">
					 <?php
					 $wpsc_tl_agent_unresolve_statuses = get_option('wpsc_tl_agent_unresolve_statuses');
					 $wpsc_tl_agent_unresolve_statuses = $wpsc_tl_agent_unresolve_statuses ? $wpsc_tl_agent_unresolve_statuses : array();
					 foreach ( $statuses as $status ) :
		          $checked = in_array($status->term_id,$wpsc_tl_agent_unresolve_statuses) ? 'checked="checked"' : '';
					    ?>
			        <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
			          <div style="width:25px;"><input type="checkbox" name="wpsc_tl_agent_unresolve_statuses[]" <?php echo $checked?> value="<?php echo $status->term_id?>" /></div>
			          <div style="padding-top:3px;"><?php echo $status->name?></div>
			        </div>
			        <?php
	         endforeach;
					 ?>
			  </div>
		</div>
		
		<?php do_action('wpsc_get_ticket_list_additional_settings_after_agent_view');?>
		
		<hr>
		<h4><?php _e('Customer View','supportcandy');?></h4>
    <div class="wpsc_padding_space"></div>
		
		<div class="form-group">
      <label for="wpsc_support_page_id"><?php _e('Default order by','supportcandy');?></label>
      <p class="help-block"><?php _e('Tickets are loaded by selected order for customer view by default.','supportcandy');?></p>
			<div class="row">
				<div class="col-sm-4" style="">
					<select class="form-control" name="wpsc_tl_customer_orderby">
		        <?php
						$customer_orderby = get_option('wpsc_tl_customer_orderby');
						foreach ($orderby_fields as $field) :
							$selected = $customer_orderby == $field->slug ? 'selected="selected"' : '';
							$label = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
							?>
							<option <?php echo $selected?> value="<?php echo $field->slug?>"><?php echo htmlentities($label)?></option>
							<?php 
					  endforeach;
						?>
		      </select>
				</div>
				<div class="col-sm-4" style="">
					<select class="form-control" name="customer_orderby_order">
		        <?php $customer_orderby_order = get_option('wpsc_tl_customer_orderby_order');?>
						<option <?php echo $customer_orderby_order == 'ASC'? 'selected="selected"':''?> value="ASC">ASC</option>
						<option <?php echo $customer_orderby_order == 'DESC'? 'selected="selected"':''?> value="DESC">DESC</option>
		      </select>
				</div>
      </div>
    </div>
	
		<div class="form-group">
      <label for="wpsc_support_page_id"><?php _e('Number of tickets','supportcandy');?></label>
      <p class="help-block"><?php _e('Number of Tickets shown per page for customer view.','supportcandy');?></p>
			<div class="row">
				<div class="col-sm-4" style="">
					<select class="form-control" name="customer_no_of_tickets">
		        <?php $customer_no_of_tickets = get_option('wpsc_tl_customer_no_of_tickets');?>
						<option <?php echo $customer_no_of_tickets == '10'? 'selected="selected"':''?> value="10"><?php _e('10','supportcandy')?></option>
						<option <?php echo $customer_no_of_tickets == '20'? 'selected="selected"':''?> value="20"><?php _e('20','supportcandy')?></option>
						<option <?php echo $customer_no_of_tickets == '30'? 'selected="selected"':''?> value="30"><?php _e('30','supportcandy')?></option>
						<option <?php echo $customer_no_of_tickets == '40'? 'selected="selected"':''?> value="40"><?php _e('40','supportcandy')?></option>
						<option <?php echo $customer_no_of_tickets == '50'? 'selected="selected"':''?> value="50"><?php _e('50','supportcandy')?></option>
						<option <?php echo $customer_no_of_tickets == '100'? 'selected="selected"':''?> value="100"><?php _e('100','supportcandy')?></option>
						<option <?php echo $customer_no_of_tickets == '500'? 'selected="selected"':''?> value="500"><?php _e('500','supportcandy')?></option>
		      </select>
				</div>
      </div>
    </div>
		
		<div class="from-group" id ="wpsc_customer_unresolved_statuses">
				<label for="wpsc_support_page_id"><?php _e('Unresolved Statuses','supportcandy');?></label>
				<p class="help-block"><?php _e('Select statuses for unresolved filter in ticket list.','supportcandy');?></p>
				 <div class="row">
					 <?php
					 $wpsc_tl_customer_unresolve_statuses = get_option('wpsc_tl_customer_unresolve_statuses');
					 $wpsc_tl_customer_unresolve_statuses = $wpsc_tl_customer_unresolve_statuses ? $wpsc_tl_customer_unresolve_statuses : array();
					 foreach ( $statuses as $status ) :
							$checked = in_array($status->term_id,$wpsc_tl_customer_unresolve_statuses) ? 'checked="checked"' : '';
							?>
							<div class="col-sm-4" style="margin-bottom:10px; display:flex;">
								<div style="width:25px;"><input type="checkbox" name="wpsc_tl_customer_unresolve_statuses[]" <?php echo $checked?> value="<?php echo $status->term_id?>" /></div>
								<div style="padding-top:3px;"><?php echo $status->name?></div>
							</div>
							<?php
					 endforeach;
					 ?>
				</div>
		</div>
		
		<?php do_action('wpsc_get_ticket_list_additional_settings_after_customer_view');?>
		
		<hr>
    <button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
    <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
    <input type="hidden" name="action" value="wpsc_ticket_list" />
    <input type="hidden" name="setting_action" value="set_ticket_list_additional_settings" />
		
</form>


