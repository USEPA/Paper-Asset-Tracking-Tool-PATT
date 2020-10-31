<?php

if (!defined('ABSPATH'))
    exit;
    // Canned reply post
    			
$labels = array(
    'name'               => _x('Canned Reply','taxonomy general name','wpsc-canned-reply'),
    'singular_name'      => _x('Canned Replys', 'taxonomy general name','wpsc-canned-reply'),
    'menu_name'          => __('Canned Reply', 'wpsc-canned-reply'),
    'add_new'            => __('Add New','wpsc-canned-reply'),
    'all_item'           => __('All Items','wpsc-canned-reply'),
    'add_new_item'       => __('Add New','wpsc-canned-reply'),
    'edit_item'          => __('Edit','wpsc-canned-reply'),
    'new_item'           => __('New','wpsc-canned-reply'),
    'view_item'          => __('View','wpsc-canned-reply'),
    'search_item'        => __('Search','wpsc-canned-reply'),
    'not_found'          => __('No items found','wpsc-canned-reply'),
    'not_found_in_trash' => __('No items found in trash','wpsc-canned-reply'),
    'parent_item_colon'  => __('Parent Item','wpsc-canned-reply')
);

$args = array(
    'labels'             => $labels,
    'public'             => false,
    'has_archive'        => true,
    'publicly_qureyable' => true,
    'query_var'          => true,
		'show_ui'						 => true,
		'menu_position'		   => 25,
		'menu_icon'					 => WPSC_PLUGIN_URL.'asset/images/admin_icon.png',	
    'rewrite'            => true,
    'capability_type'    => 'post',
    'hierarchical'       => false,
    'supports'           => array('title','editor', 'author'),
);
register_post_type( 'wpsc_canned_reply', $args );

// Register categories texonomy
$labels = array(
    'name' 							=> _x('Categories', 'taxonomy general name','wpsc-canned-reply'),
    'singular_name'     => _x('Category', 'taxonomy singular name','wpsc-canned-reply'),
    'search_items'      => __('Search Categories','wpsc-canned-reply'),
    'all_items'         => __('All Categories','wpsc-canned-reply'),
    'parent_item'       => __('Parent Category','wpsc-canned-reply'),
    'parent_item_colon' => __('Parent Category:','wpsc-canned-reply'),
    'edit_item'         => __('Edit Category','wpsc-canned-reply'),
    'update_item'       => __('Update Category','wpsc-canned-reply'),
    'add_new_item'      => __('Add New Category','wpsc-canned-reply'),
    'new_item_name'     => __('New Category Name','wpsc-canned-reply'),
    'menu_name'         => __('Categories','wpsc-canned-reply')
);

$args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => true
);
register_taxonomy( 'wpsc_canned_reply_categories', 'wpsc_canned_reply', $args );
