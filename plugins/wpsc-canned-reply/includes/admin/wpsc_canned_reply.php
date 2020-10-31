<?php
if (!defined('ABSPATH')) exit;

global $wpdb, $post;

$agent_visibility = get_post_meta( $post->ID, 'wpsc_agent_visibility', TRUE );
?>
<input type="checkbox" <?php echo $agent_visibility ? 'checked="checked"':''?> name="agent_visibility" value="1"> <?php _e( 'Visible All Agents', 'wpsc-canned-reply' )?>
