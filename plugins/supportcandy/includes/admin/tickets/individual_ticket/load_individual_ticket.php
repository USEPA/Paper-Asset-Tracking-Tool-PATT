<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$reply_form_position = get_option('wpsc_reply_form_position');
$ticket              = $wpscfunction->get_ticket($ticket_id);
$customer_email      = $ticket['customer_email'];
$status_id           = $ticket['ticket_status'];
$priority_id         = $ticket['ticket_priority'];
$category_id         = $ticket['ticket_category'];
$assigned_agents 		 = $wpscfunction->get_ticket_meta($ticket_id,'assigned_agent');
$customer_name   		 = stripcslashes($ticket['customer_name']);
$auth_id         		 = $ticket['ticket_auth_code'];
$ticket_status       = $wpscfunction->get_ticket_status($ticket_id);
$wpsc_allow_reply_confirmation = get_option('wpsc_allow_reply_confirmation');
$wpsc_thread_date_format       = get_option('wpsc_thread_date_format');
$wpsc_redirect_to_ticket_list  = get_option('wpsc_redirect_to_ticket_list');
$wpsc_allow_rich_text_editor   = get_option('wpsc_allow_rich_text_editor');

$ticket_widgets = get_terms([
		'taxonomy'   => 'wpsc_ticket_widget',
		'hide_empty' => false,
	  'orderby'    => 'meta_value_num',
	  'order'    	 => 'ASC',
	  'meta_query' => array('order_clause' => array('key' => 'wpsc_ticket_widget_load_order')),
	]);

$role_id = get_user_option('wpsc_agent_role');

include_once WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/class-fields-formatting.php';
$fields_format = new WPSC_Ticket_Field_Formatting();

$wpsc_close_ticket_status = get_option('wpsc_close_ticket_status');

$general_appearance = get_option('wpsc_appearance_general_settings');

$create_ticket_btn_css  = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';
$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';

$wpsc_allow_rich_text_editor   = get_option('wpsc_allow_rich_text_editor');

$rich_editing = $wpscfunction->rich_editing_status($current_user);

$flag = false;
if((in_array('register_user',$wpsc_allow_rich_text_editor) && !$current_user->has_cap('wpsc_agent')) && $rich_editing){
	$flag = true;
}elseif($current_user->has_cap('wpsc_agent') && $rich_editing){
	$flag = true;
}

?>

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
	<div class="col-sm-12">
    
		<button type="button" id="wpsc_individual_new_ticket_btn" onclick="wpsc_get_create_ticket();" class="btn btn-sm wpsc_create_ticket_btn" style="<?php echo $create_ticket_btn_css?>"><i class="fa fa-plus"></i> <?php _e('New Ticket','supportcandy')?></button>
    
		<?php if ($current_user->ID):?>
			<button type="button" id="wpsc_individual_ticket_list_btn" onclick="wpsc_get_ticket_list();" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
		<?php endif;?>
		
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="wpsc_open_ticket(<?php echo $ticket_id?>);" style="<?php echo $action_default_btn_css?>"><i class="fas fa-sync-alt"></i> <?php _e('Refresh','supportcandy')?></button>
		
		<?php if ($wpscfunction->has_permission('delete_ticket',$ticket_id) && !$ticket_status):?>
			<button type="button" class="btn btn-sm wpsc_action_btn wpsc_restore_btn" id="wpsc_individual_restore_btn" onclick="get_restore_ticket(<?php echo $ticket_id?>);" style="<?php echo $action_default_btn_css?>"><i class="fa fa-window-restore"></i> <?php _e('Restore','supportcandy')?></button>
		<?php endif;?>
		<?php if ($wpscfunction->has_permission('delete_ticket',$ticket_id) && !$ticket_status ):?>
			<button type="button" class="btn btn-sm wpsc_action_btn wpsc_restore_btn" id="wpsc_delete_ticket_permanently" onclick="wpsc_delete_ticket_permanently(<?php echo $ticket_id?>);" style="<?php echo $action_default_btn_css?>"><i class="fa fa-trash"></i> <?php _e('Delete Permanently','supportcandy')?></button>
		<?php endif;?>
		<?php if ( ($customer_email == $current_user->user_email && get_option('wpsc_allow_customer_close_ticket')) || $wpscfunction->has_permission('change_status',$ticket_id) ):
			if($ticket_status && ($status_id !=$wpsc_close_ticket_status)){?>
				<button type="button" id="wpsc_individual_close_btn" onclick="wpsc_get_close_ticket(<?php echo $ticket_id?>)" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-check"></i> <?php _e('Close','supportcandy')?></button>
     <?php
			}?>
		<?php endif;?>
		
		<?php if ($wpscfunction->has_permission('delete_ticket',$ticket_id) && $ticket_status):?>
    	<button type="button" id="wpsc_individual_delete_btn" onclick="wpsc_get_delete_ticket(<?php echo $ticket_id ?>);" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-trash"></i> <?php _e('Delete','supportcandy')?></button>
		<?php endif;?>
		
		<?php do_action('wpsc_after_indidual_ticket_action_btn',$ticket_id);?>
		
  </div>
	
</div>

<div class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">

  <div class="col-sm-8 col-md-9 wpsc_it_body">

    <div class="row wpsc_it_subject_widget">
      <h4>
	 	 <?php if(apply_filters('wpsc_show_hide_ticket_subject',true)){?>
        	[Request # <?php
//PATT BEGIN
$num = $ticket_id;
$str_length = 7;
$padded_request_id = substr("000000{$num}", -$str_length);
echo $padded_request_id;
//PATT END ?>]

		  <?php } ?>		
      </h4>
    </div>
<?php /*PATT BEGIN*/ do_action('wpsc_before_request_id',$ticket_id); /*PATT END*/ ?>
<br />
<br />

		<?php
		if($reply_form_position && $ticket_status){
			include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/reply_form.php';
		}
		?>

		<div class="row wpsc_threads_container">
			<?php
			$order = $reply_form_position ? 'DESC' : 'ASC';
			$args = array(
				'post_type'      => 'wpsc_ticket_thread',
				'post_status'    => 'publish',
				'orderby'        => 'ID',
				'order'          => $order,
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => 'ticket_id',
			      'value'   => $ticket_id,
			      'compare' => '='
					)
				)
			);
			$threads = get_posts($args);
			
			if(apply_filters('wpsc_print_thread',true)){	
			foreach ($threads as $thread):
				$reply = stripslashes(htmlspecialchars_decode($thread->post_content, ENT_QUOTES));
				$reply = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $reply);

				$thread_type    = get_post_meta( $thread->ID, 'thread_type', true);
				$customer_name  = get_post_meta( $thread->ID, 'customer_name', true);
				$customer_email = get_post_meta( $thread->ID, 'customer_email', true);
				$attachments    = get_post_meta( $thread->ID, 'attachments', true);
				$ticket_id      = get_post_meta( $thread->ID,'ticket_id',true);
				$seen      			= get_post_meta( $thread->ID,'user_seen',true);
				
				if( $seen && $current_user->user_email == $ticket['customer_email'] && ($thread_type == 'report' || $thread_type == 'reply') ){
					update_post_meta($thread->ID, 'user_seen', date("Y-m-d H:i:s"));
				}

				if ($thread_type == 'report'):
					?>
					<div class="wpsc_thread" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_report_thread_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_report_thread_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_report_thread_border_color']?> !important;">
						<div class="thread_avatar">
							<?php echo get_avatar( $customer_email, 40 )?>
						</div>
						<?php 
						if($wpsc_thread_date_format == 'timestamp'){
							$date = sprintf( __('reported %1$s','supportcandy'), $wpscfunction->time_elapsed_timestamp($thread->post_date_gmt) );
						}else{
							$date = sprintf( __('reported %1$s','supportcandy'), $wpscfunction->time_elapsed_string($thread->post_date_gmt) );
						}
						?>
						<div class="thread_body">
							<div class="thread_user_name">
								<strong><?php echo $customer_name; ?></strong><small><i><?php echo $date?></i></small><br>
								<?php if ( apply_filters('wpsc_thread_email_visibility',$current_user->has_cap('wpsc_agent')) ) {?>
									<small><?php echo $customer_email?></small>
								<?php }?>
								<?php if ($wpscfunction->has_permission('edit_delete_ticket',$ticket_id) && $ticket_status):?>
									<i onclick="wpsc_get_delete_thread(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>);" class="fa fa-trash thread_action_btn wpsc_delete_thread" title="<?php _e('Delete this thread','supportcandy');?>"></i>
									<i onclick="wpsc_get_edit_thread(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>);"   class="fa fa-edit thread_action_btn wpsc_edit_thread"  title="<?php _e('Edit this thread','supportcandy');?>"></i>
								<?php endif;?>
								<?php if($current_user->has_cap('wpsc_agent')): ?>
								<!--removes button from requestor role-->
									<!--<i onclick="wpsc_get_create_thread(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>);" class="fa fa-plus-square thread_action_btn wpsc_create_ticket_thread" title="<?php _e('Create new ticket from this thread','supportcandy');?>"></i>-->
									<i onclick="wpsc_get_thread_info(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>,'thread');" class="fas fa-info-circle thread_action_btn wpsc_thread_info" title="<?php _e('Thread Info','supportcandy');?>"></i>
									
								<?php endif;?>
							</div>
							<div class="thread_messege">
								<?php 
									if ($flag) {
										echo $reply;
									} else {
										echo nl2br($reply);
									}
								?>
							</div>
							<?php 
								$wpsc_view_more = get_option('wpsc_view_more');
								if($wpsc_view_more){?>
									<div onclick="wpsc_ticket_thread_expander_toggle(this);" class="col-md-12 wpsc_ticket_thread_expander" style="padding: 0px; display: none;">
										 <?php _e('View More ...','supportcandy')?>
									</div>
								<?php	
								}?>
								<?php if($attachments):?>
									<br>
									<strong class="wpsc_attachment_title"><?php _e('Attachments','supportcandy');?>:</strong><br>
									<table class="wpsc_attachment_tbl">
										<tbody>
											<?php
											foreach( $attachments as $attachment ):
												$attach      = array();
												$attach_meta = get_term_meta($attachment);
												foreach ($attach_meta as $key => $value) {
													$attach[$key] = $value[0];
												}	
												$download_url = site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
												?>
												<tr class="wpsc_attachment_tr">
													<td>
														<a class="wpsc_attachment_link" href="<?php echo $download_url?>" target="_blank">
													  <span class="wpsc_attachment_file_name" style="padding: 7px;"><?php echo $attach['filename'];?></span></a>
													  <?php if ($current_user->has_cap('manage_options')) { ?>
															<i onclick="wpsc_thread_attachment_remove(this,<?php echo $attachment; ?>,<?php echo $thread->ID; ?>,<?php echo $ticket_id; ?>); " class="fa fa-times thread_action_btn" style="padding-top:3px;" aria-hidden="true" title="<?php _e('Delete attachment','supportcandy');?>"></i>
														<?php } ?>

													</td>
											 </tr>
											<?php	endforeach;?>
										</tbody>
									</table>
									<?php do_action('wpsc_after_report_attachment',$ticket_id,$thread->ID);?>

								<?php endif;?>
								<?php if( $current_user->has_cap('wpsc_agent')){?>
									<div>
										<?php 
										if( $seen && $seen != 'null' ){ ?>
											<i class="fas fa-check-circle wpsc_seen_info" title="<?php _e("Seen: ". $wpscfunction->time_elapsed_timestamp($seen),"supportcandy");?>"></i>
											<?php
										} ?>
									</div>
								<?php } ?>
							</div>
						</div>
						<?php
				endif;

				if ($thread_type == 'reply'):
					if($wpsc_thread_date_format == 'timestamp'){
						$date = sprintf( __('replied %1$s','supportcandy'), $wpscfunction->time_elapsed_timestamp($thread->post_date_gmt) );
					}else{
						$date = sprintf( __('replied %1$s','supportcandy'), $wpscfunction->time_elapsed_string($thread->post_date_gmt) );
					}
					$user_info=get_user_by('email',$customer_email);
					$style = '';
					if($user_info && $user_info->has_cap('wpsc_agent')){
						$style = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_reply_thread_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_reply_thread_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_reply_thread_border_color'].' !important';
					}else{
						$style = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_reply_thread_customer_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_reply_thread_customer_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_reply_thread_customer_border_color'].' !important';
					}
					?>
					<div class="wpsc_thread" style="<?php echo $style;?>">
						<div class="thread_avatar">
							<?php echo get_avatar( $customer_email, 40 )?>
						</div>
						<div class="thread_body">
							<div class="thread_user_name">
								<strong><?php echo $customer_name?></strong><small><i><?php echo $date?></i></small><br>
								<?php if ( apply_filters('wpsc_thread_email_visibility',$current_user->has_cap('wpsc_agent')) ) {?>
									<small><?php echo $customer_email?></small>
								<?php }?>
								<?php if ($wpscfunction->has_permission('edit_delete_ticket',$ticket_id) && $ticket_status):?>
									<i onclick="wpsc_get_delete_thread(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>);" class="fa fa-trash thread_action_btn wpsc_delete_thread" title="<?php _e('Delete this thread','supportcandy');?>"></i>
									<i onclick="wpsc_get_edit_thread(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>);"   class="fa fa-edit thread_action_btn wpsc_edit_thread"  title="<?php _e('Edit this thread','supportcandy');?>"></i>
								<?php endif;?>
								<?php if($current_user->has_cap('wpsc_agent')): ?>
								    <!--removes button from admin/staff role-->
									<!--<i onclick="wpsc_get_create_thread(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>);" class="fa fa-plus-square thread_action_btn wpsc_create_ticket_thread" title="<?php _e('Create new ticket from this thread','supportcandy');?>"></i>-->
									<i onclick="wpsc_get_thread_info(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>,'thread');" class="fas fa-info-circle thread_action_btn wpsc_thread_info" title="<?php _e('Thread Info','supportcandy');?>"></i>
								<?php endif;?>
							</div>
							<div class="thread_messege">
								<?php 
								if ($flag) {
									echo $reply;
								} else {
									echo nl2br($reply);
								} 
								?>
							</div>
							<?php 
								$wpsc_view_more = get_option('wpsc_view_more');
								if($wpsc_view_more){?>
									<div onclick="wpsc_ticket_thread_expander_toggle(this);" class="col-md-12 wpsc_ticket_thread_expander" style="padding: 0px; display: none;">
										 <?php _e('View More ...','supportcandy')?>
									</div>
								<?php	
								}?>
								<?php if($attachments):?>
									<strong class="wpsc_attachment_title"><?php _e('Attachments','supportcandy');?>:</strong><br>
									<table class="wpsc_attachment_tbl">
										<tbody>
											<?php
											foreach( $attachments as $attachment ):
												$attach      = array();
												$attach_meta = get_term_meta($attachment);
												foreach ($attach_meta as $key => $value) {
													$attach[$key] = $value[0];
												}
												$download_url = site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
												?>
												<tr class="wpsc_attachment_tr">
													<td>
														<a class="wpsc_attachment_link" href="<?php echo $download_url?>" target="_blank">
														<span class="wpsc_attachment_file_name" style="padding: 7px;"><?php echo $attach['filename'];?></span></a>
														<?php if ($current_user->has_cap('manage_options')) { ?>
															<i onclick="wpsc_thread_attachment_remove(this,<?php echo $attachment; ?>,<?php echo $thread->ID; ?>,<?php echo $ticket_id; ?>); " class="fa fa-times thread_action_btn" style="padding-top:3px;" aria-hidden="true" title="<?php _e('Delete attachment','supportcandy');?>"></i>
														<?php } ?>

													</td>
												</tr>
											<?php	endforeach;?>
										</tbody>
									</table>
									<?php do_action('wpsc_after_reply_attachment',$ticket_id,$thread->ID);?>
								<?php endif;?>
								<?php if( $current_user->has_cap('wpsc_agent')){?>
									<div>
										<?php 
										if( $seen && $seen != 'null' ){ ?>
											<i class="fas fa-check-circle wpsc_seen_info" title="<?php _e("Seen: " .$wpscfunction->time_elapsed_timestamp($seen),"supportcandy");?>"></i><?php 
										} ?>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php
				endif;

				if ( $thread_type == 'note' && apply_filters('wpsc_private_note_visibility',$current_user->has_cap('wpsc_agent'), $thread) &&  $wpscfunction->has_permission('view_note',$ticket_id) ):
					?>
					<div class="wpsc_thread note" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_private_note_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_private_note_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_private_note_border_color']?> !important;">
						<div class="thread_avatar">
							<?php echo get_avatar( $customer_email, 40 )?>
						</div>
						<?php 
						if($wpsc_thread_date_format == 'timestamp'){
							$date = sprintf( __('added note %1$s','supportcandy'), $wpscfunction->time_elapsed_timestamp($thread->post_date_gmt) );
						}else{
							$date = sprintf( __('added note %1$s','supportcandy'), $wpscfunction->time_elapsed_string($thread->post_date_gmt) );
						}
						?>
						<div class="thread_body">
							<div class="thread_user_name">
								<strong><?php echo $customer_name?></strong><small><i><?php echo $date?></i></small><br>
								<?php if ( apply_filters('wpsc_thread_email_visibility',$current_user->has_cap('wpsc_agent')) ) {?>
									<small><?php echo $customer_email?></small>
								<?php }?>
								<?php if ($wpscfunction->has_permission('edit_delete_ticket',$ticket_id) && $ticket_status):?>
									<i onclick="wpsc_get_delete_thread(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>);" class="fa fa-trash thread_action_btn wpsc_delete_thread"></i>
									<i onclick="wpsc_get_edit_thread(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>);"  class="fa fa-edit thread_action_btn wpsc_edit_thread"></i>
								<?php endif;?>
								<?php if($current_user->has_cap('wpsc_agent')): ?>
									<!--<i onclick="wpsc_get_create_thread(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>);" class="fa fa-plus-square thread_action_btn wpsc_create_ticket_thread" title="<?php _e('Create new ticket from this thread','supportcandy');?>"></i>-->
								<?php endif;?>
								<?php if($current_user->has_cap('wpsc_agent')):?>
									<i onclick="wpsc_get_thread_info(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>,'thread');" class="fas fa-info-circle thread_action_btn wpsc_thread_info" title="<?php _e('Thread Info','supportcandy');?>"></i>
									
						        <?php endif;?>

							</div>
							<div class="thread_messege">
							<?php 
								if ($flag) {
									echo $reply;
								} else {
									echo nl2br($reply);
								}  
								?>
							</div>
							<?php 
								$wpsc_view_more = get_option('wpsc_view_more');
								if($wpsc_view_more){?>
									<div onclick="wpsc_ticket_thread_expander_toggle(this);" class="col-md-12 wpsc_ticket_thread_expander" style="padding: 0px; display: none;">
										 <?php _e('View More ...','supportcandy')?>
									</div>
								<?php	
								}?>
								<?php if($attachments):?>
									<strong class="wpsc_attachment_title"><?php _e('Attachments','supportcandy');?>:</strong><br>
									<table class="wpsc_attachment_tbl">
										<tbody>
											<?php
											foreach( $attachments as $attachment ):
												$attach      = array();
												$attach_meta = get_term_meta($attachment);
												
												foreach ($attach_meta as $key => $value) {
													$attach[$key] = $value[0];
												}
												$download_url = site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
												
												?>
												<tr class="wpsc_attachment_tr">
													<td>
														<a class="wpsc_attachment_link" href="<?php echo $download_url?>" target="_blank">
													  <span class="wpsc_attachment_file_name" style="padding: 7px;"><?php echo $attach['filename'];?></span></a>
													  <?php if ($current_user->has_cap('manage_options')) { ?>
															<i onclick="wpsc_thread_attachment_remove(this,<?php echo $attachment; ?>,<?php echo $thread->ID; ?>,<?php echo $ticket_id; ?>); " class="fa fa-times thread_action_btn" style="padding-top:3px;" aria-hidden="true" title="<?php _e('Delete attachment','supportcandy');?>"></i>
														<?php } ?>

													</td>
												</tr>
											<?php	endforeach;?>
										</tbody>
									</table>
								<?php endif;?>
						</div>
					</div>
					<?php
				endif;
/* PATT BEGIN
				if ( $thread_type == 'log' && apply_filters('wpsc_thread_log_visibility',$current_user->has_cap('wpsc_agent')) && $wpscfunction->has_permission('view_log',$ticket_id)):
					?>
					<div class="col-md-8 col-md-offset-2 wpsc_thread_log" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_logs_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_logs_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_logs_border_color']?> !important;">
		          <?php 
							if($wpsc_thread_date_format == 'timestamp'){
								$date = sprintf( __('reported %1$s','supportcandy'), $wpscfunction->time_elapsed_timestamp($thread->post_date_gmt) );
							}else{
								$date = sprintf( __('reported %1$s','supportcandy'), $wpscfunction->time_elapsed_string($thread->post_date_gmt) );
							}
							echo $reply ?> <i><small><?php echo $date ?></small></i>
		      </div>
					<?php
				endif;
PATT END */
				do_action( 'wpsc_print_thread_type', $thread_type, $thread );
			  endforeach;
			?>
		</div>

		<?php
		if( !$reply_form_position ){
			include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/reply_form.php';
		}
		?>
		<?php } ?>

  </div>
	
	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">
		
		<?php do_action( 'wpsc_before_ticket_widget', $ticket_id, true )?>
		
		<?php 
		foreach ($ticket_widgets as $key => $ticket_widget): 
			$wpsc_ticket_widget_type = get_term_meta( $ticket_widget->term_id, 'wpsc_ticket_widget_type', true);
			$wpsc_ticket_widget_role = get_term_meta( $ticket_widget->term_id, 'wpsc_ticket_widget_role', true);
			$wpsc_custom_widget_localize = get_option('wpsc_custom_widget_localize');
			$ticket_widget_name = $wpsc_custom_widget_localize['custom_widget_'.$ticket_widget->term_id];
			
			$flag = false;
			if ( $wpsc_ticket_widget_type && (in_array($role_id,$wpsc_ticket_widget_role) || (in_array('customer',$wpsc_ticket_widget_role)) || (is_super_admin($current_user->ID) && is_multisite() ) ) ) {
				$flag = true;
			}
			?>
				<?php do_action( 'wpsc_add_ticket_widget', $ticket_id, $ticket_widget, $ticket_widgets)?>		
				<?php 
					if ( $ticket_widget->slug == "status" && $flag ):
							
							?>
							<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					      <h4 class="widget_header"><i class="fa fa-arrow-circle-right"></i> <?php echo $ticket_widget_name;?>
									<?php if ($wpscfunction->has_permission('change_status',$ticket_id) && $wpscfunction->has_permission('change_agentonly_fields',$ticket_id) && $ticket_status):?>
										<button id="wpsc_individual_change_ticket_status" onclick="wpsc_get_change_ticket_status(<?php echo $ticket_id?>)" class="btn btn-sm wpsc_action_btn" style="<?php echo $edit_btn_css ?>"><i class="fas fa-edit"></i></button>
									<?php endif;?>
								</h4>
								<hr class="widget_divider">
								<?php
								$status = get_term_by('id', $status_id, 'wpsc_statuses');
								$status_color = get_term_meta( $status->term_id, 'wpsc_status_color', true);
								$status_background_color = get_term_meta( $status->term_id, 'wpsc_status_background_color', true);
								$wpsc_custom_status_localize   = get_option('wpsc_custom_status_localize');
								$wpsc_custom_category_localize = get_option('wpsc_custom_category_localize');
								$wpsc_custom_priority_localize = get_option('wpsc_custom_priority_localize');
								?>
								<div class="wpsp_sidebar_labels"><strong><?php _e('Status','supportcandy')?>:</strong> <span class="wpsp_admin_label" style="background-color:<?php echo $status_background_color?>;color:<?php echo $status_color?>;"><?php echo $wpsc_custom_status_localize['custom_status_'.$status_id]?></span></div>
					      <?php $category = get_term_by('id', $category_id, 'wpsc_categories');?>
								<!-- PATT BEGIN <div class="wpsp_sidebar_labels"><strong><?php _e('Category','supportcandy')?>:</strong>  <?php echo $wpsc_custom_category_localize['custom_category_'.$category_id] ?> </div> PATT END -->
								<?php
								$wpsc_hide_show_priority = get_option('wpsc_hide_show_priority');
								if(	$current_user->has_cap('wpsc_agent') || (!$current_user->has_cap('wpsc_agent') && $wpsc_hide_show_priority)):
									$priority = get_term_by('id', $priority_id, 'wpsc_priorities');
									$priority_color = get_term_meta( $priority->term_id, 'wpsc_priority_color', true);
									$priority_backgound_color = get_term_meta( $priority->term_id, 'wpsc_priority_background_color', true);
									?>
									<div class="wpsp_sidebar_labels"><strong><?php _e('Priority','supportcandy')?>:</strong> <span class="wpsp_admin_label" style="background-color:<?php echo $priority_backgound_color?>;color:<?php echo $priority_color?>;"><?php echo $wpsc_custom_priority_localize['custom_priority_'.$priority_id]?> </span></div>
							  <?php endif;
								do_action('wpsc_after_status_widget',$ticket_id);
								?>
			    		</div>
							<?php 
							
					endif;?>
			
				<?php 
					if ( $ticket_widget->slug == "raised-by" && $flag ):
						
								?>
								<div class="row"  id="wpsc_raised_by_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
							      <h4 class="widget_header"><i class="fas fa-user-tie"></i> <?php echo $ticket_widget_name;?>
											<?php if ($wpscfunction->has_permission('change_raised_by',$ticket_id) && $ticket_status):?>
												<button id="wpsc_individual_change_raised_by" onclick="wpsc_get_change_raised_by(<?php echo $ticket_id ?>);"  class="btn btn-sm wpsc_action_btn" style="<?php echo $edit_btn_css ?>" ><i class="fas fa-edit"></i></button>
											<?php endif;?>	
										</h4>
										<hr class="widget_divider">
										<div class="wpsc_thread raised_by_div">
											<div class="thread_avatar">
												<?php 
												$customer_email = $ticket['customer_email'];
												echo get_avatar( $customer_email, 30 );
												?>
											</div>
											<div class="thread_body">
												<div class="thread_user_name"><?php $customer_name  = $ticket['customer_name'];echo stripcslashes($customer_name);?> </div>
											</div>
										</div>
										<?php if($current_user->has_cap('wpsc_agent')){?>
										<div class="wpsp_sidebar_labels" id="wpsc_user_add_info" style="font-size:20px; cursor:pointer;color:#a7a9ab;">
											<i id="wpsc_user_all_tickets" onclick="wpsc_get_all_tickets_of_user(<?php echo $ticket_id ?>, '<?php echo stripslashes($customer_name) ?>');" class="fas fa-envelope wpsc_raised_by_action" title="<?php _e('All Tickets','supportcandy');?>"></i>
											<i id="wpsc_user_extra_info" onclick="wpsc_get_thread_info(<?php echo $ticket_id ?>,<?php echo $thread->ID ?>,'raised_by');" class="fas fa-info-circle wpsc_raised_by_action" title="<?php _e('Ticket Info','supportcandy');?>"></i>
										</div>
									<?php } ?>
							    </div>
								<?php
								
					endif;
				?>
				
				<?php 
					if ($ticket_widget->slug=="additional-recepients" && $flag):
						?>
							<div class="row"  id="wpsc_add_people_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
							      <h4 class="widget_header"><i class="fas fa-envelope"></i> <?php echo $ticket_widget_name;?>
											<button id="wpsc_individual_add_people" onclick="wpsc_get_add_ticket_users(<?php echo $ticket_id ?>);"  class="btn btn-sm wpsc_action_btn" style="<?php echo $edit_btn_css ?>" ><i class="fas fa-edit"></i></button>
										</h4>
										<hr class="widget_divider">
										
					         	<div id="wpsc_additional_users">
											<strong><small><?php _e('Emails:', 'supportcandy'); ?></small></strong>	
											<?php 
											 $extra_email = false;
											 $extra_users_emails = $wpscfunction->get_ticket_meta($ticket_id , 'extra_ticket_users');
											 if($extra_users_emails){
												 foreach ($extra_users_emails as $users_emails) {
													if($users_emails){
													  	?>
														<div style="padding:2px 0;overflow: hidden;"><?php echo get_avatar( $users_emails, 20, '', '')?> <?php echo  $users_emails?> </div>
														<?php 
													}else{
														$extra_email = true;
													}
												 }
											 }else{
												 $extra_email =  true;
											 }
											 if($extra_email){
												 ?>
												 <div class="">
													 <?php	_e('None', 'supportcandy'); ?>
												 </div>
												 <?php
											 }
											 do_action('wpsc_after_extra_users',$ticket_id);
										 ?>
					         	</div>	
										</div>
						<?php
					endif;
				?>
	
				<?php 
					if ($ticket_widget->slug=="assign-agent" && $flag):
						?>
							<div class="row assigned_agent"  id="wpsc_assign_agent_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
							      <h4 class="widget_header"><i class="fas fa-users"></i> <?php echo $ticket_widget_name;?>
											<?php if ($wpscfunction->has_permission('assign_agent',$ticket_id) && $ticket_status):?>
												<button id="wpsc_individual_change_assign_agent" onclick="wpsc_get_change_assign_agent(<?php echo $ticket_id ?>);" class="btn btn-sm wpsc_action_btn" style="<?php echo $edit_btn_css ?>" ><i class="fas fa-edit"></i></button>
											<?php endif;?>
										</h4>
										<hr class="widget_divider">
											<?php
											if($assigned_agents[0]){
							        	foreach ( $assigned_agents as $agent ) {
							 				 		$user = get_term_meta($agent);
													$user_id = isset($user['user_id'][0]) ? $user['user_id'][0] : '';
													?>
													<tr>
														<?php 
														if($user_id){
															$user_data   = get_userdata($user_id);
															$agent_name  = $user_data->display_name;
															$agent_email = $user_data->user_email;
															?>
															<td style="width:25px !important;"><div style="padding:2px 0;overflow: hidden;"><?php echo get_avatar( $agent_email, 20, '', '')?> <?php echo $agent_name?> </div></td>
															<?php  
														}else{
															do_action('wpsc_agent_name_in_individual_ticket',$agent);
														}
														?>
													</tr>
													<?php
							        	}
											}
							        else{
							          _e('None','supportcandy');
							        }
											?>
									</div>
						<?php
					endif;
				?>
				
				<?php 
					if ( $ticket_widget->slug == "ticket-fields" && $flag ):
							
							?>
							<div class="row" id="wpsc_ticket_fields_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
								<h4 class="widget_header"><i class="fab fa-wpforms"></i> <?php echo $ticket_widget_name;?>
									<?php if ( apply_filters('wpsc_get_user_permission',$wpscfunction->has_permission('change_ticket_fields',$ticket_id)) && $ticket_status):?>
										<button id="wpsc_individual_change_ticket_fields" onclick="wpsc_get_change_ticket_fields(<?php echo $ticket_id ?>);" class="btn btn-sm wpsc_action_btn" style="<?php echo $edit_btn_css ?>" ><i class="fas fa-edit"></i></button>
									<?php endif;?>
								</h4>
								<hr class="widget_divider">
								<div id="wpsc_ticket_fields">
									<?php
									$fields = get_terms([
										'taxonomy'   => 'wpsc_ticket_custom_fields',
										'hide_empty' => false,
										'orderby'    => 'meta_value_num',
										'meta_key'	 => 'wpsc_tf_load_order',
										'order'    	 => 'ASC',
										'meta_query' => array(
											array(
												'key'       => 'agentonly',
												'value'     => '0',
												'compare'   => '='
											)
										),
									]);
									$flag=true;
									if($fields){
										$flag=true;
										$cust_flag = true;
										foreach ($fields as $field) {
											$wpsc_tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type',true);
											$value = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
											if($value){
												if($wpsc_tf_type!=0) {
													$cust_flag = false;
												}
												$flag=false;
												$fields_format->get_field_val($field);
											}
									  }
										if($cust_flag){
											_e('No Ticket Fields','supportcandy');
										}	
									}
									?>
					    	</div>
								<?php do_action( 'wpsc_after_ticket_fields_widget', $ticket_id,$fields)?>
					    </div>
							<?php
							
					endif;
				?>
	
				<?php 
					if ( $ticket_widget->slug == "agent-only-fields" && $flag ):
						
							?>
							<div class="row" id="wpsc_agent_only_fields_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
						      <h4 class="widget_header"><i class="fab fa-wpforms"></i> <?php echo $ticket_widget_name;?>
										<?php if ($wpscfunction->has_permission('change_agentonly_fields',$ticket_id) && $ticket_status):?>
											<button id="wpsc_individual_change_agent_fields" onclick="wpsc_get_change_agent_fields(<?php echo $ticket_id ?>)" class="btn btn-sm wpsc_action_btn" style="<?php echo $edit_btn_css ?>" ><i class="fas fa-edit"></i></button>
										<?php endif;?>
									</h4>
									<hr class="widget_divider">
									<div id="wpsc_ticket_aof_fields">
										<?php
										$fields = get_terms([
											'taxonomy'   => 'wpsc_ticket_custom_fields',
											'hide_empty' => false,
											'orderby'    => 'meta_value_num',
											'meta_key'	 => 'wpsc_tf_load_order',
											'order'    	 => 'ASC',
											'meta_query' => array(
												array(
										      'key'       => 'agentonly',
										      'value'     => '1',
										      'compare'   => '='
										    )
											),
										]);
										$cust_aflag = true;
										if($fields){
											$flag=true;
											foreach ($fields as $field) {
												$wpsc_tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type',true);
												$value = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
												if($value){
												  $cust_aflag = false;
													$flag=false;
													$fields_format->get_field_val($field);
												}
											}
										}
										if($cust_aflag){
											_e('No Agent Only Fields','supportcandy');
										}
										?>
									</div>
									<?php do_action( 'wpsc_after_agent_only_fields_widget', $ticket_id,$fields)?>
					    </div>
							<?php
							
					endif;
				?>
				
				<?php
				
					if ($ticket_widget->slug=="biographical-info" && $flag):
						?>
							<div class="row"  id="wpsc_add_biographical_info" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
								<h4 class="widget_header"><i class="fas fa-info-circle"></i> <?php echo $ticket_widget_name;?> </h4>
								
								<hr class="widget_divider">
								
								<div id ="biographical_info">
									
									<?php 	
										$customer_email = $ticket['customer_email'];
										$user = get_user_by('email',$customer_email);
										if ($user) {
											$user_description = get_user_meta($user->ID,'description',true);
											?>
											<?php if ($user_description){ ?>
												 <div style="padding:2px 0;overflow: hidden;"> <?php echo nl2br( stripcslashes($user_description)); ?> </div>
											<?php } else {?>
												 <div style="padding:2px 0;overflow: hidden;"> <?php 	_e('No Biographical Info added.','supportcandy');  ?> </div>
												<?php
											}
										} else {
											?>
										 <div style="padding:2px 0;overflow: hidden;"> <?php 	_e('No Biographical Info added.','supportcandy');  ?> </div>
											<?php
										}
										?>
									</div>
									
								</div>
							<?php
					endif;
			
		endforeach;
		
		?>
	<?php do_action( 'wpsc_after_ticket_widget', $ticket_id)?>		
	</div>
	
</div>
<?php do_action('wpsc_load_individual_ticket'); ?>
<?php
$flag = false;
if((in_array('register_user',$wpsc_allow_rich_text_editor) && !$current_user->has_cap('wpsc_agent')) && $rich_editing){
	$flag = true;
} elseif($current_user->has_cap('wpsc_agent') && $rich_editing){
	$flag = true;
}
//PATT BEGIN
$assigned_agent = $wpscfunction->get_ticket_meta( $ticket_id, assigned_agent, true);
$request_data = $wpscfunction->get_ticket($ticket_id);
$request_status = $request_data['ticket_status'];

if(in_array($request_status, array('3', '4', '5', '670', '63')) && $assigned_agent != '')
{
$wpscfunction->change_status($ticket_id, 64);
}
//PATT END

if ($flag) {
	$wpsc_tinymce_toolbar = get_option('wpsc_tinymce_toolbar');
	$toolbar_active = get_option('wpsc_tinymce_toolbar_active');
	$tinymce_toolbox = array();
	if(is_array($toolbar_active)) {
		foreach ($toolbar_active as $key => $value) {
			 $tinymce_toolbox[] = $wpsc_tinymce_toolbar[$value]['value'];
			if($value == 'blockquote' || $value == 'align' || $value == 'numbered_list' || $value == 'right_to_left'){
				$tinymce_toolbox[] = ' | ';
		 	}
		}
	}

$directionality = $wpscfunction->check_rtl();
?>
<script>
	tinymce.remove();
	tinymce.init({
	  selector:'#wpsc_reply_box',
	  body_id: 'wpsc_reply_box',
	  directionality : '<?php echo $directionality; ?>',
	  menubar: false,
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
							}else {
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
<?php } ?>

<script>

	jQuery(document).ready(function(){
		jQuery('.btn-success.dropdown-toggle').click(function(){
	    if(jQuery(this).parent().hasClass('open')){
        jQuery(this).parent().removeClass('open');
	    } else {
				jQuery(this).parent().addClass('open');
	    }
		});
	});

	// Submit note
	function wpsc_submit_reply( save_type ){
		jQuery('.submit .btn-group').removeClass('open');
		<?php
		if((in_array('register_user',$wpsc_allow_rich_text_editor) && !$current_user->has_cap('wpsc_agent')) && $rich_editing){
				$flag = true;
			}elseif($current_user->has_cap('wpsc_agent') && $rich_editing){
				$flag = true;
			}
			if ($flag) { ?>
				var description = tinyMCE.activeEditor.getContent().trim();
				<?php 
			}else {?>
				var description = jQuery('#wpsc_reply_box').val().trim();
				<?php 
			} ?>

		if(description.length==0){
			alert('<?php _e('Description empty!','supportcandy')?>');
			return;
		}
		switch(save_type){
			case 'note' :
				wpsc_post_note(description);
				break;
			case 'reply':
				wpsc_post_reply(description);
				break;
			case 'canned_reply':
				wpsc_save_canned_reply();
				break;
		}
	}

	function wpsc_post_note(description){
		var dataform = new FormData(jQuery('#wpsc_frm_tkt_reply')[0]);
		jQuery('.wpsc_reply_widget').html(wpsc_admin.loading_html);
		dataform.append('action','wpsc_tickets');
		dataform.append('setting_action','submit_note');
		dataform.append('reply_body',description);
		jQuery.ajax({
			url: wpsc_admin.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		})
		.done(function (response_str) {
			wpsc_open_ticket(<?php echo $ticket_id?>);
		});
		var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
		if(is_tinymce) tinyMCE.activeEditor.setContent('');
	}

	function wpsc_post_reply(description){
		<?php if($wpsc_allow_reply_confirmation){?>
			if(!confirm('<?php _e('Are you sure?','supportcandy')?>')) return;
		<?php } ?>
		var dataform = new FormData(jQuery('#wpsc_frm_tkt_reply')[0]);
		var redirect = <?php echo $wpsc_redirect_to_ticket_list?> ;
		jQuery('.wpsc_reply_widget').html(wpsc_admin.loading_html);
		dataform.append('action','wpsc_tickets');
		dataform.append('setting_action','submit_reply');
		dataform.append('reply_body',description);
		jQuery.ajax({
			url: wpsc_admin.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		})
		.done(function (response_str) {
			redirect = redirect ? wpsc_get_ticket_list() : wpsc_open_ticket(<?php echo $ticket_id?>);
		});
		var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
		if(is_tinymce) tinyMCE.activeEditor.setContent('');
	}

	jQuery(document).ready(function (){
		  jQuery(document).find('.thread_messege').each(function(){
		       var height = parseInt(jQuery(this).height());
					 <?php 
						$wpsc_view_more = get_option('wpsc_view_more');
						if($wpsc_view_more){?>
							if( height > 100){
									jQuery(this).height(100);
									jQuery(this).parent().find('.wpsc_ticket_thread_expander').text(wpsc_admin.view_more);
									jQuery(this).parent().find('.wpsc_ticket_thread_expander').show();
							}<?php
						} else{?>
							 jQuery(this).parent().find('.thread_messege').height('auto');<?php
						} ?>	

		    });
		  	jQuery('.wpsc_ticket_thread_content img').addClass('img-responsive');

		});
</script>

<?php do_action('wpsc_after_individual_ticket',$ticket_id) ?>