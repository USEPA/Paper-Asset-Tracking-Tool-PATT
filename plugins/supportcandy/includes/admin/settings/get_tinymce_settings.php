<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}
?>
<form id="wpsc_get_tinymce_settings" method="post" action="javascript:wpsc_set_tinymce_settings();">
    <div class="form-group">
        <label for="wpsc_allow_rich_text_editor"><?php _e('Allow rich text editor','supportcandy'); ?></label>
	    <p class="help-block"><?php _e("Only selected roles can use rich text editor.","supportcandy"); ?></p>
	    <div class="row">
            <?php $wpsc_allow_rich_text_editor = get_option('wpsc_allow_rich_text_editor'); ?>
            
            <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
			    <?php $checked = in_array('register_user', $wpsc_allow_rich_text_editor) ? 'checked="checked"' : '';?>
			    <div style="width:25px;"><input <?php  echo $checked ?> type="checkbox" name="wpsc_allow_rte[]" value="register_user" /></div>
			    <div style="padding-top:3px;"><?php _e('Registered Users','supportcandy') ?></div>
            </div>

			<div class="col-sm-4" style="margin-bottom:10px; display:flex;">
                <?php $checked = in_array('guest_user', $wpsc_allow_rich_text_editor) ? 'checked="checked"' : '';?>
			    <div style="width:25px;"><input <?php  echo $checked ?> type="checkbox" name="wpsc_allow_rte[]" value="guest_user" /></div>
				<div style="padding-top:3px;"><?php _e('Guest','supportcandy') ?></div>
            </div>

        </div>
    </div>

    <div class="form-group">
		<?php 
		$wpsc_tinymce_toolbar = get_option('wpsc_tinymce_toolbar');
		$wpsc_tinymce_toolbar_active = get_option('wpsc_tinymce_toolbar_active');
		?>
		<label for="wpsc_tinymce_toolbar"><?php _e('Tinymce toolbar','supportcandy');?></label>
		<div class="row">
			<?php foreach ($wpsc_tinymce_toolbar as $key=> $val) {?>
			<div class="col-sm-4" style="margin-bottom:10px; display:flex;">
				<?php $checked = in_array($key, $wpsc_tinymce_toolbar_active) ? 'checked="checked"' : '';?>
				<div style="width:25px;"><input <?php echo $checked ?> type="checkbox" name="wpsc_tinymce_toolbar[]" value= <?php echo $key?> /></div>
				<div style="padding-top:3px;"><?php echo $val['name'] ?></div>
			</div>
			<?php } ?>
		</div>
	</div>

	<div class="form-group">
		<label for="wpsc_html_pasting"><?php _e('Allow direct HTML pasting','supportcandy');?></label>
		<p class="help-block"><?php _e("Enable/Disable HTML pasting","supportcandy");?></p>
		<select class="form-control" name="wpsc_html_pasting" id="wpsc_html_pasting">
			<?php
			$wpsc_allow_html_pasting = get_option('wpsc_allow_html_pasting');
			$selected = $wpsc_allow_html_pasting == '1' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
			$selected = $wpsc_allow_html_pasting == '0' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
			?>
		</select>
	</div>
    
    <button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
    <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
    <input type="hidden" name="action" value="wpsc_settings" />
    <input type="hidden" name="setting_action" value="set_tinymce_settings" />

</form>