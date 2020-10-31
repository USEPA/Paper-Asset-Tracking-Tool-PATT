<?php
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly
?>
<br>

<div id="tab_container" style="clear: both;">

    <div class="wpsp_company_setting_div">

        <form id="wpsc_frm_add_new_company_settings" method="post" action="javascript:set_add_new_company_settings();">

            <h3><?php echo __('Add New Usergroup','wpsc-usergroup') ?></h3>
            <hr>

            <table class="table table-striped table-hover">

                <tr>
                    <th><?php _e('Name','wpsc-usergroup');?>:</th>
                    <td><input type="text" id="wpsp_add_usergroup_name" name="wpsp_usergroup_name"></td>
                </tr>
                <tr>
                    <th><?php _e('Add User','wpsc-usergroup');?>:</th>
                    <td>
                      <input id="usergroup_users" class="form-control form-control wpsc_usergroup_users ui-autocomplete-input" name="usergroup_users" type="text" autocomplete="off" placeholder="<?php _e('Search User ...','wpsc-usergroup')?>" />
                      <ui class="wpsp_filter_display_container"></ui>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Usergroup Users','wpsc-usergroup');?>:</th>
                    <td>
                        <table id="wpsp_company_selected_users">
                            <thead>
                                <tr>
                                    <th><?php _e('Name','wpsc-usergroup');?></th>
                                    <th><?php _e('Supervisors','wpsc-usergroup');?></th>
                                    <th style="width: 50px;"><?php _e('Action','wpsc-usergroup');?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Category','wpsc-usergroup');?>:</th>
                    <td>
                      <select id="wpsc_usergroup_category" class="form-control" name="wpsc_usergroup_category">
          			        <option value=""></option>
          							<?php
          							$categories = get_terms([
          							  'taxonomy'   => 'wpsc_categories',
          							  'hide_empty' => false,
          								'orderby'    => 'meta_value_num',
          							  'order'    	 => 'ASC',
          								'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
          							]);
                        foreach ( $categories as $category ) :
          			          echo '<option value="'.$category->term_id.'">'.$category->name.'</option>';
          			        endforeach;
          			        ?>
        			        </select>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="action" value="wpsc_set_add_new_company_settings" />
            <button type="submit" id="wpsc_save_company_btn" class="btn btn-success"><?php _e('Save Changes','wpsc-usergroup');?></button>
            <button class="btn btn-success" onclick="wpsc_back_usergroup_list();"><?php _e('Cancel','wpsc-usergroup');?></button>
            <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function(){
	
  	jQuery( ".wpsc_usergroup_users" ).autocomplete({
  			minLength: 1,
  			appendTo: jQuery('.wpsc_usergroup_users').parent(),
  			source: function( request, response ) {
  				var term = request.term;
  				request = {
  					'action' : 'wpsc_filter_user_autocomplete',
             term    :  term
					}
  				jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
  					response( data );
  				});
  			},
  			select: function (event, ui) {
          var html_str = "<tr><td>"+ ui.item.label + "</td><td><input type='checkbox' name='supervisor_id[]' value='"+ui.item.id+"'>"+
                                "<input type='hidden' name='user_id[]' value='"+ ui.item.id +"'/></td>"+
                        "<td><span class='delete-row'>Remove</span></td></tr>";
  				jQuery("#wpsp_company_selected_users tbody").append(html_str);
  			  jQuery(this).val(''); return false;
  				}		
  	});
  
    jQuery(document).on('click', '.delete-row', function () {
        jQuery(this).closest("tr").remove();

    });
});
</script>