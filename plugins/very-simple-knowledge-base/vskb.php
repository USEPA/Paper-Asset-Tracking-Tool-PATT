<?php
/*
 * Plugin Name: Very Simple Knowledge Base
 * Description: This is a lightweight plugin to create a knowledge base. Add the shortcode on a page or use the widget to display your categories and posts.
 * Version: 5.3
 * Author: Guido
 * Author URI: https://www.guido.site
 * License: GNU General Public License v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: very-simple-knowledge-base
 * Domain Path: /translation
 */

// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// load plugin text domain
function vskb_init() {
	load_plugin_textdomain( 'very-simple-knowledge-base', false, dirname( plugin_basename( __FILE__ ) ) . '/translation' );
}
add_action('plugins_loaded', 'vskb_init');
 
// enqueue plugin scripts
function vskb_scripts() {
	wp_enqueue_style('vskb_style', plugins_url('/css/vskb-style.min.css',__FILE__));
}
add_action('wp_enqueue_scripts', 'vskb_scripts');

// the sidebar widget
function register_vskb_widget() {
	register_widget( 'vskb_widget' );
}
add_action( 'widgets_init', 'register_vskb_widget' );

// include files
include 'vskb-shortcodes.php';
include 'vskb-widget.php';
