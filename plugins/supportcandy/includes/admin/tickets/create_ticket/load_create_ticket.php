<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$wpsc_captcha                   = get_option('wpsc_captcha');
$wpsc_terms_and_conditions      = get_option('wpsc_terms_and_conditions');
$wpsc_set_in_gdpr               = get_option('wpsc_set_in_gdpr');
$wpsc_gdpr_html                 = get_option('wpsc_gdpr_html');
$term_url                       = get_option('wpsc_term_page_url');
$wpsc_terms_and_conditions_html = get_option('wpsc_terms_and_conditions_html');
$wpsc_recaptcha_type            = get_option('wpsc_recaptcha_type');
$wpsc_get_site_key= get_option('wpsc_get_site_key');
$wpsc_allow_rich_text_editor = get_option('wpsc_allow_rich_text_editor');

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

include WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/class-ticket-form-field-format.php';

$form_field = new WPSC_Ticket_Form_Field();

$general_appearance = get_option('wpsc_appearance_general_settings');

$create_ticket_btn_css = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';
$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_create_ticket = get_option('wpsc_create_ticket');

$description = get_term_by('slug', 'ticket_description', 'wpsc_ticket_custom_fields' );
$wpsc_desc_status = get_term_meta( $description->term_id, 'wpsc_tf_status', true);

?>
<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  <div class="col-sm-12">
    <button type="button" id="wpsc_load_new_create_ticket_btn" onclick="wpsc_get_create_ticket();" class="btn btn-sm wpsc_create_ticket_btn" style="<?php echo $create_ticket_btn_css?>"><i class="fa fa-plus"></i> <?php _e('New Ticket','supportcandy')?></button>
    <?php if($current_user->ID):?>
			<button type="button" id="wpsc_load_ticket_list_btn" onclick="wpsc_get_ticket_list();" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
		<?php endif;?>
  </div>
</div>
<?php
do_action('wpsc_before_create_ticket');
if(apply_filters('wpsc_print_create_ticket_html',true)):
?>
<div id="create_ticket_body" class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">
	<form id="wpsc_frm_create_ticket" onsubmit="return wpsc_submit_ticket();" method="post">
		<div class="row create_ticket_fields_container">
			<?php 
			foreach ($fields as $field) {
				do_action('print_listing_form_block', $field);
				$form_field->print_field($field);
			}
			?>
		</div>
		
		<?php if($wpsc_captcha) {
			if($wpsc_recaptcha_type){?>
				<div class="row create_ticket_fields_container">
					<div class="col-md-6 captcha_container" style="margin-bottom:10px;margin-right:15px; display:flex; background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_text_color']?> !important;">
						<div style="width:25px;">
							<input type="checkbox" onchange="get_captcha_code(this);" class="wpsc_checkbox" value="1">
							<img id="captcha_wait" style="width:16px;display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" alt="">
						</div>
						<div style="padding-top:3px;"><?php _e("I'm not a robot",'supportcandy')?></div>
					</div>
				</div>
				<?php  
			}
			else {
				?>
				<div class="row create_ticket_fields_container">
					<div class="col-sm-12" style="margin-bottom:10px;margin-right:15px; display:flex">
						<div style="width:25px;">
							<div class="g-recaptcha" data-sitekey=<?php echo $wpsc_get_site_key ?>></div>
						</div>
					</div>
				</div>
				<?php  
			}
		}
		?>
		
		<?php if($wpsc_set_in_gdpr) {?>
			<div class="row create_ticket_fields_container">
				<div class="col-sm-12" style="margin-bottom:10px; display:flex;">
					<div style="width:25px;">
						<input type="checkbox" name="wpsc_gdpr" id="wpsc_gdpr" value="1">
					</div>			   
					<div style="padding-top:3px;">
						<?php echo stripcslashes(html_entity_decode($wpsc_gdpr_html))?>	
					</div>			
				</div>										
			</div>
			<?php  
		   }
			?>
		
		<?php 
		if($wpsc_terms_and_conditions) {?>
			
			<div class="row create_ticket_fields_container">
				<div class="col-sm-12" style="margin-bottom:10px; display:flex;">
					<div style="width:25px;">
						<input type="checkbox" name="terms" id="terms" value="1">
					</div>
					<div style="padding-top:3px;">
						<?php 
						echo stripcslashes(html_entity_decode($wpsc_terms_and_conditions_html));						
						 ?>
					</div>
				</div>						
			</div>
			<?php  
		  }
		?>
		
		<?php
		$wpsc_notify = get_option('wpsc_do_not_notify_setting');
		$wpsc_notify_checkbox = get_option('wpsc_default_do_not_notify_option');
		if($current_user->has_cap('wpsc_agent') && $wpsc_notify) {?>
			
			<div class="row create_ticket_fields_container">
				<div class="col-sm-6" style="margin-bottom:10px; display:flex;">
					<div style="width:25px;">
						<?php $checked = ($wpsc_notify_checkbox == 1) ? 'checked="checked"' : '';?>
						<input <?php echo $checked ?> type="checkbox" name="notify_owner" id="notify_owner" value="1">
					</div>
					<div class="wpsc_notify_owner"style="padding-top:3px;">
						<?php echo __("Don't notify owner",'supportcandy'); ?>
					</div>
				</div>						
			</div>
			<?php  
		  }
		?>
		
		<div class="row create_ticket_frm_submit">
			<button type="submit" id="wpsc_create_ticket_submit" class="btn" style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_border_color']?> !important;"><?php _e('Submit Ticket','supportcandy')?></button>
			<button type="button" id="wpsc_create_ticket_reset" onclick="wpsc_get_create_ticket();" class="btn" style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_border_color']?> !important;"><?php _e('Reset Form','supportcandy')?></button>
		  <?php do_action('wpsc_after_create_ticket_frm_btn');?>
		</div>
		
		<input type="file" id="attachment_upload" class="hidden" onchange="">
		<input type="hidden" id="wpsc_nonce" value="<?php echo wp_create_nonce()?>">
		
		<input type="hidden" name="action" value="wpsc_tickets">
		<input type="hidden" name="setting_action" value="submit_ticket">
		<input type="hidden" id="captcha_code" name="captcha_code" value="">			
		
	</form>
</div>

<?php do_action('patt_custom_imports_tickets', WPSC_PLUGIN_URL); ?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		
		if(jQuery('.wpsc_drop_down,.wpsc_checkbox,.wpsc_radio_btn,.wpsc_category,.wpsc_priority').val != ''){
			wpsc_reset_visibility();
		}

		jQuery('.wpsc_drop_down,.wpsc_checkbox,.wpsc_radio_btn,.wpsc_category,.wpsc_priority').change(function(){
			wpsc_reset_visibility();
		});
    });
		
		jQuery( "#customer_name" ).autocomplete({
      minLength: 1,
      appendTo: jQuery("#wpsc_agent_name").parent(),
      source: function( request, response ) {
        var term = request.term;
        request = {
          action: 'wpsc_tickets',
          setting_action : 'get_users',
          term : term
        }
        jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
          response(data);
        });	
      },
			select: function (event, ui) {
        jQuery('#customer_name').val(ui.item.value);
				jQuery('#customer_email').val(ui.item.email);
      }
    });		
		jQuery('.wpsc_datetime').datetimepicker({
			 dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
				showAnim : 'slideDown',
				changeMonth: true,
				changeYear: true,
			 timeFormat: 'HH:mm:ss'
		 });
	
	function get_captcha_code(e){
		jQuery(e).hide();
		jQuery('#captcha_wait').show();
		var data = {
	    action: 'wpsc_tickets',
	    setting_action : 'get_captcha_code'
	  };
		jQuery.post(wpsc_admin.ajax_url, data, function(response) {
			jQuery('#captcha_code').val(response.trim());;
			jQuery('#captcha_wait').hide();
			jQuery(e).show();
			jQuery(e).prop('disabled',true);
	  });
	}
	
	function wpsc_reset_visibility(){
		
		jQuery('.wpsc_form_field').each(function(){
			var visible_flag = false;
			var visibility = jQuery(this).data('visibility').trim();
			if(visibility){
				visibility = visibility.split(';;');
				jQuery(visibility).each(function(key, val){
					var condition = val.split('--');
					var cond_obj = jQuery('.field_'+condition[0]);
					var field_type = jQuery(cond_obj).data('fieldtype');
					switch (field_type) {
						
						case 'dropdown':
							if ( jQuery(cond_obj).hasClass('visible') && jQuery(cond_obj).find('select').val()==condition[1] ) visible_flag=true;
							break;
							
						case 'checkbox':
							var check = false;
							jQuery(cond_obj).find('input:checked').each(function(){
								if(jQuery(this).val()==condition[1]) check=true;
							});
							if ( jQuery(cond_obj).hasClass('visible') && check ) visible_flag=true;
							break;
							
						case 'radio':
							if ( jQuery(cond_obj).hasClass('visible') && jQuery(cond_obj).find('input:checked').val()==condition[1] ) visible_flag=true;
							break;
							
					}
				});
				if (visible_flag) {
					jQuery(this).removeClass('hidden');
					jQuery(this).addClass('visible');
				} else {
					jQuery(this).removeClass('visible');
					jQuery(this).addClass('hidden');
					var field_type = jQuery(this).data('fieldtype');
					switch (field_type) {
						
						case 'text':
						case 'email':
						case 'number':
						case 'date':
						case 'datetime':
						case 'url':
						case 'time':
							jQuery(this).find('input').val('');
							break;
							
						case 'textarea':
							jQuery(this).find('textarea').val('');
							break;
						
						case 'dropdown':
							jQuery(this).find('select').val('');
							break;
							
						case 'checkbox':
							jQuery(this).find('input:checked').each(function(){
								jQuery(this).prop('checked',false);
							});
							break;
							
						case 'radio':
							jQuery(this).find('input:checked').prop('checked',false);
							break;
							
					}
				}
			}
		});
		
	}
	
	function wpsc_attachment_upload(id,name){
		jQuery('#attachment_upload').unbind('change');
    jQuery('#attachment_upload').on('change', function() {
			
			var flag = false;
	    var file = this.files[0];
	    jQuery('#attachment_upload').val('');
      
			var allowedExtension = ['exe', 'php'];
	    var file_name_split = file.name.split('.');
	    var file_extension = file_name_split[file_name_split.length-1];
			file_extension = file_extension.toLowerCase();	
			<?php 
				$attachment = get_option('wpsc_allow_attachment_type');
				$attachment_data =  explode(',' , $attachment );
				$attachment_data =  array_map('trim', $attachment_data);
				$attachment_data =  array_map('strtolower', $attachment_data);
			?>
			var allowedExtensionSetting = [<?php echo '"'.implode('","', $attachment_data).'"' ?>];

			if(!flag && (jQuery.inArray(file_extension,allowedExtensionSetting)  <= -1 || jQuery.inArray(file_extension,allowedExtension) > -1)) {
				flag = true;
				alert("<?php _e('Attached file type not allowed!','supportcandy')?>");
			}

			var current_filesize=file.size/1000000;
		
			if(current_filesize><?php echo get_option('wpsc_attachment_max_filesize')?>){
				flag = true;
				alert("<?php _e('File size exceed allowed limit!','supportcandy')?>");
			}

		if (!flag){

			var html_str = '<div class="row wpsp_attachment">'+
				'<div class="progress" style="float: none !important; width: unset !important;">'+
					'<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">'+
							file.name+
							'</div>'+
						'</div>'+
						'<img onclick="attachment_cancel(this);" class="attachment_cancel" src="<?php echo WPSC_PLUGIN_URL.'asset/images/close.png'?>" style="display:none;" />'+
					'</div>';

					jQuery('#'+id).append(html_str);

					var attachment = jQuery('#'+id).find('.wpsp_attachment').last();

					var data = new FormData();
						data.append('file', file);
						data.append('arr_name', name);
						data.append('action', 'wpsc_tickets');
            data.append('setting_action', 'upload_file');
            data.append('nonce', jQuery('#wpsc_nonce').val().trim());

						jQuery.ajax({
							type: 'post',
							url: wpsc_admin.ajax_url,
              data: data,
							xhr: function(){
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
              success: function(response) {
						
								var return_obj=JSON.parse(response);
						    jQuery(attachment).find('.attachment_cancel').show();
										
								if( parseInt(return_obj.id) != 0 ){
              		jQuery(attachment).append('<input type="hidden" name="'+name+'[]" value="'+return_obj.id+'">');
                  jQuery(attachment).find('.progress-bar').addClass('progress-bar-success');
                } else {
                    jQuery(attachment).find('.progress-bar').addClass('progress-bar-danger');
                  }
								}
							});
						
						}

    });
		jQuery('#attachment_upload').trigger('click');
	}

	<?php do_action('patt_print_js_functions_create'); ?>
	
	function wpsc_submit_ticket(){
		
		var validation = true;
		
		/*
			Required fields
		*/
		jQuery('.visible.wpsc_required').each(function(e){
			var field_type = jQuery(this).data('fieldtype');
			switch (field_type) {
				case 'text':
				case 'email':
				case 'number':
				case 'date':
				case 'url':
				case 'time':
					if(jQuery(this).find('input').val()=='') validation=false;
					break;
		
				case 'textarea':
					if(jQuery(this).find('textarea').val()=='') validation=false;
					break;
		
				case 'dropdown':
					if(jQuery(this).find('select').val()=='') validation=false;
					break;
		
				case 'checkbox':
				case 'radio':
					if(jQuery(this).find('input:checked').length==0) validation=false;
					break;
					
				case 'file_attachment':
					if(jQuery(this).find('.attachment_container').is(':empty')){
						validation=false;
					}
					break;
					
				case 'tinymce':
				 	<?php 
					
					$rich_editing = $wpscfunction->rich_editing_status($current_user);
				 
					 $flag = false;
				 	if($wpsc_desc_status && is_user_logged_in() && (in_array('register_user',$wpsc_allow_rich_text_editor) && !$current_user->has_cap('wpsc_agent') ) && $rich_editing){
						$flag = true;
					} elseif ($wpsc_desc_status && $current_user->has_cap('wpsc_agent') && is_user_logged_in() && $rich_editing){
						$flag = true;
					}elseif (!is_user_logged_in() && $wpsc_desc_status && in_array('guest_user',$wpsc_allow_rich_text_editor)){
						$flag = true;
					}
				 
					 if($flag){
				 		?>
						var description = tinyMCE.activeEditor.getContent();
						if(description.trim().length==0) validation=false;
						break;
					<?php 
					}else {?>
						if(jQuery('#ticket_description').val()=='') validation=false;
						break;
						<?php 
					}?>
			}
			if (!validation) return;
		});
		if (!validation) {
			alert("<?php _e('Required fields can not be empty!','supportcandy')?>");
			return false;
		}
		
		/*
			Emails
		*/
		jQuery('.wpsc_email').each(function(e){
			var email = jQuery(this).val().trim();
			if(email.length>0 && !validateEmail(email)) {
				validation=false;
				jQuery(this).focus();
			}
			if (!validation) return;
		});
		if (!validation) {
			alert("<?php _e('Incorrect email address!','supportcandy')?>");
			return false;
		}
		
		/*
			URLs
		*/
		jQuery('.wpsc_url').each(function(e){
			var url = jQuery(this).val().trim();
			if(url.length>0 && !validateURL(url)) {
				validation=false;
				jQuery(this).focus();
			}
			if (!validation) return;
		});
		if (!validation) {
			alert("<?php _e('Incorrect URL!','supportcandy')?>");
			return false;
		}
			
		<?php	do_action('wpsc_create_ticket_validation');	?>
		
		/*
			Captcha
		*/
		<?php
		if( $wpsc_captcha ) { 
			if( $wpsc_recaptcha_type ){?>
				if (jQuery('#captcha_code').val().trim().length==0) {
					alert("<?php _e('Please confirm you are not a robot!','supportcandy')?>");
					validation=false;
					return false;
				}
				<?php
			}
			else {?>
				var recaptcha = jQuery("#g-recaptcha-response").val();
				if (recaptcha === "") {
					alert("<?php _e('Please confirm you are not a robot!','supportcandy')?>");
					validation=false;
					return false;
				}<?php
			}
		}
		?>
		
		<?php
		if($wpsc_set_in_gdpr) { ?>
				if (!jQuery('#wpsc_gdpr').is(':checked')){
	 	     alert("<?php _e('Ticket can not be created unless you agree to privacy policy.','supportcandy')?>");
	 	     return false;
	 	   }
		<?php
		}
		?>
			
		<?php
		if($wpsc_terms_and_conditions) { ?>
				if (!jQuery('#terms').is(':checked')){
	 	     alert("<?php _e('Ticket can not be created unless you agree to terms & coditions.', 'supportcandy')?>");
	 	     return false;
	 	   }
		<?php
		}
		?>
		
		if (validation) {
// 			var dataform = new FormData(jQuery('#wpsc_frm_create_ticket')[0]);
					// CR - SCroll To Top
					jQuery("html, body").animate({ scrollTop: 0 }, "slow");
// 			                        //New get DataTable data in the form of an
                        var data = jQuery('#boxinfodatatable').DataTable().rows().data().toArray();

                        var data = JSON.stringify(jQuery('#boxinfodatatable').toJson());

                        var dataform = new FormData(jQuery('#wpsc_frm_create_ticket')[0]);

                        dataform.append('boxinfo', data);
			
			var is_tinymce = true;
			<?php

			$rich_editing = $wpscfunction->rich_editing_status($current_user);
			$flag = false;
			
			if( is_user_logged_in() && (in_array('register_user',$wpsc_allow_rich_text_editor) && !$current_user->has_cap('wpsc_agent') ) && $rich_editing){
				$flag = true;
			} elseif (  $current_user->has_cap('wpsc_agent') && is_user_logged_in() && $rich_editing){
				$flag = true;
			}elseif (!is_user_logged_in() && in_array('guest_user',$wpsc_allow_rich_text_editor)){
				$flag = true;
			}
			if($wpsc_desc_status){
				if($flag){
				?>
					//PATT var description = tinyMCE.get('ticket_description').getContent().trim();
					//PATT dataform.append('ticket_description', description);
					//PATT is_tinymce = true;
				<?php
				}else{
					?>
					//PATT var description = jQuery('#ticket_description').val();
					//PATT dataform.append('ticket_description',description);
					//PATT is_tinymce = false;
					<?php
				}
			}
			?>
			jQuery('#create_ticket_body').html(wpsc_admin.loading_html);
			//wpsc_doScrolling('.wpsc_tl_action_bar',1000);
		  jQuery.ajax({
		    url: wpsc_admin.ajax_url,
		    type: 'POST',
		    data: dataform,
		    processData: false,
		    contentType: false
		  })
		  .done(function (response_str) {
		    var response = JSON.parse(response_str);
				if(response.redirct_url==''){
					jQuery('#create_ticket_body').html(response.thank_you_page);
				} else {
					window.location.href = response.redirct_url;
				}
		  });
			<?php  if($wpsc_desc_status){ ?>
				//PATT if(is_tinymce) tinyMCE.activeEditor.setContent('');
			<?php } ?>
			return false;
		}
		
	}
	<?php do_action('wpsc_print_ext_js_create_ticket');	?>
	
</script>
 <?php if (!$wpsc_recaptcha_type && $wpsc_captcha): ?>
	 <script src='https://www.google.com/recaptcha/api.js'></script>
 <?php endif; ?>
<?php
endif;
?>