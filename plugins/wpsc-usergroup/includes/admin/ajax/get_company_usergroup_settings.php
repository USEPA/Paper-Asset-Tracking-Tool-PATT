<?php
if ( ! defined( 'ABSPATH' ) )
      exit; // Exit if accessed directly
?>

<div>
	<ul class="nav nav-tabs">
		<li role="presentation" class="tab active" onclick="wpsc_change_tab(this,'add_usergroup_settings');"><a href="#"><?php _e('Usergroup','wpsc-usergroup');?></a></li>
		<li role="presentation" class="tab" onclick="wpsc_change_tab(this,'wpsc_other_settings');"><a href="#"><?php _e('Other Settings','wpsc-usergroup');?></a></li>
	</ul>
</div>

<?php 
$usergroups = get_terms([
	'taxonomy'   => 'wpsc_usergroup_data',
	'hide_empty' => false,
  'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC'
]);

$user_group_term = get_term_by('slug','usergroup','wpsc_ticket_custom_fields');
$wpsc_usergroup_label = get_term_meta($user_group_term->term_id,'wpsc_tf_label',true);


$wpsc_usergroup_change_category = get_option('wpsc_allow_usergroup_change_category','1');
?>

<div id="add_usergroup_settings" class="tab_content visible" style="margin-top:20px;">
  
  <button type="button" style="margin-bottom:6px; float:right;" class="btn btn-success" id="wpsc_add_new_company_btn" onclick="wpsc_add_new_company_settings();"><?php _e('+ Add New','wpsc-usergroup');?></button>

    <form method="post" action="">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th><?php _e('Sr. No','wpsc-usergroup');?></th>
            <th><?php _e('Usergroup Name','wpsc-usergroup');?></th>
            <th><?php _e('Supervisor','wpsc-usergroup');?></th>
            <th style="width: 100px;"><?php _e('Action','wpsc-usergroup');?></th>
          </tr>
        </thead>

        <?php
        if($usergroups){
          $count=0;
          
          foreach ($usergroups as $usergroup){
            $wpsc_usergroup_supervisor = get_term_meta( $usergroup->term_id, 'wpsc_usergroup_supervisor_id');
            $supervisor                = implode(",",$wpsc_usergroup_supervisor);
            $supervisor_explode        = explode(",",$supervisor);
            $name                      = array();
            
            if($wpsc_usergroup_supervisor){
              foreach($supervisor_explode as $value){
                $user_obj = get_user_by('id', $value);
                $name[]   = $user_obj->display_name;
              }
            }
            
            $supervisor_name = implode(",",$name);
            ++$count;
            ?>
            <tr>
              <td><?php echo $count;?></td>
              <td><?php echo $usergroup->name; ?></td>
              <td><?php echo $supervisor_name; ?></td>
              <td>
                <span class="dashicons dashicons-edit wpsp_pointer" onclick="wpsc_edit_usergroup(<?php echo $usergroup->term_id; ?>);"></span>
                <span class="dashicons dashicons-trash wpsp_pointer" onclick="wpsc_delete_usergroup(<?php echo $usergroup->term_id; ?>);"></span>
              </td>
            </tr>
            <?php
          }
        }else{
          echo '<tr><td colspan="4">No records found.</td></tr>';
        }
        ?>
      </table>
    </form>
</div>

<div id="wpsc_other_settings" class="tab_content hidden" style="margin-top:20px;">
  <form id="wpsc_usergroup_other_settings" method="post" action="javascript:wpsc_ug_set_other_settings()">
    <div class="form-group">
      <label for = "usergroup_label_text"><?php _e('Usergroup field label','wpsc-usergroup'); ?></label>
      <p class="help-block"><?php _e('This will change usergroup label text in ticket list and filter.','wpsc-usergroup'); ?></p>
      <input type = "text" class="form-control" name="wpsc_usergroup_label" value = "<?php echo $wpsc_usergroup_label; ?>"/>
    </div>

    <div class="form-group">
      <label for = "usergroup_change_category"><?php _e('Allow user to change category','wpsc-usergroup'); ?></label>
      <p class="help-block"><?php _e('This will change category in ticket list .','wpsc-usergroup_change_category'); ?></p>
      <select class="form-control" name="wpsc_usergroup_change_category" id = "wpsc_usergroup_change_category">
      <?php
			$selected = $wpsc_usergroup_change_category == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','usergroup').'</option>';
			$selected = $wpsc_usergroup_change_category == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','usergroup').'</option>';
			?>
    </select>
    </div>

    
    <button type="submit" class="btn btn-success" id="wpsc_other_settings_btn"><?php _e('Save Changes','wpsc-usergroup');?></button>
    <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
    <input type="hidden" name="action" value="wpsc_ug_set_other_settings" />
    
  </form>
</div>
<script>
function wpsc_change_tab(e,content_id){
  jQuery('.tab').removeClass('active');
  jQuery(e).addClass('active');
  jQuery('.tab_content').removeClass('visible').addClass('hidden');
  jQuery('#'+content_id).removeClass('hidden').addClass('visible');
}

function wpsc_ug_set_other_settings(){
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#wpsc_usergroup_other_settings')[0]);
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
    jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
  });
}
</script>