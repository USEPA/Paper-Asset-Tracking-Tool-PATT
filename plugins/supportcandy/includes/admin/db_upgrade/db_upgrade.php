<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user;

/**
 * Exit if logged in user do not have administrator capabilities
 */
if( !$current_user->has_cap('edit_published_posts') ) exit();

$installed_db_version = get_option( 'wpsc_db_version', 1 );

if( $installed_db_version < '2.0' ) {

    $addons_compatibility = array();
    
    if( class_exists('Support_Candy_SLA') && WPSC_SLA_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - SLA';
      
    if( class_exists('WPSC_Conditional_Agent_Assign') && WPSC_CAA_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - Assign Agent Rules';
      
    if( class_exists('Support_Candy_SF') && WPSC_SF_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - Satisfaction Survey';
      
    if( class_exists('WPSC_Email_Piping') && WPSC_EP_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - Email Piping';
      
    if( class_exists('Support_Candy_ULT_FAQ') && WPSC_ULTFAQ_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - FAQ Integration';
      
    if( class_exists('WPSC_Reports') && WPSC_RP_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - Reports';
      
    if( class_exists('Support_Candy_PRASS_KB') && WPSC_PRASS_KB_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - Knowledgebase Integration';
      
    if( class_exists('WPSupportCandyWoocommerce') && WPSC_WOO_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy Woocommerce Add-On';
      
    if( class_exists('WPSC_USERGROUP') && WPSC_USERGROUP_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - Usergroup';
      
    if( class_exists('Support_Candy_ST') && WPSC_ST_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - Schedule Tickets';
      
    if( class_exists('WPSC_Export_Ticket') && WPSC_EXP_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - Export Ticket';
      
    if( class_exists('Support_Candy_ATC') && WPSC_ATC_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - Automatic Close Ticket';
      
    if( class_exists('WPSC_AGENTGROUP') && WPSC_AGENTGROUP_VERSION < 2 )
      $addons_compatibility[] = 'SupportCandy - Agentgroup';
      
    if( $addons_compatibility ){
      
      ?>
      <div class="bootstrap-iso">
        
          <div class="row" style="margin-top:20px;">
            <div id="wpsc_upgrade_dialog_container" class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2" style="background-color:#fff;text-align:center;padding-bottom:20px;">
              <h3 >Add-On update Required</h3>
              <p class="help-block" style="font-size:15px;text-align:left;">Please upgrade the following add-ons in order to make it compatible with SupportCandy v2.0</p>
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
      
      include WPSC_ABSPATH.'includes/admin/db_upgrade/db_version2.php';
      
    }

}
