<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$post_id = $_POST['post_id'];
if (!$post_id) {exit;}
$content_post = get_post($post_id);
$content = $content_post->post_content;
echo wpautop($content);
?>