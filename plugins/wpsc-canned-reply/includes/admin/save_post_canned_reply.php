<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user;

$agent_visibility =  isset($_POST['agent_visibility']) ? 1 : 0;

update_post_meta($_POST['post_ID'],'wpsc_agent_visibility',$agent_visibility);
// update_post_meta($_POST['post_ID'],'wpsc_author',$current_user->ID);
