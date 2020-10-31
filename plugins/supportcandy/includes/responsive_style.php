<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

?>

<script>
var bootstrap_between_768_992  = '<?php echo '<link href="'.WPSC_PLUGIN_URL.'asset/css/responsive/bootstrap-between-768-992.css?version='.WPSC_VERSION.'" rel="stylesheet">'?>';
var bootstrap_between_992_1200 = '<?php echo '<link href="'.WPSC_PLUGIN_URL.'asset/css/responsive/bootstrap-between-992-1200.css?version='.WPSC_VERSION.'" rel="stylesheet">'?>';
var bootstrap_max_width_767    = '<?php echo '<link href="'.WPSC_PLUGIN_URL.'asset/css/responsive/bootstrap-max-width-767.css?version='.WPSC_VERSION.'" rel="stylesheet">'?>';
var bootstrap_min_width_768    = '<?php echo '<link href="'.WPSC_PLUGIN_URL.'asset/css/responsive/bootstrap-min-width-768.css?version='.WPSC_VERSION.'" rel="stylesheet">'?>';
var bootstrap_min_width_992    = '<?php echo '<link href="'.WPSC_PLUGIN_URL.'asset/css/responsive/bootstrap-min-width-992.css?version='.WPSC_VERSION.'" rel="stylesheet">'?>';
var bootstrap_min_width_1200   = '<?php echo '<link href="'.WPSC_PLUGIN_URL.'asset/css/responsive/bootstrap-min-width-1200.css?version='.WPSC_VERSION.'" rel="stylesheet">'?>';

jQuery(document).ready(function(){
  wpsc_apply_responsive_bootstrap();
});

function wpsc_apply_responsive_bootstrap(){
  
  if (jQuery('.bootstrap-iso').length > 0) {
    
    var wpsc_width = jQuery('.bootstrap-iso').width();
    
    /* @media screen and (max-width: 767px) */
    if( wpsc_width < 768 ){
      jQuery('html').append(bootstrap_max_width_767);
    }
    
    /* @media (min-width: 768px) */
    if( wpsc_width >= 768 ){
      jQuery('html').append(bootstrap_min_width_768);
    }
    
    /* @media (min-width: 768px) and (max-width: 991px) */
    if( wpsc_width >= 768 && wpsc_width < 992 ){
      jQuery('html').append(bootstrap_between_768_992);
    }
    
    /* @media (min-width: 992px) */
    if( wpsc_width >= 992 ){
      jQuery('html').append(bootstrap_min_width_992);
    }
    
    /* @media (min-width: 992px) and (max-width: 1199px) */
    if( wpsc_width >= 992 && wpsc_width < 1200 ){
      jQuery('html').append(bootstrap_between_992_1200);
    }
    
    /* @media (min-width: 1200px) */
    if( wpsc_width >= 1200 ){
      jQuery('html').append(bootstrap_min_width_1200);
    }
    
  }
}
</script>
