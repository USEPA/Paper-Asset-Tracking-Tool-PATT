<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user;

/**
 * Exit if logged in user do not have administrator capabilities
 */
if( !$current_user->has_cap('manage_options') ) exit();

$installed_db_version = get_option( 'wpsc_rp_db_version', 1 );

if( $installed_db_version < '2.0' ) {
     $addons_compatibility = array();

      if (class_exists('Support_Candy_SLA') && WPSC_SLA_VERSION < '2.0.6') {
          $addons_compatibility[] = 'SupportCandy - SLA';
      }

     if (class_exists('Support_Candy_SF') && WPSC_SF_VERSION < '2.0.4') {
          $addons_compatibility[] = 'SupportCandy - Satisfaction Survey';
      }

     if( $addons_compatibility ){
      
        ?>
        <div class="bootstrap-iso">
          
            <div class="row" style="margin-top:20px;">
              <div id="wpsc_upgrade_dialog_container" class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2" style="background-color:#fff;text-align:center;padding-bottom:20px;">
                <h3 >Add-On update Required</h3>
                <p class="help-block" style="font-size:15px;text-align:left;">Please upgrade the following add-ons in order to make it compatible with SupportCandy - Reports v2.0.1</p>
                <div style="text-align:left;">
                <?php 
                  echo '<ol><li>' . implode( '</li><li>', $addons_compatibility) . '</li></ol>';
                ?>
              </div>
              </div>
            </div>
          
        </div>
        <?php
      
     } else {

      include WPSC_RP_ABSPATH.'includes/db_upgrade/db_version2.php';
  }

}
