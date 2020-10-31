<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$enable_login_settings = get_option('wpsc_default_login_setting');
$custom_login_url      = get_option('wpsc_custom_login_url');
$wpsc_appearance_login_form=get_option('wpsc_appearance_login_form');

$general_appearance = get_option('wpsc_appearance_general_settings');

$signin_button_css = 'background-color:'.$wpsc_appearance_login_form['wpsc_signin_button_bg_color'].' !important;color:'.$wpsc_appearance_login_form['wpsc_signin_button_text_color'].' !important;border-color:'.$wpsc_appearance_login_form['wpsc_signin_button_border_color'].' !important;';
$wpsc_login_captcha 			= get_option('wpsc_login_captcha');
$wpsc_recaptcha_type       		= get_option('wpsc_recaptcha_type');
?>
<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4" style="margin-bottom:20px;">
<?php 
do_action('wpsc_before_signin_module');
if($enable_login_settings=='1') {?>
	
		<h2 class="form-signin-heading"><?php echo __('Please sign in','supportcandy')?></h2>
		
		<form id="frm_wpsc_sign_in" action="javascript:wpsc_sign_in();" method="post" style="margin-bottom:5px;">
			<p id="wpsc_message_login" class="bg-success" style="display:none;"></p>
			<label class="sr-only"><?php echo __('Username or email','supportcandy')?></label>
			<input id="inputEmail" name="username" class="form-control" placeholder="<?php echo __('Username or email','supportcandy')?>" required="" autofocus="" autocomplete="off" type="text" value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : '';?>">
			<label for="inputPassword" class="sr-only"><?php echo __('Password','supportcandy')?></label>
			<input id="inputPassword" name="password" class="form-control" placeholder="<?php echo __('Password','supportcandy')?>" required="" autocomplete="off" type="password">
			<div class="checkbox">
					<label>
							<input name="remember" value="remember-me" type="checkbox"> <p style="color:<?php echo $general_appearance['wpsc_text_color']?> !important;"><?php echo __('Remember me','supportcandy')?></p>
					</label>
					<div class="pull-right forgot-password">
							<a href="<?php echo wp_lostpassword_url()?>" style="color:<?php echo $general_appearance['wpsc_text_color']?> !important;"><?php echo __('Forgot your password?','supportcandy')?></a>
					</div>
			</div>
			<?php
				$wpsc_appearance_create_ticket  = get_option('wpsc_create_ticket');

				if($wpsc_login_captcha && $wpsc_recaptcha_type){
					?>
					<div class="col-md-12 captcha_container" style="margin-bottom:10px;margin-left:0px; display:flex; background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_text_color']?> !important;">
						<div style="width:25px;">
							<input type="checkbox" onchange ="get_captcha_code(this);" id="wpsc_login_captcha_check" class="wpsc_checkbox" value="1">
							<img id="captcha_wait" style="width:16px;display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" alt="">
						</div>
						<div style=""><?php _e("I'm not a robot",'supportcandy')?></div>
					</div>
					<input type="hidden" id="captcha_code" name = "captcha_code" value="">
					<?php
				}elseif($wpsc_login_captcha && !$wpsc_recaptcha_type){
					$wpsc_get_site_key = get_option('wpsc_get_site_key');
					?>
					<div class="col-sm-12" style="margin-bottom:10px;margin-left:0px;display:flex;padding:0px;">
						<div style="width:25px;">
							<div class="g-recaptcha" data-sitekey=<?php echo $wpsc_get_site_key ?>></div>
						</div>
					</div>
					<?php
				}
			?>
			<input type="hidden" name="action" value="wpsc_tickets" />
			<input type="hidden" name="setting_action" value="set_user_login" />
			<input type="hidden" name="nonce" value="<?php echo wp_create_nonce()?>" />
			<button id="wpsc_sign_in_btn" class="btn btn-lg btn-block" type="submit" style="<?php echo $signin_button_css ?>"><?php _e('Sign In','supportcandy')?></button>
		
		</form>
		
	<?php 		
} else {	
		$support_page_id  = get_option('wpsc_support_page_id');
		$support_page_url = get_permalink($support_page_id);
		
		$login_url = wp_login_url($support_page_url);
		if( $enable_login_settings == '3' ) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $custom_login_url)) {				 
				$login_url = "http://" . $custom_login_url;					 
			} else {
				$login_url = $custom_login_url;
			}
		}
		
		?>	
		<input class="btn btn-lg btn-block" type="button" id="wpsc_login_link" onclick="window.location.href='<?php echo $login_url; ?>'" value="<?php _e('Sign In','supportcandy');?>" style="margin-top:80px; margin-bottom: 5px; <?php echo $signin_button_css ?>"/>
		<?php
}

if(get_option('wpsc_user_registration')){
	$enable_user_registration_settings =get_option('wpsc_user_registration_method');
	$wpsc_custom_registration_url=get_option('wpsc_custom_registration_url');
	if($enable_user_registration_settings == '1'){	
	?>
		<button  class="btn btn-lg btn-block" onclick="wpsc_signup_user();" type="button" style="background-color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_button_bg_color']?> !important; color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_text_color']?> !important;border-color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_button_border_color']?> !important;"><?php _e('Register Now','supportcandy')?></button>
	<?php 
	} else {
		$registration_url = wp_registration_url();
		if( $enable_user_registration_settings == '3' ) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $wpsc_custom_registration_url)) {				 
				$registration_url = "http://" . $wpsc_custom_registration_url;					 
			} else {
				$registration_url = $wpsc_custom_registration_url;
			}
		}
	?>
		<a href="<?php echo $registration_url; ?>" class="btn btn-lg btn-block" style="background-color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_button_bg_color']?> !important; color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_text_color']?> !important;border-color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_button_border_color']?> !important;"><?php _e('Register Now','supportcandy')?></a>
	<?php	
	}

}
do_action('wpsc_after_signin_module');
?>	
<?php 
if(get_option('wpsc_allow_guest_ticket')):?>

	<button id="wpsc_login_continue_as_guest" class="btn btn-lg btn-block" onclick="wpsc_get_create_ticket();" type="button" style="background-color:<?php echo $wpsc_appearance_login_form['wpsc_continue_as_guest_button_bg_color']?> !important; color:<?php echo $wpsc_appearance_login_form['wpsc_continue_as_guest_button_text_color']?> !important; border-color:<?php echo $wpsc_appearance_login_form['wpsc_continue_as_guest_button_border_color']?> !important;"><?php _e('Continue as Guest','supportcandy')?></button>
<?php 
endif;?>
</div>
<?php do_action('wpsc_after_guest_module'); ?>

<script>
<?php 
if($enable_login_settings=='1' && $wpsc_login_captcha && $wpsc_recaptcha_type) {
	?>
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
	<?php
}
?>
function wpsc_sign_in(){
	<?php
		if($wpsc_login_captcha && $wpsc_recaptcha_type){
			?>
			if (jQuery('#captcha_code').val().trim().length==0) {
				alert("<?php _e('Please confirm you are not a robot!','supportcandy')?>");
				validation = false;
				return;
			}
			<?php
		}elseif($wpsc_login_captcha && !$wpsc_recaptcha_type){
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
  var dataform = new FormData(jQuery('#frm_wpsc_sign_in')[0]);
  jQuery('#frm_wpsc_sign_in').find('input,button').attr('disabled',true);
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    if (response.error == '1') {
      jQuery('#wpsc_message_login').html(response.message);
      jQuery('#wpsc_message_login').attr('class','bg-danger').slideDown('fast',function(){});
      jQuery('#frm_wpsc_sign_in').find('input,button').attr('disabled',false);
      jQuery('#frm_wpsc_sign_in').find('#inputPassword').val('');
	  setTimeout(function(){ jQuery('#wpsc_message_login').slideUp('fast',function(){}); }, 3000);
	  jQuery("#wpsc_login_captcha_check").prop("checked", false); 
	  jQuery('#captcha_code').val('');
	  jQuery('#wpsc_login_captcha_check').prop("disabled", false);
	  grecaptcha.reset();

    } else {
      jQuery('#wpsc_message_login').html(response.message);
      jQuery('#wpsc_message_login').attr('class','bg-success').slideDown('fast',function(){});
      location.reload(true);
    }
  });
}
</script>
<?php if (!$wpsc_recaptcha_type && $wpsc_login_captcha): ?>
	 <script src='https://www.google.com/recaptcha/api.js'></script>
 <?php endif; ?>