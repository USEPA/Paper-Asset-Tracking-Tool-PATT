<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user,$wpscfunction;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$wpsc_agent_body = get_user_meta($current_user->ID,'wpsc_agent_signature',true);

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

$labels  = $wpscfunction->get_ticket_filter_labels();
$rich_editing = $wpscfunction->rich_editing_status($current_user);
$directionality = $wpscfunction->check_rtl();

ob_start();
?>
<form id="wpsc_frm_agent_setting" method="post">
      <div class="form-group">
         <label class="label label-default" style="font-size:15px;"><?php _e('Signature','supportcandy') ?></label>
      </div>
	  <?php if($rich_editing){?>
			<div class="text-right">
			<button id="visual" class="wpsc-switch-editor wpsc-switch-editor-active" type="button" onclick="wpsc_get_agent_signature_tinymce('wpsc_agent_signature','agent_signature');"><?php _e('Visual','supportcandy');?></button>
			<button id="text" class="wpsc-switch-editor" type="button" onclick="wpsc_get_textarea('wpsc_agent_signature')"><?php _e('Text','supportcandy');?></button>
      		</div>
	  <?php } ?>
      <div class="form-group">
         <textarea class="form-control" name="wpsc_agent_signature" id="wpsc_agent_signature"><?php echo html_entity_decode($wpsc_agent_body) ?></textarea>
      </div>

	 	 <div class="form-group">
		    <label class="label label-default" style="font-size:15px;"><?php _e('Default Filter','supportcandy') ?></label>
		</div>
		<?php //setting to add agent's default filter ?>	 
		<div class="form-group">	
			<select id="wpsc_agent_default_filter" class="form-control wpsc_drop_down" name="wpsc_agent_default_filter">
				<?php
				$blog_id = get_current_blog_id();
				$default_filter = get_user_meta($current_user->ID,$blog_id.'_wpsc_user_default_filter',true);
				
				$saved_filters = get_user_meta($current_user->ID, $blog_id.'_wpsc_filter',true);
				$saved_filters = $saved_filters ? $saved_filters : array();
				foreach ($labels as $key => $val) :
					if($current_user->has_cap('wpsc_agent') && ($val['visibility']=='agent' || $val['visibility']=='both')):
						$selected = $key == $default_filter ? 'selected="selected"' : '';
						?>
						  <option <?php echo $selected?> value="<?php echo $key?>"><?php echo $val['label']?></option>
					  <?php
					endif;
				endforeach;
				
				foreach ($saved_filters as $ckey => $value):
					if(is_numeric($default_filter) || $default_filter == 0 && !is_string($default_filter) ){
						$selected = $value['save_label'] == $saved_filters[$default_filter]['save_label'] ? 'selected="selected"' : '';
					}
					?>
						<option <?php echo $selected?> value="<?php echo $ckey?>"><?php echo $value['save_label']?></option>
					<?php
				endforeach;
				?>
			</select>
		</div>


      <?php do_action('wpsc_get_agent_setting');?>
      
      <input type="hidden" name="action" value="wpsc_support_agents" />
      <input type="hidden" name="setting_action" value="set_agent_setting" />
</form> 

<script>
function wpsc_get_agent_signature_tinymce(selector,body_id){
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
  jQuery('#visual_body').addClass('btn btn-default visual_body');
  jQuery('#text_body').removeClass('btn btn-primary text_body');
  tinymce.remove('#'+selector);
  jQuery('#text').addClass('wpsc-switch-editor-active');
  jQuery('#visual').removeClass('wpsc-switch-editor-active');
}	

tinymce.remove();
tinymce.init({ 
  selector:'#wpsc_agent_signature',
  body_id: 'agent_signature',
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

<?php 
$body = ob_get_clean();

ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"    onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_agent_setting();"><?php _e('Save Settings','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);