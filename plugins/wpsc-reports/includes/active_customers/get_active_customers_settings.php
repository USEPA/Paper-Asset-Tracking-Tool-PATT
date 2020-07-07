<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$current_user,$wpdb;

?>

<div id="wpsc_active_customers_list"></div>

<script>
  jQuery(document).ready(function() {
    get_active_customers_report();
  });

</script>