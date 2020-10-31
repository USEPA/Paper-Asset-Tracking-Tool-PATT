<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user;

/**
 * Exit if logged in user do not have administrator capabilities
 */
if( !$current_user->has_cap('manage_options') ) exit();

?>

<div class="bootstrap-iso">
  
    <div class="row" style="margin-top:20px;">
      <div id="wpsc_rp_upgrade_dialog_container" class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2" style="background-color:#fff;text-align:center;padding-bottom:20px;">
        <h3>Database Upgrade Required</h3>
        <p class="help-block" style="font-size:15px">
          Database upgrade required for SupportCandy Reports. 
          This is related to SupportCandy Reports only and no other database table will get changed. Please do not press "Refresh" or "Back" button until it finishes. It may take few minutes, thank you for your patience.
        </p>
        <button type="button" class="btn btn-sm btn-primary" onclick="wpsc_rp_db_upgrade_version2()"><?php _e('Upgrade Now','wpsc-rp')?></button>
      </div>
    </div>
  
</div>

<script type="text/javascript">
  
  function wpsc_rp_db_upgrade_version2(){
    var replace_html_str = '<p class="help-block" style="font-size:15px">This may take few minutes, please do not press "Back" or "Refresh" button until it finishes.</p>'
                          + '<div class="progress" style="position: initial !important; height:20px !important; width:100% !important;">'
                            +  '<div id="wpsc_db_upgrade_progress_bar" class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>'
                          + '</div>';
    jQuery('#wpsc_rp_upgrade_dialog_container').html(replace_html_str);
    wpsc_rp_run_db_upgrade(1);
  }
  
  /**
   * Run database upgrade
   */
  function wpsc_rp_run_db_upgrade( date ) {
    var data = {
      action: 'wpsc_rp_run_db_v2_upgrade',
      date : date
    };
    jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
      var response = JSON.parse(response_str);
      jQuery('#wpsc_db_upgrade_progress_bar').css('width',response.completed+'%');
      if(response.is_next===1){
        wpsc_rp_run_db_upgrade( response.date );
      } else {
        window.location.reload();
      }
    });
  }
  
</script>