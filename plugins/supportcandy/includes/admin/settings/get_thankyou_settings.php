<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_thankyou_html = stripslashes(get_option('wpsc_thankyou_html'));
$wpsc_thankyou_url = get_option('wpsc_thankyou_url');
$rich_editing = $wpscfunction->rich_editing_status($current_user);

$directionality = $wpscfunction->check_rtl();

?>
<form id="wpsc_frm_thankyou_settings" method="post" action="javascript:wpsc_set_thankyou_settings();">
    
    <div class="form-group">
      <label for="wpsc_thankyou_html"><?php _e('Thank you text','supportcandy');?></label>
      <p class="help-block"><?php _e("Text to show on thank you screen.","supportcandy");?></p>
      <?php if($rich_editing){?>
			  <div class="text-right">
				  <button id="visual" class="wpsc-switch-editor wpsc-switch-editor-active" type="button" onclick="wpsc_get_tinymce('wpsc_thankyou_html','thankyou_body');"><?php _e('Visual', 'supportcandy');?></button>
				  <button id="text" class="wpsc-switch-editor" type="button" onclick="wpsc_get_textarea('wpsc_thankyou_html')"><?php _e('Text', 'supportcandy');?></button>
        </div>
      <?php } ?>
			<textarea class="form-control" name="wpsc_thankyou_html" id="wpsc_thankyou_html"><?php echo htmlentities($wpsc_thankyou_html)?></textarea>
    	<div class="row attachment_link">
					<span onclick="wpsc_get_templates(); "><?php _e('Insert Macros','supportcandy') ?></span>
			</div>
	</div>
	
		<div class="form-group">
      <label for="wpsc_thankyou_url"><?php _e('Thank you page redirect url','supportcandy');?></label>
      <p class="help-block"><?php _e("Url to redirect in case you want to show your custom thank you page. Keep this empty to show above default thank you messege.","supportcandy");?></p>
      <input type="text" class="form-control" name="wpsc_thankyou_url" id="wpsc_thankyou_url" value="<?php echo $wpsc_thankyou_url?>" />
    </div>
		
		<?php do_action('wpsc_get_thankyou_settings');?>
		
    <button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
    <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
    <input type="hidden" name="action" value="wpsc_settings" />
    <input type="hidden" name="setting_action" value="set_thankyou_settings" />
    
</form>

<script>
function wpsc_get_tinymce(selector,body_id){
	
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

function wpsc_get_textarea(selector){

  jQuery('#visual_body').removeClass('btn btn-primary visual_body');
  jQuery('#visual_body').addClass('btn btn-default visual_body');
  jQuery('#text_body').addClass('btn btn-primary text_body');
  tinymce.remove('#'+selector);
  jQuery('#text').addClass('wpsc-switch-editor-active');
  jQuery('#visual').removeClass('wpsc-switch-editor-active');
}

tinymce.remove();
tinymce.init({ 
  selector:'#wpsc_thankyou_html',
  body_id: 'thankyou_body',
  directionality : '<?php echo $directionality; ?>',
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
</script>