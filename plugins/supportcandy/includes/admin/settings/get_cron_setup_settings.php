<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}
?>

<h4 style="margin-bottom: 10px;">
	<?php _e('Cron Setting','supportcandy');?>
</h4>
<form id="wpsc_set_cron_setup_settings" method="post" action="javascript:wpsc_set_cron_setup_settings();">
	<?php $wpscfunction->show_cron_status(); ?>
	<div class="form-group" style="margin-top:30px">
		<label for="wpsc_start_job"><?php _e('Cron Method','supportcandy');?></label>
			<div class="row">
					<div class="col-sm-12" style="margin-bottom:10px; display:flex;">
							<?php
			 				$cron_job_schedule = get_option('wpsc_cron_job_schedule_setup');
			 				$checked = $cron_job_schedule == 1 ? 'checked="checked"' : '';
		 				 ?>
		 					<div style="width:25px;"><input <?php echo $checked?> type="radio" name="wpsc_cron_job_schedule_setup"  value="1" /></div>
							<div style="padding-top:3px;"><?php _e('WP Cron (Wordpress Cron)','supportcandy')?></div>
					</div>
					<div class="col-sm-12"  style="margin-bottom:10px; display:flex;">
						<?php
		 				$checked = $cron_job_schedule == 0 ? 'checked="checked"' : '';
		 				?>
	 					<div style="width:25px;"><input <?php echo $checked?> type="radio" name="wpsc_cron_job_schedule_setup" value="0" /></div>
						<div style="padding-top:3px;"><?php _e('External','supportcandy')?><br />
								<?php
							 	$cron_path = plugins_url('/supportcandy/asset/lib/cron.php');
							 	?>
								<?php _e('<i>Set cron in your hosting control panel to execute below command every minute -</i>','supportcandy');?></br>
								<strong><?php echo 'wget -q -O - '.$cron_path ?></strong>
						</div>
						</br>
					</div>
			</div>
  </div>
 
	<button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
	<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
	<input type="hidden" name="action" value="wpsc_settings" />
	<input type="hidden" name="setting_action" value="set_cron_setup_settings" />
</form>
