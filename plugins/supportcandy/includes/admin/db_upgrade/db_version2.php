<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user;

/**
 * Exit if logged in user do not have administrator capabilities
 */
if( !$current_user->has_cap('edit_published_posts') ) exit();

?>

<div class="bootstrap-iso">
  
    <div class="row" style="margin-top:20px;">
      <div id="wpsc_upgrade_dialog_container" class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2" style="background-color:#fff;text-align:center;padding-bottom:20px;">
        <h3>Database Upgrade Required</h3>
        <p class="help-block" style="font-size:15px">Database upgrade is required in your installation for SupportCandy (not related to database tables other than SupportCandy). This is mandatory process and you will not see ticket list until you complete this. This may take few minutes, please do not press "Back" or "Refresh" button until it finishes.</p>
        <button type="button" class="btn btn-sm btn-primary" onclick="wpsc_db_upgrade_version2()">Upgrade Now</button>
      </div>
    </div>
  
</div>

<script type="text/javascript">
  
  function wpsc_db_upgrade_version2(){
    var replace_html_str = '<p class="help-block" style="font-size:15px">This may take few minutes, please do not press "Back" or "Refresh" button until it finishes.</p>'
                          + '<div class="progress" style="position: initial !important; height:20px !important; width:100% !important;">'
                            +  '<div id="wpsc_db_upgrade_progress_bar" class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>'
                          + '</div>';
    jQuery('#wpsc_upgrade_dialog_container').html(replace_html_str);
    wpsc_run_db_upgrade(1);
  }
  
  /**
   * Run database upgrade
   */
  function wpsc_run_db_upgrade( page_no ) {
    var data = {
      action: 'wpsc_run_db_v2_upgrade',
      page_no : page_no
    };
    jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
      var response = JSON.parse(response_str);
      jQuery('#wpsc_db_upgrade_progress_bar').css('width',response.completed+'%');
      if(response.is_next===1){
        wpsc_run_db_upgrade( response.page_no );
      } else {
        window.location.reload();
      }
    });
  }
  
</script>