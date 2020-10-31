<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// shortcode for one column
function vskb_one_column( $vskb_atts ) {
	// shortcode attributes
	$vskb_atts = shortcode_atts(array(
		'class' => 'vskb-one-container',
		'post_type' => 'post',
		'taxonomy' => 'category',
		'include' => '',
		'exclude' => '',
		'hide_empty' => 1,
		'posts_per_page' => -1,
		'order' => 'desc',
		'orderby' => 'date',
		'description' => '',
		'woo_image' => '',
		'count' => '',
		'meta' => '',
		'all_link' => '',
		'all_link_label' => __('View All &raquo;', 'very-simple-knowledge-base'),
		'no_title_label' => __('(no title)', 'very-simple-knowledge-base')
	), $vskb_atts);

	// initialize output
	$output = '';
	// main container
	$output .= '<div id="vskb-one" class="'.$vskb_atts['class'].'">';
		// categories query
		$vskb_cat_args = array(
			'taxonomy' => $vskb_atts['taxonomy'],
			'include' => $vskb_atts['include'],
			'exclude' => $vskb_atts['exclude'],
			'hide_empty' => $vskb_atts['hide_empty']
		);
		$vskb_cats = get_categories( $vskb_cat_args );

		foreach ($vskb_cats as $cat) :
			if ($vskb_atts['count'] == "true") {
				$vskb_count = '<span class="vskb-post-count">('.$cat->category_count.')</span>';
			} else {
				$vskb_count = '';
			}
			// include template
			include 'vskb-template.php';
		endforeach;
	$output .= '</div>';

	// return output
	return $output;
}
add_shortcode('knowledgebase-one', 'vskb_one_column');

// shortcode for two columns
function vskb_two_columns( $vskb_atts ) {
	// shortcode attributes
	$vskb_atts = shortcode_atts(array(
		'class' => 'vskb-two-container',
		'post_type' => 'post',
		'taxonomy' => 'category',
		'include' => '',
		'exclude' => '',
		'hide_empty' => 1,
		'posts_per_page' => -1,
		'order' => 'desc',
		'orderby' => 'date',
		'description' => '',
		'woo_image' => '',
		'count' => '',
		'meta' => '',
		'all_link' => '',
		'all_link_label' => __('View All &raquo;', 'very-simple-knowledge-base'),
		'no_title_label' => __('(no title)', 'very-simple-knowledge-base')
	), $vskb_atts);

	// initialize output
	$output = '';
	// main container
	$output .= '<div id="vskb-two" class="'.$vskb_atts['class'].'">';
		// categories query
		$vskb_cat_args = array(
			'taxonomy' => $vskb_atts['taxonomy'],
			'include' => $vskb_atts['include'],
			'exclude' => $vskb_atts['exclude'],
			'hide_empty' => $vskb_atts['hide_empty']
		);
		$vskb_cats = get_categories( $vskb_cat_args );

		foreach ($vskb_cats as $cat) :
			if ($vskb_atts['count'] == "true") {
				$vskb_count = '<span class="vskb-post-count">('.$cat->category_count.')</span>';
			} else {
				$vskb_count = '';
			}
			// include template
			include 'vskb-template.php';
		endforeach;
	$output .= '</div>';

	// return output
	return $output;
}
add_shortcode('knowledgebase-two', 'vskb_two_columns');

// shortcode for three columns
function vskb_three_columns( $vskb_atts ) {
	// shortcode attributes
	$vskb_atts = shortcode_atts(array(
		'class' => 'vskb-three-container',
		'post_type' => 'post',
		'taxonomy' => 'category',
		'include' => '',
		'exclude' => '',
		'hide_empty' => 1,
		'posts_per_page' => -1,
		'order' => 'desc',
		'orderby' => 'date',
		'description' => '',
		'woo_image' => '',
		'count' => '',
		'meta' => '',
		'all_link' => '',
		'all_link_label' => __('View All &raquo;', 'very-simple-knowledge-base'),
		'no_title_label' => __('(no title)', 'very-simple-knowledge-base')
	), $vskb_atts);

	// initialize output
	$output = '';
	// main container
	$output .= '<div id="vskb-three" class="'.$vskb_atts['class'].'">';
		// categories query
		$vskb_cat_args = array(
			'taxonomy' => $vskb_atts['taxonomy'],
			'include' => $vskb_atts['include'],
			'exclude' => $vskb_atts['exclude'],
			'hide_empty' => $vskb_atts['hide_empty']
		);
		$vskb_cats = get_categories( $vskb_cat_args );

		foreach ($vskb_cats as $cat) :
			if ($vskb_atts['count'] == "true") {
				$vskb_count = '<span class="vskb-post-count">('.$cat->category_count.')</span>';
			} else {
				$vskb_count = '';
			}
			// include template
			include 'vskb-template.php';
		endforeach;
	$output .= '</div>';

	// return output
	return $output;
}
add_shortcode('knowledgebase-three', 'vskb_three_columns');

// shortcode for four columns
function vskb_four_columns( $vskb_atts ) {
	// shortcode attributes
	$vskb_atts = shortcode_atts(array(
		'class' => 'vskb-four-container',
		'post_type' => 'post',
		'taxonomy' => 'category',
		'include' => '',
		'exclude' => '',
		'hide_empty' => 1,
		'posts_per_page' => -1,
		'order' => 'desc',
		'orderby' => 'date',
		'description' => '',
		'woo_image' => '',
		'count' => '',
		'meta' => '',
		'all_link' => '',
		'all_link_label' => __('View All &raquo;', 'very-simple-knowledge-base'),
		'no_title_label' => __('(no title)', 'very-simple-knowledge-base')
	), $vskb_atts);

	// initialize output
	$output = '';
	// main container
	$output .= '<div id="vskb-four" class="'.$vskb_atts['class'].'">';
		// categories query
		$vskb_cat_args = array(
			'taxonomy' => $vskb_atts['taxonomy'],
			'include' => $vskb_atts['include'],
			'exclude' => $vskb_atts['exclude'],
			'hide_empty' => $vskb_atts['hide_empty']
		);
		$vskb_cats = get_categories( $vskb_cat_args );

		foreach ($vskb_cats as $cat) :
			if ($vskb_atts['count'] == "true") {
				$vskb_count = '<span class="vskb-post-count">('.$cat->category_count.')</span>';
			} else {
				$vskb_count = '';
			}
			// include template
			include 'vskb-template.php';
		endforeach;
	$output .= '</div>';

	// return output
	return $output;
}
add_shortcode('knowledgebase', 'vskb_four_columns');
