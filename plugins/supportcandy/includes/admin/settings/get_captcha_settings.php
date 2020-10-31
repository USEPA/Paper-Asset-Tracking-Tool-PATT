<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}
?>

<form id="wpsc_frm_captcha_settings" method="post" action="javascript:wpsc_set_captcha_settings();">
  <div class="form-group">
    <label for="wpsc_captcha"><?php _e('Allow Captcha','supportcandy');?></label>
    <p class="help-block"><?php _e("Enable or disable Captcha in ticket form.","supportcandy");?></p>
    <select class="form-control wpsc_catpcha_setting" name="wpsc_captcha" id="wpsc_captcha">
      <?php
      $wpsc_captcha = get_option('wpsc_captcha');
      $selected = $wpsc_captcha == '1' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
      $selected = $wpsc_captcha == '0' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
      ?>
    </select>
  </div>
	
	<div class="form-group">
    <label for="wpsc_registration_captcha"><?php _e('Allow Captcha In Registration Form','supportcandy');?></label>
    <p class="help-block"><?php _e("Enable or disable Captcha in registration form.","wpsc");?></p>
    <select class="form-control wpsc_catpcha_setting" name="wpsc_registration_captcha" id="wpsc_registration_captcha">
      <?php
      $wpsc_registration_captcha = get_option('wpsc_registration_captcha');
      $selected = $wpsc_registration_captcha == '1' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
      $selected = $wpsc_registration_captcha == '0' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
      ?>
    </select>
  </div>
	
  	<div class="form-group">
		<label for="wpsc_login_captcha"><?php _e('Allow Captcha In Login Form','supportcandy');?></label>
		<p class="help-block"><?php _e("Enable or disable Captcha in Login form.","wpsc");?></p>
		<select class="form-control wpsc_catpcha_setting" name="wpsc_login_captcha" id="wpsc_login_captcha">
			<?php
			$wpsc_login_captcha = get_option('wpsc_login_captcha');
			$selected = $wpsc_login_captcha == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_login_captcha == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select>
	</div>

	<div class="form-group" id="wpsc_recaptcha_types" style="display:none;">
		<label for="wpsc_recaptcha_type"><?php _e('Recaptcha Type','supportcandy');?></label>
		<p class="help-block"><?php _e("Select the captcha type for Supportcandy on create ticket page.","supportcandy");?></p>
		<div class="form-group" id="google_captcha_v2_notice" style="display:none;">
			<span><strong> <?php _e('Please Use Google Recaptcha Version 2.0.','supportcandy')?> </strong>  <?php echo sprintf(__('%1$s for steps to get Site Key and Secret Key for Google Recaptcha.','supportcandy'),'<a href="https://supportcandy.net/knowledgebase/captcha/" target="_blank">Click here</a>')?></span>
		</div>
		<select class="form-control" name="wpsc_recaptcha_type" id="wpsc_recaptcha_type">
			<?php
			$wpsc_recaptcha_type = get_option('wpsc_recaptcha_type');
			$selected = $wpsc_recaptcha_type == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('SupportCandy Captcha','supportcandy').'</option>';
			$selected = $wpsc_recaptcha_type == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Google reCaptcha','supportcandy').'</option>';
			?>
		</select>
	</div>
	
	<div class="form-group" id="wpsc_get_site_key" style="display:none;">
		<label for="wpsc_recaptcha_type"><?php _e('Enter Site Key','supportcandy');?></label>
		<input type="text" class="form-control" name="wpsc_get_site_key" id="wpsc_get_site_key" value="<?php echo get_option('wpsc_get_site_key'); ?>">
	</div>
	
	<div class="form-group" id="wpsc_get_secret_key" style="display:none;">
		<label for="wpsc_get_secret_key"><?php _e('Enter Secret Key','supportcandy');?></label>
		<input type="text" class="form-control" name="wpsc_get_secret_key" id="wpsc_get_secret_key" value="<?php echo get_option('wpsc_get_secret_key'); ?>">
	</div>
	
  <button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
  <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
  <input type="hidden" name="action" value="wpsc_settings" />
  <input type="hidden" name="setting_action" value="set_captcha_settings" />
</form>
<script>

jQuery(document).ready(function() {	   
	 <?php
	 if( $wpsc_recaptcha_type == '0' ){
		 ?>
	 	jQuery('#wpsc_get_site_key').show();
	 	jQuery('#wpsc_get_secret_key').show();
		jQuery('#google_captcha_v2_notice').show();
		 <?php
	 }
	 ?>
	 <?php 
	  if($wpsc_captcha == '1' || $wpsc_registration_captcha == '1' || $wpsc_login_captcha =='1'){ ?>
	    jQuery('#wpsc_recaptcha_types').show();
		wpsc_captcha_type = jQuery('#wpsc_recaptcha_type').val();
		if(!wpsc_captcha_type){
			jQuery('#wpsc_get_site_key').show();
			jQuery('#wpsc_get_secret_key').show();
			jQuery('#google_captcha_v2_notice').show();
		}
	  <?php
	  }else{?>
		jQuery('#wpsc_recaptcha_types').hide();
		jQuery('#wpsc_get_site_key').hide();
		jQuery('#wpsc_get_secret_key').hide();
		jQuery('#google_captcha_v2_notice').hide();
		<?php
	  }
	 ?>
	 
	 jQuery(".wpsc_catpcha_setting").change(function(){
		wpsc_captcha = parseInt(jQuery('#wpsc_captcha').val());
		wpsc_reg_captcha = parseInt(jQuery("#wpsc_registration_captcha").val());
		wpsc_login_captcha = parseInt(jQuery("#wpsc_login_captcha").val());
		if( wpsc_captcha || wpsc_reg_captcha || wpsc_login_captcha ){
			jQuery('#wpsc_recaptcha_types').show();
			wpsc_captcha_type = parseInt(jQuery('#wpsc_recaptcha_type').val());
			if(!wpsc_captcha_type){
				jQuery('#wpsc_get_site_key').show();
				jQuery('#wpsc_get_secret_key').show();
				jQuery('#google_captcha_v2_notice').show();
			}
		}else{
			jQuery('#wpsc_recaptcha_types').hide();
			jQuery('#wpsc_get_site_key').hide();
		 	jQuery('#wpsc_get_secret_key').hide();
			jQuery('#google_captcha_v2_notice').hide();
		}
	 });

	 jQuery('#wpsc_recaptcha_type').change(function() {
		 if(this.value==0){			 
		 	jQuery('#wpsc_get_site_key').show();
		 	jQuery('#wpsc_get_secret_key').show();
			jQuery('#google_captcha_v2_notice').show();
		 }else {
		 	jQuery('#wpsc_get_site_key').hide();
		 	jQuery('#wpsc_get_secret_key').hide();
			jQuery('#google_captcha_v2_notice').hide();
		 }
	 });
});
	
function wpsc_set_captcha_settings(){
	wpsc_captcha = parseInt(jQuery('#wpsc_captcha').val());
	wpsc_reg_captcha = parseInt(jQuery("#wpsc_registration_captcha").val());
  if(jQuery("#wpsc_recaptcha_type").val()=='0' && (wpsc_captcha || wpsc_reg_captcha)){
    if(!jQuery("input[name=wpsc_get_site_key]").val()){
      alert("<?php _e('Enter site key','supportcandy')?>");
      return;
    }
    if(!jQuery("input[name=wpsc_get_secret_key]").val()){
      alert("<?php _e('Enter secret key','supportcandy')?>");
      return;
    }
  }
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_frm_captcha_settings')[0]);  
  jQuery.ajax({
    url: wpsc_admin.ajax_url,
    type: 'POST',
    data: dataform,
    processData: false,
    contentType: false
  })
  .done(function (response_str) {
    var response = JSON.parse(response_str);
    jQuery('.wpsc_submit_wait').hide();
    if (response.sucess_status=='1') {
      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    }
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });  
}
</script>