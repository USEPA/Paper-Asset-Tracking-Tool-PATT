<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$agent_role = get_option('wpsc_agent_role');

ob_start();
?>
<div class="form-group">
  <label for="wpsc_priority_name"><?php _e('Select User','supportcandy');?></label>
  <p class="help-block"><?php _e('Select user to whome you want to make an agent.','supportcandy');?></p>
  <input id="wpsc_agent_name" class="form-control" placeholder="<?php _e('Search...','supportcandy');?>" name="wpsc_priority_name" value="" />
  <input type="hidden" id="wpsc_agent_id" name="wpsc_agent_id" value="">
</div>
<div class="form-group">
  <label for="wpsc_priority_color"><?php _e('Select Role','supportcandy');?></label>
  <p class="help-block"><?php _e('Select agent role. You can create new role in Agent Roles section if needed.','supportcandy');?></p>
  <select class="form-control" id="wpsc_agent_role" name="wpsc_agent_role">
    <?php foreach ($agent_role as $key => $value):?>
    	<option value="<?php echo $key?>"><?php echo $value['label']?></option>
    <?php endforeach;?>
  </select>
</div>
<script>
  
  jQuery( function() {
    jQuery( "#wpsc_agent_name" ).on('focus',function(){
        jQuery(this).val('');
        jQuery('#wpsc_agent_id').val('');
    });
		jQuery( "#wpsc_agent_name" ).autocomplete({
      minLength: 0,
      appendTo: jQuery("#wpsc_agent_name").parent(),
      source: function( request, response ) {
        var term = request.term;
        request = {
          action: 'wpsc_support_agents',
          setting_action : 'get_users_add_agent',
          term : term
        }
        jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
          response(data);
        });	
      },
			select: function (event, ui) {
        jQuery('#wpsc_agent_id').val(ui.item.id);
      }
    }).focus(function() {
				jQuery(this).autocomplete("search", "");
		});
  });
</script>

<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_add_support_agent();"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
