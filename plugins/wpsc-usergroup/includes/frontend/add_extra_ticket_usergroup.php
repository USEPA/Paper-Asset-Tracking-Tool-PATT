<?php

if ( ! defined( 'ABSPATH' ) )
 exit; // Exit if accessed directly

global $wpscfunction , $current_user;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}
$groups = $wpscfunction->get_ticket_meta($ticket_id,'usergroups');
?>

<div id="usergroups">
  <div class="form-group">
      <label class="wpsc_ct_field_label" for="wpsc_ticket_et_user"><?php _e('Usergroups','wpsc-usergroup')?> </label>  
      <p class="help-block"> <?php _e('(Optional) Select usergroups so that email notifications of this ticket will get sent to users of all usergroups as raised by user.','wpsc-usergroup')?></p>
      <input class="form-control  wpsc_usergroups ui-autocomplete-input" name="usergroups"  type="text" autocomplete="off" placeholder="<?php _e('Search Usergroup ...','wpsc-usergroup')?>" />
      <ui class="wpsp_filter_display_container"></ui>
  </div>
</div>
<div id="user_groups" class="form-group col-md-12">
  <?php
     foreach ( $groups as $group ) {
      if($group):
        $group_obj = get_term_by('id',$group,'wpsc_usergroup_data');
        ?>
        <div class="form-group wpsp_filter_display_element wpsc_usergroups ">
          <div class="flex-container" style="padding:10px;font-size:1.0em;">
            <?php echo htmlentities($group_obj->name)?><span onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></span>
              <input type="hidden" name="usergroups[]" value="<?php echo htmlentities($group) ?>" />
          </div>
        </div>
      <?php
      endif;
     }
  ?>
</div>

<script>
jQuery(document).ready(function(){
	
	jQuery("input[name='usergroups']").keypress(function(e) {
		//Enter key
		if (e.which == 13) {
			return false;
		}
	});
	
	jQuery( ".wpsc_usergroups" ).autocomplete({
			minLength: 0,
			appendTo: jQuery('.wpsc_usergroups').parent(),
			source: function( request, response ) {
				var term = request.term;
				request = {
					action : 'wpsc_filter_autocomplete_usergroups',
					term : term,
					field : 'usergroups',
				}
				jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
					response(data);
        });
			},
			select: function (event, ui) {
				var html_str = '<li class="wpsp_filter_display_element">'
												+'<div class="flex-container">'
													+'<div class="wpsp_filter_display_text">'
														+ui.item.label
														+'<input type="hidden" name="usergroups[]" value="'+ui.item.flag_val+'">'
													+'</div>'
													+'<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></div>'
												+'</div>'
											+'</li>';
				jQuery('#usergroups .wpsp_filter_display_container').append(html_str);
			  jQuery(this).val(''); return false;
			}
	}).focus(function() {
			jQuery(this).autocomplete("search", "");
	});

});
</script>


