<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}

$wpsc_atc_warning_text  = stripslashes(get_option('wpsc_atc_warning_text'));
$wpsc_atc_waring_email_age  = get_option('wpsc_atc_waring_email_age','0');

$directionality = $wpscfunction->check_rtl();
?>
<form id="frm_atc_ticket_notification_settings" action="javascript:wpsc_set_atc_ticket_notifications();" method="post">
    <h4><?php _e('Warning Email','wpsc-atc');?></h4>
	<div class="form-group">
		<label><?php _e('No of days before closing ticket','wpsc-atc');?></label>
		<p class="help-block"><?php _e("Warning email to send for selected days before closing ticket. Set '0' to disable warning email.",'wpsc-atc');?></p>
		<input type="number" class="form-control" name="wpsc_atc_waring_email_age" id="wpsc_atc_waring_email_age" value="<?php echo $wpsc_atc_waring_email_age?>" />
	</div>

	<div class="form-group">
		<label for="wpsc_atc_subject"><?php _e('Subject','wpsc-atc');?></label>
		<input type="text" class="form-control" name="wpsc_atc_subject" id="wpsc_atc_subject" value="<?php echo get_option('wpsc_atc_subject')?>" />
	</div>
 
	<div class="form-group">
		<label for="wpsc_atc_email_body"><?php _e('Body','wpsc-atc');?></label>
    <div class="text-right">
			<button id="visual" class="wpsc-switch-editor wpsc-switch-editor-active" type="button" onclick="wpsc_get_atc_tinymce_email('wpsc_atc_email_body','html_body');"><?php _e('Visual','wpsc-atc');?></button>
			<button id="text" class="wpsc-switch-editor" type="button" onclick="wpsc_get_atc_textarea_email('wpsc_atc_email_body')"><?php _e('Text','wpsc-atc');?></button>
		</div>
		<textarea class="form-control" name="wpsc_atc_email_body" id="wpsc_atc_email_body"><?php echo stripslashes(get_option('wpsc_atc_email_body'))?></textarea>
		<div class="row attachment_link">
		   <span onclick="wpsc_get_templates(); "><?php _e('Insert Macros','wpsc') ?></span>
		</div>
	</div>

    <button type="submit" class="btn btn-success" id="wpsc_save_changes_atc_btn"><?php _e('Save Changes','wpsc-atc');?></button>
    <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
    <input type="hidden" name="action" value="wpsc_set_atc_ticket_notifications" />
</form>	

<script>

function wpsc_set_atc_ticket_notifications(){
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#frm_atc_ticket_notification_settings')[0]);
  
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

function wpsc_get_atc_tinymce_email(selector,body_id){
	
  jQuery('#visual_header').addClass('btn btn-primary visual_header');
  jQuery('#text_header').removeClass('btn btn-primary text_header');
  jQuery('#text_header').addClass('btn btn-default text_header');
  jQuery('#text').removeClass('wpsc-switch-editor-active');
  jQuery('#visual').addClass('wpsc-switch-editor-active');
  tinymce.init({ 
    selector:'#'+selector,
    body_id: body_id,
    menubar: false,
    statusbar: false,
    height : '200',
    plugins: [
    'lists link image directionality'
    ],
    image_advtab: true,
    toolbar: 'bold italic underline blockquote | alignleft aligncenter alignright | bullist numlist | rtl | link image',
    branding: false,
    autoresize_bottom_margin: 20,
    browser_spellcheck : true,
    relative_urls : false,
    remove_script_host : false,
    convert_urls : true,
    setup: function (editor) {
    }
  });
}

function wpsc_get_atc_textarea_email(selector){

  jQuery('#visual_body').removeClass('btn btn-primary visual_body');
  jQuery('#visual_body').addClass('btn btn-default visual_body');
  jQuery('#text_body').addClass('btn btn-primary text_body');
  tinymce.remove('#'+selector);
  jQuery('#text').addClass('wpsc-switch-editor-active');
  jQuery('#visual').removeClass('wpsc-switch-editor-active');
}

tinymce.remove();
tinymce.init({ 
  selector:'#wpsc_atc_email_body',
  body_id: 'body',
  directionality : '<?php echo $directionality; ?>',
  menubar: false,
  height : '200',
  plugins: [
      'lists link image directionality'
  ],
  image_advtab: true,
  toolbar: 'bold italic underline blockquote | alignleft aligncenter alignright | bullist numlist | rtl | link image',
  branding: false,
  autoresize_bottom_margin: 20,
  browser_spellcheck : true,
  relative_urls : false,
  remove_script_host : false,
  convert_urls : true,
  setup: function (editor) {
  }
});
</script>