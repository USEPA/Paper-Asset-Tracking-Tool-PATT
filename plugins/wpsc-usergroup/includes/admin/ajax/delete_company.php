<?php
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly
?>
<?php

wp_delete_term($_POST['comp_id'], 'wpsc_usergroup_data');