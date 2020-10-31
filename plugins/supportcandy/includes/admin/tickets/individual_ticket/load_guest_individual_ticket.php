<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$reply_form_position = get_option('wpsc_reply_form_position');
$ticket_data         = $wpscfunction->get_ticket($ticket_id);
$customer_email      = $ticket_data['customer_email'];
$status_id           = $ticket_data['ticket_status'];
$priority_id         = $ticket_data['ticket_priority'];
$category_id         = $ticket_data['ticket_category'];
$auth_id             = $ticket_data['ticket_auth_code'];

$general_appearance = get_option('wpsc_appearance_general_settings');

$create_ticket_btn_css  = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';
$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');
$wpsc_thread_date_format  = get_option('wpsc_thread_date_format');
$ticket_widgets = get_terms([
	'taxonomy'   => 'wpsc_ticket_widget',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
  'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wpsc_ticket_widget_load_order')),
	]);

$wpsc_allow_rich_text_editor   = get_option('wpsc_allow_rich_text_editor');

?>

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
	<div class="col-sm-12">
    <button type="button" id="wpsc_load_guest_new_ticket_btn" onclick="wpsc_get_create_ticket();" class="btn btn-sm wpsc_create_ticket_btn" style="<?php echo $create_ticket_btn_css?>"><i class="fa fa-plus"></i> <?php _e('New Ticket','supportcandy')?></button>
		<?php do_action('wpsc_after_guest_indidual_ticket_action_btn',$ticket_id);?>
  </div>
</div>

<div class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">

  <div class="col-sm-8 col-md-9 wpsc_it_body">

    <div class="row wpsc_it_subject_widget">
      <h4>
        [<?php echo get_option('wpsc_ticket_alice').$ticket_id?>] <?php echo $ticket_data['ticket_subject']; ?>
        <?php if ($wpscfunction->has_permission('change_ticket_fields',$ticket_id)):?>
					<button onclick="edit_ticket_subject(<?php echo $ticket_id?>)" class="btn btn-sm wpsc_action_btn"><i class="fas fa-edit"></i></button>
				<?php endif;?>
      </h4>
    </div>

		<?php
		if($reply_form_position){
			include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/guest_reply_form.php';
		}
		?>

		<div class="row wpsc_threads_container">
			<?php
			$order = $reply_form_position ? 'DESC' : 'ASC';
			$args = array(
				'post_type'      => 'wpsc_ticket_thread',
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => $order,
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => 'ticket_id',
			      'value'   => $ticket_id,
			      'compare' => '='
					),
				),
			);
			$threads = get_posts($args);

			foreach ($threads as $thread):
				$reply = stripslashes(addslashes(htmlspecialchars_decode($thread->post_content, ENT_QUOTES)));
				$reply = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $reply);
				$thread_type    = get_post_meta( $thread->ID, 'thread_type', true);
				$customer_name  = get_post_meta( $thread->ID, 'customer_name', true);
				$customer_email = get_post_meta( $thread->ID, 'customer_email', true);
				$attachments    = get_post_meta( $thread->ID, 'attachments', true);

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
								<strong><?php echo $customer_name?></strong><small><i><?php echo $date?></i></small><br>
								<?php if ( apply_filters('wpsc_thread_email_visibility',$current_user->has_cap('wpsc_agent')) ) {?>
									<small><?php echo $customer_email?></small>
								<?php }?>
								<?php if ($wpscfunction->has_permission('delete_ticket',$ticket_id)):?>
									<i class="fa fa-trash thread_action_btn"></i>
									<i class="fa fa-edit thread_action_btn"></i>
								<?php endif;?>
							</div>
							<div class="thread_messege">
							<?php 
								if (in_array('guest_user' , $wpsc_allow_rich_text_editor)) {
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
														<span  class="wpsc_attachment_file_name" style="padding: 7px;"><a href="<?php echo $download_url?>" target="_blank"><?php echo $attach['filename'];?></a></span>
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

				if ($thread_type == 'reply'):
					$user_info = get_user_by('email',$customer_email);
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
						<?php 
						if($wpsc_thread_date_format == 'timestamp'){
							$date = sprintf( __('replied %1$s','supportcandy'), $wpscfunction->time_elapsed_timestamp($thread->post_date_gmt) );
						}else{
							$date = sprintf( __('replied %1$s','supportcandy'), $wpscfunction->time_elapsed_string($thread->post_date_gmt) );
						}
						?>
						<div class="thread_body">
							<div class="thread_user_name">
								<strong><?php echo $customer_name?></strong><small><i><?php echo $date?></i></small><br>
								<?php if ( apply_filters('wpsc_thread_email_visibility',$current_user->has_cap('wpsc_agent')) ) {?>
									<small><?php echo $customer_email?></small>
								<?php }?>
								<?php if ($wpscfunction->has_permission('delete_ticket',$ticket_id)):?>
									<i class="fa fa-trash thread_action_btn"></i>
									<i class="fa fa-edit thread_action_btn"></i>
								<?php endif;?>
							</div>
							<div class="thread_messege">
							<?php 
								if (in_array('guest_user' , $wpsc_allow_rich_text_editor)) {
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
											 $upload_dir   = wp_upload_dir();
											 
											 $wpsp_file = get_term_meta($attachment,'wpsp_file');
											 
											 if ($wpsp_file) {
												 $file_url = $upload_dir['baseurl']  . '/wpsp/'. $attach['save_file_name'];
											 }else {
												if( isset($attach['is_restructured']) && $attach['is_restructured']){
													$updated_time   = get_term_meta($attachment,'time_uploaded',true);
													$time  = strtotime($updated_time);
													$month = date("m",$time);
													$year  = date("Y",$time);
													$file_url     = $upload_dir['baseurl'] . '/wpsc/'.$year.'/'.$month.'/'. $attach['save_file_name'];
												}else{
													$file_url     = $upload_dir['baseurl'] . '/wpsc/'. $attach['save_file_name'];
												}
												 
											 }
											 
											 $download_url = site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
											 ?>
											 <tr class="wpsc_attachment_tr">
												 <td>
													 <span class="wpsc_attachment_file_name" style="padding: 7px;"><a href="<?php echo $download_url?>" target="_blank"><?php echo $attach['filename'];?></a></span>
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
				
				do_action( 'wpsc_print_thread_type', $thread_type, $thread );

			endforeach;
			?>
		</div>

		<?php
		if( !$reply_form_position ){
			include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/guest_reply_form.php';
		}
		?>
  </div>

  <div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">
    
		<?php do_action( 'wpsc_before_ticket_widget', $ticket_id, false )?>
		<?php 
		foreach ($ticket_widgets as $key => $ticket_widget):
			$wpsc_ticket_widget_type = get_term_meta( $ticket_widget->term_id, 'wpsc_ticket_widget_type', true);
			$wpsc_custom_widget_localize = get_option('wpsc_custom_widget_localize');
			$ticket_widget_name = $wpsc_custom_widget_localize['custom_widget_'.$ticket_widget->term_id];
		
		    do_action( 'wpsc_add_ticket_widget', $ticket_id, $ticket_widget, $ticket_widgets);
				
				if ($ticket_widget->slug=="status"):
					if($wpsc_ticket_widget_type) {
						?>
						<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
							 <h4 class="widget_header"><i class="fa fa-arrow-circle-right"></i> <?php echo $ticket_widget_name;?></h4>
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
							<div class="wpsp_sidebar_labels"><strong><?php _e('Category','supportcandy')?>:</strong>  <?php echo $wpsc_custom_category_localize['custom_category_'.$category_id] ?></div>
							<?php
							$wpsc_hide_show_priority = get_option('wpsc_hide_show_priority');
							if(	$wpsc_hide_show_priority):
								$priority = get_term_by('id', $priority_id, 'wpsc_priorities');
								$priority_color = get_term_meta( $priority->term_id, 'wpsc_priority_color', true);
								$priority_backgound_color = get_term_meta( $priority->term_id, 'wpsc_priority_background_color', true);
								?>
								 <div class="wpsp_sidebar_labels"><strong><?php _e('Priority','supportcandy')?>:</strong> <span class="wpsp_admin_label" style="background-color:<?php echo $priority_backgound_color?>;color:<?php echo $priority_color?>;"><?php echo $wpsc_custom_priority_localize['custom_priority_'.$priority_id]?> </span></div>
							<?php endif; ?>	 
						 </div>
						<?php
					}
				endif;
				if ($ticket_widget->slug=="ticket-fields"):
					if($wpsc_ticket_widget_type) {
						?>
						<div class="row" id="wpsc_ticket_fields_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
							<h4 class="widget_header"><i class="fab fa-wpforms"></i> <?php echo $ticket_widget_name;?></h4>
						 <hr class="widget_divider">
						 <?php 
						 $fields = get_terms([
							 'taxonomy'   => 'wpsc_ticket_custom_fields',
							 'hide_empty' => false,
							 'orderby'    => 'meta_value_num',
							 'meta_key'	 => 'wpsc_tf_visibility',
							 'order'    	 => 'ASC',
							 'meta_query' => array(
								 array(
									 'key'       => 'agentonly',
									 'value'     => '0',
									 'compare'   => '='
								 )
							 ),
						 ]);
						 $cust_flag = true;
						 if($fields){
							 foreach ($fields as $field) {
								 $wpsc_tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type',true);
								 $label = $wpscfunction->get_ticket_meta($ticket_id, $field->slug, true);
								 if($label){
									 $cust_flag = false;
									 include_once WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/class-fields-formatting.php';
									 $fields_format = new WPSC_Ticket_Field_Formatting();
									 $fields_format->get_field_val($field);
								 }
							 }
							 if($cust_flag){							
								 _e('No Ticket Fields','supportcandy');
							 }
						 }else{
							 	_e('No Ticket Fields','supportcandy');
						 }
						 ?>
						</div>
					<?php
					}
				endif;
		endforeach;
		?>
		<?php do_action( 'wpsc_after_ticket_widget', $ticket_id, false )?>
		
	</div>
</div>
<?php  

$flag = false;
if ( in_array('guest_user',$wpsc_allow_rich_text_editor)) {
	$flag = true;
}

if($flag){
	$wpsc_tinymce_toolbar = get_option('wpsc_tinymce_toolbar');
	$toolbar_active       = get_option('wpsc_tinymce_toolbar_active');
	$tinymce_toolbox = array();
	foreach ($toolbar_active as $key => $value) {
		 $tinymce_toolbox[] = $wpsc_tinymce_toolbar[$value]['value'];
		 if($value == 'blockquote' || $value == 'align' || $value == 'numbered_list' || $value == 'right_to_left'){
			 $tinymce_toolbox[] = ' | ';
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
	<?php 
	}
	?>

<script>
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
						 }
					<?php
	  			 } else{?>
				 			jQuery(this).parent().find('.thread_messege').height('auto');
					<?php
	 				 } 
					 ?>		
			});
	  jQuery('.wpsc_ticket_thread_content img').addClass('img-responsive');
	});
</script>

<?php do_action('wpsc_after_guest_individual_ticket',$ticket_id) ?>