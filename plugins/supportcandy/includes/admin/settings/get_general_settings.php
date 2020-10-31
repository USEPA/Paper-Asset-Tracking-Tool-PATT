<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

?>
<form id="wpsc_frm_general_settings" method="post" action="javascript:wpsc_set_general_settings();">
    
    <div class="form-group">
      <label for="wpsc_support_page_id"><?php _e('Support Page','supportcandy');?></label>
      <p class="help-block"><?php _e('Select page in which shortcode is inserted. Create a page with shortcode [supportcandy] if not created yet. Fullwidth page template is recommended.','supportcandy');?></p>
      <select class="form-control" name="wpsc_support_page_id" id="wpsc_support_page_id">
        <option value=""></option>
        <?php
        $args = array(
          'sort_order'  => 'asc',
          'sort_column' => 'post_title',
          'post_type'   => 'page',
          'post_status' => 'publish'
        );
				$wpsc_support_page_id = get_option('wpsc_support_page_id');
        $pages = get_pages( $args );
        foreach ( $pages as $page ) :
          $selected = $wpsc_support_page_id == $page->ID ? 'selected="selected"' : '';
          echo '<option '.$selected.' value="'.$page->ID.'">'.$page->post_title.'</option>';
        endforeach;
        ?>
      </select>
			<?php
			$support_page_url = get_permalink($wpsc_support_page_id);
			?>
			<i><small><a href="<?php echo htmlentities($support_page_url) ?>" target="_blank"><?php _e('Click here to go to support page','supportcandy')?></a></small></i>
    </div>
		
		<div class="form-group">
      <label for="wpsc_default_ticket_status"><?php _e('Default ticket status','supportcandy');?></label>
      <p class="help-block"><?php _e('This status will get applied for newly created ticket.','supportcandy');?></p>
      <select class="form-control" name="wpsc_default_ticket_status" id="wpsc_default_ticket_status">
        <?php
				$statuses = get_terms([
				  'taxonomy'   => 'wpsc_statuses',
				  'hide_empty' => false,
					'orderby'    => 'meta_value_num',
				  'order'    	 => 'ASC',
					'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
				]);
				$wpsc_default_ticket_status = get_option('wpsc_default_ticket_status');
        foreach ( $statuses as $status ) :
          $selected = $wpsc_default_ticket_status == $status->term_id ? 'selected="selected"' : '';
          echo '<option '.$selected.' value="'.$status->term_id.'">'.$status->name.'</option>';
        endforeach;
        ?>
      </select>
    </div>
		
		<div class="form-group">
      <label for="wpsc_default_ticket_category"><?php _e('Default ticket category','supportcandy');?></label>
      <p class="help-block"><?php _e('This category will get applied for newly created ticket.','supportcandy');?></p>
      <select class="form-control" name="wpsc_default_ticket_category" id="wpsc_default_ticket_category">
        <?php
				$categories = get_terms([
				  'taxonomy'   => 'wpsc_categories',
				  'hide_empty' => false,
					'orderby'    => 'meta_value_num',
				  'order'    	 => 'ASC',
					'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
				]);
				$wpsc_default_ticket_category = get_option('wpsc_default_ticket_category');
        foreach ( $categories as $category ) :
          $selected = $wpsc_default_ticket_category == $category->term_id ? 'selected="selected"' : '';
          echo '<option '.$selected.' value="'.$category->term_id.'">'.$category->name.'</option>';
        endforeach;
        ?>
      </select>
    </div>
		
		<div class="form-group">
      <label for="wpsc_default_ticket_priority"><?php _e('Default ticket priority','supportcandy');?></label>
      <p class="help-block"><?php _e('This priority will get applied for newly created ticket.','supportcandy');?></p>
      <select class="form-control" name="wpsc_default_ticket_priority" id="wpsc_default_ticket_priority">
        <?php
				$priorities = get_terms([
				  'taxonomy'   => 'wpsc_priorities',
				  'hide_empty' => false,
					'orderby'    => 'meta_value_num',
				  'order'    	 => 'ASC',
					'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
				]);
				$wpsc_default_ticket_priority = get_option('wpsc_default_ticket_priority');
        foreach ( $priorities as $priority ) :
          $selected = $wpsc_default_ticket_priority == $priority->term_id ? 'selected="selected"' : '';
          echo '<option '.$selected.' value="'.$priority->term_id.'">'.$priority->name.'</option>';
        endforeach;
        ?>
      </select>
    </div>
		
		<div class="form-group">
      <label for="wpsc_ticket_status_after_customer_reply"><?php _e('Ticket status after customer reply','supportcandy');?></label>
      <p class="help-block"><?php _e("This status will be applied to the ticket if customer post reply in ticket. 'Default' will not change status of the ticket in this case.","supportcandy");?></p>
      <select class="form-control" name="wpsc_ticket_status_after_customer_reply" id="wpsc_ticket_status_after_customer_reply">
        <option value=""><?php _e('Default','supportcandy');?></option>
				<?php
				$wpsc_ticket_status_after_customer_reply = get_option('wpsc_ticket_status_after_customer_reply');
        foreach ( $statuses as $status ) :
          $selected = $wpsc_ticket_status_after_customer_reply == $status->term_id ? 'selected="selected"' : '';
          echo '<option '.$selected.' value="'.$status->term_id.'">'.$status->name.'</option>';
        endforeach;
        ?>
      </select>
    </div>
		
		<div class="form-group">
      <label for="wpsc_ticket_status_after_agent_reply"><?php _e('Ticket status after agent reply','supportcandy');?></label>
      <p class="help-block"><?php _e("This status will be applied to the ticket if agent or any support staff post reply in ticket. 'Default' will not change status of the ticket in this case.","supportcandy");?></p>
      <select class="form-control" name="wpsc_ticket_status_after_agent_reply" id="wpsc_ticket_status_after_agent_reply">
        <option value=""><?php _e('Default','supportcandy');?></option>
				<?php
				$wpsc_ticket_status_after_agent_reply = get_option('wpsc_ticket_status_after_agent_reply');
        foreach ( $statuses as $status ) :
          $selected = $wpsc_ticket_status_after_agent_reply == $status->term_id ? 'selected="selected"' : '';
          echo '<option '.$selected.' value="'.$status->term_id.'">'.$status->name.'</option>';
        endforeach;
        ?>
      </select>
    </div>
		
		<div class="form-group">
      <label for="wpsc_close_ticket_status"><?php _e('Close ticket status','supportcandy');?></label>
      <p class="help-block"><?php _e("Status to apply if 'Close Ticket' button clicked for a ticket.","supportcandy");?></p>
      <select class="form-control" name="wpsc_close_ticket_status" id="wpsc_close_ticket_status">
        <?php
				$wpsc_close_ticket_status = get_option('wpsc_close_ticket_status');
        foreach ( $statuses as $status ) :
          $selected = $wpsc_close_ticket_status == $status->term_id ? 'selected="selected"' : '';
          echo '<option '.$selected.' value="'.$status->term_id.'">'.$status->name.'</option>';
        endforeach;
        ?>
      </select>
    </div>
		
		<div class="form-group">
      <label for="wpsc_allow_customer_close_ticket"><?php _e('Allow customer to close ticket','supportcandy');?></label>
      <p class="help-block"><?php _e("Enables 'Close Ticket' button for customer inside open ticket screen.","supportcandy");?></p>
      <select class="form-control" name="wpsc_allow_customer_close_ticket" id="wpsc_allow_customer_close_ticket">
        <?php
				$wpsc_allow_customer_close_ticket = get_option('wpsc_allow_customer_close_ticket');
				$selected = $wpsc_allow_customer_close_ticket == '1' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="1">'.__('Yes','supportcandy').'</option>';
				$selected = $wpsc_allow_customer_close_ticket == '0' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="0">'.__('No','supportcandy').'</option>';
				?>
      </select>
    </div>
		
		<div class="form-group">
      <label for="wpsc_reply_form_position"><?php _e('Reply form position','supportcandy');?></label>
      <p class="help-block"><?php _e("Reply form position in open ticket page. 'Top' will load reply form above ticket threads and ticket threads will load in decending order. Whereas, 'Bottom' will load reply form below ticket thread and threads loaded in acending order.","supportcandy");?></p>
      <select class="form-control" name="wpsc_reply_form_position" id="wpsc_reply_form_position">
        <?php
				$wpsc_reply_form_position = get_option('wpsc_reply_form_position');
				$selected = $wpsc_reply_form_position == '1' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="1">'.__('Top','supportcandy').'</option>';
				$selected = $wpsc_reply_form_position == '0' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="0">'.__('Bottom','supportcandy').'</option>';
				?>
      </select>
    </div>
		
		<div class="form-group">
      <label for="wpsc_calender_date_format"><?php _e('Calender date format','supportcandy');?></label>
      <p class="help-block"><?php _e("This format will be applicable for input date fields (datepicker) for SupportCandy.","supportcandy");?></p>
      <select class="form-control" name="wpsc_calender_date_format" id="wpsc_calender_date_format">
        <?php
				$wpsc_calender_date_format = get_option('wpsc_calender_date_format');
				$selected = $wpsc_calender_date_format == 'dd-mm-yy' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="dd-mm-yy">dd-mm-yy</option>';
				$selected = $wpsc_calender_date_format == 'mm-dd-yy' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="mm-dd-yy">mm-dd-yy</option>';
				$selected = $wpsc_calender_date_format == 'yy-mm-dd' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="yy-mm-dd">yy-mm-dd</option>';
				$selected = $wpsc_calender_date_format == 'yy-dd-mm' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="yy-dd-mm">yy-dd-mm</option>';
				$selected = $wpsc_calender_date_format == 'mm-yy-dd' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="mm-yy-dd">mm-yy-dd</option>';
				$selected = $wpsc_calender_date_format == 'dd-yy-mm' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="dd-yy-mm">dd-yy-mm</option>';
				?>
      </select>
    </div>
		
		<div class="form-group">
			<label for="wpsc_ticket_alice"><?php _e('Ticket Alice in Email Notification Subject','supportcandy' );?></label>
			<p class="help-block"><?php _e("Label to represent ticket in email notification subject etc e.g. You may want to use this as a bug tracking then you can label it like issue #123, bug #132 etc. So that it should be  show in issue #123 or Bug #123. Please note this will only work in email subject. If you want to change all places, you will need to translate .pot","supportcandy");?></p>
			<input type="text" class="form-control" name="wpsc_ticket_alice" id="wpsc_ticket_alice" value="<?php echo get_option('wpsc_ticket_alice');?>" />
		</div>
		
		
		
		<div class="form-group">
      <label for="wpsc_allow_guest_ticket"><?php _e('Allow guest ticket','supportcandy');?></label>
      <p class="help-block"><?php _e("Enables guest ticket facility. Guest will able to create ticket without needing them to have an user account on website. They can create ticket using their name and email address.","supportcandy");?></p>
      <select class="form-control" name="wpsc_allow_guest_ticket" id="wpsc_allow_guest_ticket">
        <?php
				$wpsc_allow_guest_ticket = get_option('wpsc_allow_guest_ticket');
				$selected = $wpsc_allow_guest_ticket == '1' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="1">'.__('Yes','supportcandy').'</option>';
				$selected = $wpsc_allow_guest_ticket == '0' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="0">'.__('No','supportcandy').'</option>';
				?>
      </select>
    </div>
		
		<div class="form-group">
			<?php 
			$reply_to_close_ticket = get_option('wpsc_allow_reply_to_close_ticket');
			?>
			<label for="wpsc_allow_attach"><?php _e('Allow Reply to Closed Tickets','supportcandy');?></label>
			<p class="help-block"><?php _e("Only selected roles can reply to closed tickets.","supportcandy");?></p>
			<div class="row">
				<div class="col-sm-4" style="margin-bottom:10px; display:flex;">
					<?php $checked = in_array('customer', $reply_to_close_ticket) ? 'checked="checked"' : '';?>
					<div style="width:25px;"><input <?php echo $checked ?> type="checkbox" name="wpsc_allow_rtct[]" value="customer" /></div>
					<div style="padding-top:3px;"><?php _e('Customers','supportcandy') ?></div>
				</div>
				<div class="col-sm-4" style="margin-bottom:10px; display:flex;">
					<?php $checked = in_array('agents', $reply_to_close_ticket) ? 'checked="checked"' : '';?>
					<div style="width:25px;"><input <?php echo $checked ?> type="checkbox" name="wpsc_allow_rtct[]" value="agents" /></div>
					<div style="padding-top:3px;"><?php _e('Agents','supportcandy') ?></div>
				</div>
			</div>
		</div>
		
		<div class="form-group">
			 <label for="wpsc_default_login_setting"><?php _e('Default Login','supportcandy');?></label><br />
			 <p class="help-block"><?php _e(" Default Login on support page.","supportcandy");?></p>
			  <select class="form-control" name="wpsc_default_login_setting" id="wpsc_default_login_setting">
				<?php
				$wpsc_enable_default_login = get_option('wpsc_default_login_setting');				
	 			
				$selected = $wpsc_enable_default_login == '1' ? 'selected="selected"' : '';
	 			echo '<option '.$selected.' value="1">Support Candy Login form</option>';
	 			
				$selected = $wpsc_enable_default_login == '2' ? 'selected="selected"' : '';
	 			echo '<option '.$selected.' value="2">WordPress Login Link</option>';
	 			
				$selected = $wpsc_enable_default_login == '3' ? 'selected="selected"' : '';
	 			echo '<option '.$selected.' value="3">Custom Login URL</option>';			 				
				?>
       </select>
			 
			 <div id="custom_login" style="display:none;">
				 <label style="margin-top:10px"><?php _e('Custom Url','supportcandy');?></label>
				 <input type="text" class="form-control" style="width:500px; margin-top:8px;" name="wpsc_custom_login_url" value="<?php echo get_option('wpsc_custom_login_url'); ?>"><br>
			 </div>
				 					 
		</div>
		<div class="form-group">
			<label for="wpsc_user_registration"><?php _e('User Registration','supportcandy');?></label>
			<p class="help-block"></p>
			<select class="form-control" name="wpsc_user_registration" id="wpsc_user_registration">
				<?php
				$wpsc_user_registration = get_option('wpsc_user_registration');
				$selected = $wpsc_user_registration == '1' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
				$selected = $wpsc_user_registration == '0' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
				?>
			</select>
		</div>
		<div class="form-group">
				<label for="wpsc_user_registration_method"><?php _e('User Registration Method','supportcandy');?></label>
				<p class="help-block"></p>
				<select class="form-control" name="wpsc_user_registration_method" id="wpsc_user_registration_method">
						<?php
						$wpsc_user_registration_method=get_option('wpsc_user_registration_method');
						
						$selected = $wpsc_user_registration_method == '1' ? 'selected="selected"' : '';
						echo '<option '.$selected.' value="1">Support Candy Registration </option>';
						
						$selected = $wpsc_user_registration_method == '2' ? 'selected="selected"' : '';
						echo '<option '.$selected.' value="2">WordPress Default Registration</option>';
						
						$selected = $wpsc_user_registration_method == '3' ? 'selected="selected"' : '';
						echo '<option '.$selected.' value="3">Custom Registration URL</option>';		
						
						 ?>
						
				</select>
				<div id="custom_registration" style="display:none;">
					<label style="margin-top:10px"><?php _e('Custom Url','supportcandy');?></label>
					<input type="text" class="form-control" style="width:500px; margin-top:8px;" name="wpsc_custom_registration_url" value="<?php echo get_option('wpsc_custom_registration_url'); ?>"><br>
				</div>
				
		</div>

		<?php do_action('wpsc_get_gerneral_settings');?>
		
    <button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
    <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
    <input type="hidden" name="action" value="wpsc_settings" />
    <input type="hidden" name="setting_action" value="set_general_settings" />
    
</form>

<script>
 jQuery(document).ready(function() {	   
	 
	 <?php
	 if($wpsc_enable_default_login == 3){
		 ?>
		 jQuery('#custom_login').show();
		 <?php
	 }
	 if($wpsc_user_registration_method==3)
	 {
		 ?>
		 jQuery('#custom_registration').show();
		 <?php
	 }
	 ?>
	 
	 jQuery('#wpsc_user_registration_method').change(function() {
		 if(this.value=='3'){			 
			  jQuery('#custom_registration').show();
		 }else {
		 		jQuery('#custom_registration').hide(); 
		 }
	 });
	 jQuery('#wpsc_default_login_setting').change(function() {
		 if(this.value=='3'){			 
			  jQuery('#custom_login').show();
		 }else {
		 		jQuery('#custom_login').hide(); 
		 }
	 });
 });
</script>
