<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction, $current_user;

$fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
 ]);
 
foreach ($fields as $field) {
	if($field->name=='ticket_description'){
		$term_id=$field->term_id;
	}
}

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');
$reply_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_submit_reply_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_submit_reply_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_submit_reply_btn_border_color'].'!important';

$wpsc_appearance_create_ticket = get_option('wpsc_create_ticket');
$extra_info_css = 'color:'.$wpsc_appearance_create_ticket['wpsc_extra_info_text_color'].' !important;';

$reply_to_close_ticket = get_option('wpsc_allow_reply_to_close_ticket');
$wpsc_close_ticket_status = get_option('wpsc_close_ticket_status');
$wpsc_allow_reply_confirmation = get_option('wpsc_allow_reply_confirmation');

$allow_reply = true;
if($status_id == $wpsc_close_ticket_status && !in_array('customer', $reply_to_close_ticket)){
	$allow_reply = false;
}
if($allow_reply):

?>
<div class="row wpsc_reply_widget" style="margin-top:20px;">
  <form id="wpsc_frm_tkt_reply" action="javascript:wpsc_post_reply();" method="post">
		<?php if(!$current_user->ID):?>
			<div class="col-sm-6 form-group attachment" style="padding-right:3px;">
				<label for="wpsc_guest_name"><?php _e('Your Name','supportcandy');?></label>
				<p class="help-block" style="<?php echo $extra_info_css ?>"><?php _e('Please insert your name','supportcandy');?></p>
				<input id="wpsc_guest_name" class="form-control" name="customer_name" value="" required />
			</div>
			<div class="col-sm-6 form-group attachment" style="padding-right:3px;">
				<label for="wpsc_guest_email"><?php _e('Your Email','supportcandy');?></label>
				<p class="help-block" style="<?php echo $extra_info_css ?>"><?php _e('Please insert your email address','supportcandy');?></p>
				<input type="email" id="wpsc_guest_email" class="form-control" name="customer_email" value="" required />
			</div>
		<?php endif;?>
		<div class="col-sm-12 attachment">
			<textarea id="wpsc_reply_box" name="reply_body" class="wpsc_textarea"></textarea>
		</div>
		
    <div class="col-sm-10 attachment">
      <div class="row attachment_link">
		<?php 
		$notice_flag = false;
		$wpsc_allow_attach_reply_form = get_option('wpsc_allow_attach_reply_form');
		if( (in_array('customers',$wpsc_allow_attach_reply_form) && is_user_logged_in() && !$current_user->has_cap('wpsc_agent')) || 
			(in_array('guests',$wpsc_allow_attach_reply_form) && !is_user_logged_in()) ){
				$notice_flag = true;
			?>
			<span onclick="wpsc_attachment_upload('<?php echo 'attach_'.$term_id?>','desc_attachment');"><?php _e('Attach file','supportcandy')?></span>
			<?php
		}
		?>
		
	<div class="form-group">
		<?php					
			$wpsc_allow_attachment_type = get_option('wpsc_allow_attachment_type');
			$max_attachment_limit = get_option('wpsc_attachment_max_filesize');
			$wpsc_show_attachment_notice  = get_option('wpsc_show_attachment_notice');
			if($wpsc_show_attachment_notice && $notice_flag){
				$attach_notice = get_option('wpsc_attachment_notice');
				?> 
				<p class="help-block"> <i> <?php echo $attach_notice;?></i></p>
		   <?php 
			}
		?>
	</div>
			
      </div>
      <div id="<?php echo 'attach_'.$term_id?>" class="row attachment_container" style="padding-right:15px;"></div>
    </div>
	
		
    <div class="col-sm-2 submit">
      <button type="submit" id="wpsc_guest_reply_btn" style="width:100%; <?php echo $reply_btn_css ?>" class="btn"><?php _e('Reply','supportcandy')?></button>
    </div>
		<input type="file" id="attachment_upload" class="hidden" onchange="">
    <input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id)?>">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce($ticket_id)?>">
  </form>
</div>

<script>
	function wpsc_post_reply(){
		var email = jQuery('#wpsc_guest_email').val().trim();
		if(email.length>0 && !validateEmail(email)) {
			alert("<?php _e('Incorrect email address!','supportcandy')?>");
			return false;
		}
		var description = '';
		<?php 
			$wpsc_allow_rich_text_editor = get_option('wpsc_allow_rich_text_editor');
			 $flag = false;
			 $rich_editing = $wpscfunction->rich_editing_status($current_user);
 			if((in_array('register_user',$wpsc_allow_rich_text_editor) && !$current_user->has_cap('wpsc_agent')) && $rich_editing){
				$flag = true;
 			}elseif($current_user->has_cap('wpsc_agent') && $rich_editing){
				$flag = true;
 			}elseif( !is_user_logged_in() && in_array('guest_user',$wpsc_allow_rich_text_editor)){
				$flag = true;
 			} 

 			if($flag) {
			 ?>
		 	var description = tinyMCE.activeEditor.getContent().trim();
			if(description.length==0){
				alert('<?php _e('Description empty!','supportcandy')?>');
				return;
			}
			<?php
	 	 }else{
			?>
			
			var description = jQuery('#wpsc_reply_box').val().trim();
			if(description.length==0){
				alert('<?php _e('Description empty!','supportcandy')?>');
				return;
			}
			<?php 
		 }?>

			<?php 
			if($wpsc_allow_reply_confirmation){?>
				if(!confirm('<?php _e('Are you sure?','supportcandy')?>')) return;
			<?php
			 } 
			 ?>
		var dataform = new FormData(jQuery('#wpsc_frm_tkt_reply')[0]);
		dataform.append('action','wpsc_tickets');
		dataform.append('setting_action','submit_reply');
		dataform.append('reply_body',description);
		jQuery('.wpsc_reply_widget').html(wpsc_admin.loading_html);
		jQuery.ajax({
			url: wpsc_admin.ajax_url,
			type: 'POST',
			data: dataform,
			processData: false,
			contentType: false
		})
		.done(function (response_str) {
			window.location.reload();
		});
	}
	
function wpsc_attachment_upload(id,name){
	 jQuery('#attachment_upload').unbind('change');
	 jQuery('#attachment_upload').on('change', function(){
		 var flag = false;
		 var file = this.files[0];
	   jQuery('#attachment_upload').val('');
		 var file_name_split = file.name.split('.');
		 var file_extension = file_name_split[file_name_split.length-1];
		 var allowedExtension = ['exe', 'php'];
		 file_extension = file_extension.toLowerCase();  		
		 <?php 
		 $attachment      = get_option('wpsc_allow_attachment_type');
		 $attachment_data = explode(',' , $attachment);
		 $attachment_data = array_map('trim', $attachment_data);
		 $attachment_data = array_map('strtolower', $attachment_data);
		 ?>
		 var allowedExtensionSetting = [<?php echo '"'.implode('","', $attachment_data).'"' ?>];
		 
		 if(!flag && (jQuery.inArray(file_extension,allowedExtensionSetting) <= -1 || jQuery.inArray(file_extension,allowedExtension) > -1)) {
			 flag = true;
			 alert("<?php _e('Attached file type not allowed!','supportcandy')?>");
		 }
	
		 var current_filesize=file.size/1000000;
		 if(current_filesize><?php echo get_option('wpsc_attachment_max_filesize')?>){
			 flag = true;
			 alert('<?php _e('File size exceed allowed limit!','supportcandy')?>');
		 }
		 
		 if(!flag){
			 
			 var html_str = '<div class="row wpsc_attachment">'+
										'<div class="progress" style="float: none !important; width: unset !important;">'+
												'<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">'+
													file.name+
												'</div>'+
										'</div>'+
										'<img onclick="attachment_cancel(this);" class="attachment_cancel" src="<?php echo WPSC_PLUGIN_URL.'asset/images/close.png'?>" style="display:none;" />'+
								'</div>';
						
			  jQuery('#'+id).append(html_str);
				
				var attachment = jQuery('#'+id).find('.wpsc_attachment').last();
				
				var data = new FormData();
				data.append('file',file);
				data.append('arr_name',name);
				data.append('action','wpsc_tickets');
				data.append('setting_action','upload_file');
	
			  jQuery.ajax({
					 type : 'post',
					 url : wpsc_admin.ajax_url,
					 data : data,
					 xhr : function(){
				       var xhr = new window.XMLHttpRequest();
	              xhr.upload.addEventListener("progress", function(evt){
	                  if (evt.lengthComputable) {
	                      var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
	                      jQuery(attachment).find('.progress-bar').css('width',percentComplete+'%');
	                  }
	              }, false);
	              return xhr;
					 },
					 processData: false,
	         contentType: false,
					 success : function(response){
             
						 var return_obj =JSON.parse(response);
						 
						 jQuery(attachment).find('.attachment_cancel').show();
						 
						 if(parseInt(return_obj.id) != 0){
							 jQuery(attachment).append('<input type="hidden" name="'+name+'[]" value="'+return_obj.id+'">');
							 jQuery(attachment).find('.progress-bar').addClass('progress-bar-success');
						 }else{
							 jQuery(attachment).find('.progress-bar').addClass('.progress-bar-danger');
						 }
					 }
		     });
		  }
  });
	jQuery('#attachment_upload').trigger('click');
}
</script>
<?php
endif;
?>