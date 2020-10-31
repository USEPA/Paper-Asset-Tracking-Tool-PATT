
<?php
if ( ! defined( 'ABSPATH' ) ) 
{
	exit; // Exit if accessed directly
}
global $current_user;
$wpsc_appearance_signup = get_option('wpsc_appearance_signup');
$general_appearance     = get_option('wpsc_appearance_general_settings');
$wpsc_appearance_create_ticket = get_option('wpsc_create_ticket');
$wpsc_set_in_gdpr               = get_option('wpsc_set_in_gdpr');
$wpsc_terms_and_conditions      = get_option('wpsc_terms_and_conditions');
$wpsc_gdpr_html                 = get_option('wpsc_gdpr_html');
$wpsc_terms_and_conditions_html = get_option('wpsc_terms_and_conditions_html');
$wpsc_registration_captcha = get_option('wpsc_registration_captcha');
$wpsc_recaptcha_type       = get_option('wpsc_recaptcha_type');
$wpsc_get_site_key         = get_option('wpsc_get_site_key');
$wpsc_get_secret_key       = get_option('wpsc_get_secret_key');

?>
	<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
			<h2 class="form-signup-heading"><?php echo __('Please Sign Up','supportcandy')?></h2>
		  <form id="wpsc_frm_signup_user" method="post">
						<div class="form-group reg_required">
						 <label for="wpsc_register_user_first_name" style="color:<?php echo $general_appearance['wpsc_text_color']?> !important;"><?php _e('First Name','supportcandy');?></label>
						 <span class="wpsc_reg_required_sym">*</span>
						 <input type="text" id="wpsc_register_user_first_name"  class="form-control" name="wpsc_register_user_first_name"   />
						 <div id="wpsc_register_username_error"></div>
					 	</div>
					 
						<div class="form-group reg_required">
							<label for="wpsc_register_user_last_name" style="color:<?php echo $general_appearance['wpsc_text_color']?> !important;"><?php _e('Last Name','supportcandy');?></label>
							<span class="wpsc_reg_required_sym">*</span>
							<input type="text" id="wpsc_register_user_last_name"  class="form-control" name="wpsc_register_user_last_name"   />
							<div id="wpsc_register_username_error"></div>
						</div>
					 
				   <div class="form-group reg_required">
					 <label for="wpsc_register_user_name" style="color:<?php echo $general_appearance['wpsc_text_color']?> !important;"><?php _e('Username','supportcandy');?></label>
					 <span class="wpsc_reg_required_sym">*</span>
			       <input type="text" id="wpsc_register_user_name"  class="form-control" name="wpsc_register_user_name"   />
						 <div id="wpsc_register_username_error"></div>
				   </div>
					
		       <div class="form-group reg_required">
				 <label for="wpsc_register_email" style="color:<?php echo $general_appearance['wpsc_text_color']?> !important;"><?php _e('Email','supportcandy');?></label>
				 <span class="wpsc_reg_required_sym">*</span>
		         <input id="wpsc_register_email" class="form-control" name="wpsc_register_email" />      
				     <div id="wpsc_register_email_error"></div>
		       </div>
		    
			     <div class="form-group reg_required">
					 <label for="wpsc_register_pass" style="color:<?php echo $general_appearance['wpsc_text_color']?> !important;"><?php _e('Password','supportcandy');?></label>
					 <span class="wpsc_reg_required_sym">*</span>
			         <input type="password" id="wpsc_register_pass" class="form-control" name="wpsc_register_pass" />      
			     </div>

			     <div class="form-group reg_required">
					 <label for="wpsc_register_confirmpass" style="color:<?php echo $general_appearance['wpsc_text_color']?> !important;" ><?php _e('Confirm Password','supportcandy');?></label>
					 <span class="wpsc_reg_required_sym">*</span>
			         <input type="password" id="wpsc_register_confirmpass" class="form-control" name="wpsc_register_confirmpass"  />      
			     </div>
		    	
					<?php
						if($wpsc_registration_captcha && $wpsc_recaptcha_type){
							?>
							<div class="col-md-12 captcha_container" style="margin-bottom:10px;margin-left:0px; display:flex; background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_text_color']?> !important;">
								<div style="width:25px;">
									<input type="checkbox" onchange ="get_captcha_code(this);" class="wpsc_checkbox" value="1">
									<img id="captcha_wait" style="width:16px;display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" alt="">
								</div>
								<div style="padding-top:3px;"><?php _e("I'm not a robot",'supportcandy')?></div>
							</div>
							<?php
						}elseif($wpsc_registration_captcha && !$wpsc_recaptcha_type){
							?>
							<div class="col-sm-12" style="margin-bottom:10px;margin-left:0px;display:flex;padding:0px;">
								<div style="width:25px;">
									<div class="g-recaptcha" data-sitekey=<?php echo $wpsc_get_site_key ?>></div>
								</div>
							</div>
							<?php
						}
					?>

			<?php if($wpsc_set_in_gdpr) {?>
				<div class="row create_ticket_fields_container">
					<div class="col-sm-12" style="margin-bottom:10px;display:flex; right:35px;">
						<div style="width:50px;">
							<input type="checkbox" name="wpsc_gdpr" id="wpsc_gdpr"value="1">
						</div>			   
						<div style="">
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
					<div class="col-sm-12" style="margin-bottom:30px;display:flex;right:35px;">
						<div style="width:25px;">
							<input type="checkbox" name="wpsc_terms" id="wpsc_terms" value="1">
						</div>
						<div style="">
							<?php 
							echo stripcslashes(html_entity_decode($wpsc_terms_and_conditions_html));						
							?>
						</div>
					</div>						
				</div>
				<?php  
			}
			?>


			     <div class="form-group">
			         <button type="submit" class="btn btn-sm" name='btnsubmit' onclick="javascript:wpsc_register_user(event);" style="background-color:<?php echo $wpsc_appearance_signup['wpsc_appearance_signup_button_bg_color']?> !important; color:<?php echo $wpsc_appearance_signup['wpsc_appearance_signup_button_text_color']?> !important; border-color:<?php echo $wpsc_appearance_signup['wpsc_appearance_signup_button_border_color']?> !important;"><?php _e('Register Now','supportcandy')?></button>
						   <button type="cancel" class="btn btn-sm" name='btncancel' onclick="javascript:wpsc_sign_in();" style="background-color:<?php echo $wpsc_appearance_signup['wpsc_appearance_cancel_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_signup['wpsc_appearance_cancel_button_text_color']?> !important; border-color:<?php echo $wpsc_appearance_signup['wpsc_appearance_cancel_button_border_color']?> !important;"><?php _e('Cancel','supportcandy');?></button>
	         </div>
					 
					 <input type="hidden" name="action" value="wpsc_tickets" />
			 		 <input type="hidden" name="setting_action" value="submit_user" />
					 <input type="hidden" id="captcha_code" name = "captcha_code" value="">
					 
	  </form>
 </div>
 

<script type="text/javascript">

function wpsc_register_user(event) {
	event.preventDefault();
	jQuery('#wpsc_register_email_error').html('');
	jQuery('#wpsc_register_username_error').html('');
	
	var validation = true;
	
	jQuery('.reg_required').each(function(e){
		if(jQuery(this).find('input').val()=='') validation = false;
	});
	
	if (!validation) {
		alert("<?php _e('Required fields can not be empty!','supportcandy')?>");
		return ;
	}
	
	var email       = jQuery('#wpsc_register_email').val();
  	var password   	= jQuery('#wpsc_register_pass').val();
	var confirmpass = jQuery('#wpsc_register_confirmpass').val();
	
	if(!validateEmail(email)) {
		validation = false;
	 	alert('<?php _e('Please enter correct email address!','supportcandy')?>');
		return;
	}
	
	if(password!=confirmpass) {
		validation = false;
		alert('<?php _e('Password and confirm password does not match.','supportcandy')?>');
		return;
	}else{
		<?php
		if($wpsc_registration_captcha && $wpsc_recaptcha_type){
			?>
			if (jQuery('#captcha_code').val().trim().length==0) {
				alert("<?php _e('Please confirm you are not a robot!','supportcandy')?>");
				validation = false;
				return;
			}
			<?php
		}elseif($wpsc_registration_captcha && !$wpsc_recaptcha_type){
			?>
			var recaptcha = jQuery("#g-recaptcha-response").val();
			if (recaptcha === "") {
				alert("<?php _e('Please confirm you are not a robot!','supportcandy')?>");
				validation = false;
				return;
			}
			<?php
		}
		?>
	}
	
	<?php
		if($wpsc_set_in_gdpr) { ?>
				if (!jQuery('#wpsc_gdpr').is(':checked')){
	 	     alert("<?php _e('Please accept privacy policy.','supportcandy')?>");
	 	     return false;
	 	   }
		<?php
		}
		?>
			
		<?php
		if($wpsc_terms_and_conditions) { ?>
				if (!jQuery('#wpsc_terms').is(':checked')){
	 	     alert("<?php _e('Please agree to terms & coditions.', 'supportcandy')?>");
	 	     return false;
	 	   }
		<?php
		}
		?>

	if(validation){
		var dataform = new FormData(jQuery('#wpsc_frm_signup_user')[0]);
		jQuery.ajax({
		    url: wpsc_admin.ajax_url,
		    type: 'POST',
		    data: dataform,
		    processData: false,
		    contentType: false
		  })
		.done(function (response_str) {
			var response=JSON.parse(response_str);
			if (response.error == '1'){
				jQuery('#wpsc_register_email_error').html("<?php _e('This email is already registered, please choose another one.','supportcandy')?>");
			} else if(response.error == '2'){
				jQuery('#wpsc_register_username_error').html("<?php _e('This username is already registered. Please choose another one.','supportcandy')?>");
			} else {
				jQuery('#wpsc_register_email_error').hide();
				location.reload(true);
			}
		});	
	}
	
}

function get_captcha_code(e){
	jQuery(e).hide();
	jQuery('#captcha_wait').show();
	var data = {
		action: 'wpsc_tickets',
		setting_action : 'get_captcha_code'
	};
	jQuery.post(wpsc_admin.ajax_url, data, function(response) {
		jQuery('#captcha_code').val(response);
		jQuery('#captcha_wait').hide();
		jQuery(e).show();
		jQuery(e).prop('disabled',true);
	});
}
</script>
<?php if (!$wpsc_recaptcha_type && $wpsc_registration_captcha): ?>
	 <script src='https://www.google.com/recaptcha/api.js'></script>
 <?php endif; ?>