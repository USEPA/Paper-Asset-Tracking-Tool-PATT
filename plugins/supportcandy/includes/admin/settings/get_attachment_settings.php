<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}
$wpsc_allow_attach_create_ticket = get_option('wpsc_allow_attach_create_ticket');
$wpsc_allow_attach_reply_form 	 = get_option('wpsc_allow_attach_reply_form');
$wpsc_allow_attachment_type 	 = get_option('wpsc_allow_attachment_type');
?>
<form id="wpsc_frm_attachment_settings" method="post" action="javascript:wpsc_set_attachment_settings();">
    <div class="form-group">
        <label for="wpsc_attachment_max_filesize"><?php _e('Attachment max filesize(MB)','supportcandy');?></label>
        <p class="help-block"><?php _e("Maximum attachment size of file to be able to attach for attachment fields.","supportcandy");?></p>
        <input type="number" min="1" class="form-control" name="wpsc_attachment_max_filesize" id="wpsc_attachment_max_filesize" value="<?php echo get_option('wpsc_attachment_max_filesize');?>" />
    </div>

    <div class="form-group">
	     <label for="wpsc_allow_attach"><?php _e('Allow Attachment','supportcandy');?></label>
	     <p class="help-block"><?php _e("Create ticket form","supportcandy");?></p>
	      <div class="row">
	  	    <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
	  	       <?php $checked = in_array('agents', $wpsc_allow_attach_create_ticket) ? 'checked="checked"' : '';?>
	  		   <div style="width:25px;"><input <?php echo $checked ?> type="checkbox" name="wpsc_allow_attach_create_ticket[]" value="agents" /></div>
	           <div style="padding-top:3px;"><?php _e('Agent','supportcandy') ?></div>
		   </div>
		    <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
		      <?php $checked = in_array('customers', $wpsc_allow_attach_create_ticket) ? 'checked="checked"' : '';?>
              <div style="width:25px;"><input <?php echo $checked ?> type="checkbox" name="wpsc_allow_attach_create_ticket[]" value="customers" /></div>
              <div style="padding-top:3px;"><?php _e('Customer','supportcandy') ?></div>
	       </div>
	
	       <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
		       <?php $checked = in_array('guests', $wpsc_allow_attach_create_ticket) ? 'checked="checked"' : '';?>
               <div style="width:25px;"><input <?php echo $checked ?> type="checkbox" name="wpsc_allow_attach_create_ticket[]" value="guests" /></div>
	           <div style="padding-top:3px;"><?php _e('Guest','supportcandy') ?></div>
          </div>

       <p class="help-block"><?php _e("Reply form","supportcandy");?></p>
	      <div class="row">
	        <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
	            <?php $checked = in_array('agents', $wpsc_allow_attach_reply_form) ? 'checked="checked"' : '';?>
	            <div style="width:25px;"><input <?php echo $checked ?> type="checkbox" name="wpsc_allow_attach_reply_form[]" value="agents" /></div>
	            <div style="padding-top:3px;"><?php _e('Agent','supportcandy') ?></div>
	        </div>
	        <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
	              <?php $checked = in_array('customers', $wpsc_allow_attach_reply_form) ? 'checked="checked"' : '';?>
                  <div style="width:25px;"><input <?php echo $checked ?> type="checkbox" name="wpsc_allow_attach_reply_form[]" value="customers" /></div>
                  <div style="padding-top:3px;"><?php _e('Customer','supportcandy') ?></div>
	        </div>
	        <div class="col-sm-4" style="margin-bottom:10px; display:flex;">  
	             <?php $checked = in_array('guests', $wpsc_allow_attach_reply_form) ? 'checked="checked"' : '';?>
                 <div style="width:25px;"><input <?php echo $checked ?> type="checkbox" name="wpsc_allow_attach_reply_form[]" value="guests" /></div>
                 <div style="padding-top:3px;"><?php _e('Guest','supportcandy') ?></div>
		   </div>
		 </div>
	</div>	
	  
        <div class="form-group">
			<label for="wpsc_allow_attachment_type"><?php _e('Allow attachment type','supportcandy');?></label>
			<p class="help-block"><?php _e("Please enter comma separated extension. e.g. jpg, png, zip","supportcandy");?></p>
			    <input type="text" class="form-control" name="wpsc_allow_attachment_type" id="wpsc_allow_attachment_type" value="<?php echo $wpsc_allow_attachment_type; ?>" />
		</div>

       <div class="form-group">
		   <label for="wpsc_image_type"><?php _e('Image Attachment','supportcandy');?></label>
		   <p class="help-block"><?php _e("Set image attachment behaviour in the ticket.","supportcandy");?></p>
		   <select class="form-control" name="wpsc_image_download_method" id="wpsc_image_download_method">
			    <?php
			    $wpsc_image_download_method  = get_option('wpsc_image_download_method');
			    $selected = $wpsc_image_download_method == '1' ? 'selected="selected"' : '';
			    echo '<option '.$selected.' value="1">'.__('Show in Browser','supportcandy').'</option>';
			    $selected = $wpsc_image_download_method == '0' ? 'selected="selected"' : '';
			    echo '<option '.$selected.' value="0">'.__('Download','supportcandy').'</option>';
			    ?>
		   </select>
       </div>   
	
	   	<div class="form-group">
			<label for="wpsc_show_attachment_notice"><?php _e('Show file attachment notice','supportcandy');?></label>
			<p class="help-block"><?php _e("Enable/Disable file attachment notice","supportcandy");?></p>
			<select class="form-control" name="wpsc_show_attachment_notice" id="wpsc_show_attachment_notice">
				<?php
				$wpsc_show_attachment_notice  = get_option('wpsc_show_attachment_notice');

				$selected = $wpsc_show_attachment_notice == '1' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';

				$selected = $wpsc_show_attachment_notice == '0' ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';

				?>
			</select>
		   
		    <div id="attachment_notice" style="display:none;">
               <label style="margin-top:10px"><?php _e('Attachment notice','supportcandy');?></label>
			     <?php 
                 $wpsc_attachment_notice = get_option('wpsc_attachment_notice');
			     ?>
                <input type="text" class="form-control" name="wpsc_attachment_notice" id="wpsc_attachment_notice" value= "<?php echo $wpsc_attachment_notice; ?>"><br>
	     	</div>
		</div>

      <button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
      <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
      <input type="hidden" name="action" value="wpsc_settings" />
      <input type="hidden" name="setting_action" value="set_attachment_settings" />
    </form>

<script>
  jQuery(document).ready(function() {	

      <?php 
	   if($wpsc_show_attachment_notice == 1){
           ?>
		   jQuery('#attachment_notice').show();
           <?php
		}
		if($wpsc_show_attachment_notice==0)
        {
	        ?>
			jQuery('#attachment_notice').hide();
            <?php
        }
        ?>

        jQuery('#wpsc_show_attachment_notice').change(function() {
		    if(this.value=='1'){			 
			    jQuery('#attachment_notice').show();
		    }else {
		 		jQuery('#attachment_notice').hide(); 
		    }
	   });
	});
</script>