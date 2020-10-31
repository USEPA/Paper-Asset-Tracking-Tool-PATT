<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

			// category container with title and post count
			$output .= '<ul class="vskb-cat-list"><li class="vskb-cat-name"><a href="'. get_category_link( $cat->cat_ID ) .'" title="'. $cat->name .'" >'. $cat->name .'</a> '. $vskb_count .'</li>';
				// woocommerce product category image
				if ( class_exists( 'woocommerce' ) && ($vskb_atts['woo_image'] == "true") ) {
					$vskb_thumbnail_id_woo = get_term_meta( $cat->cat_ID, 'thumbnail_id', true );
					$vskb_thumbnail_woo = wp_get_attachment_url( $vskb_thumbnail_id_woo );
					if ( $vskb_thumbnail_woo ) :
						$output .= '<img class="vskb-cat-image" src="'.esc_url( $vskb_thumbnail_woo ).'" alt="'. $cat->name .'" />';
					endif;
				}
				// category description
				if ($vskb_atts['description'] == "true") {
					$vskb_description = category_description( $cat->cat_ID );
					if ( !empty( $vskb_description ) ) :
						$output .= '<div class="vskb-cat-description">'. wp_kses_post( $vskb_description ) .'</div>';
					endif;
				}
				// posts query
				$vskb_post_args = array(
					'post_type' => $vskb_atts['post_type'],
					'tax_query' => array(
						array(
							'taxonomy' => $vskb_atts['taxonomy'],
							'field' => 'term_id',
							'terms' => $cat->term_id,
							'include_children' => false
						),
					),
					'posts_per_page' => $vskb_atts['posts_per_page'],
					'order' => $vskb_atts['order'],
					'orderby' => $vskb_atts['orderby']
				);
				$vskb_posts = get_posts( $vskb_post_args );

				foreach( $vskb_posts AS $single_post ) :
					// initialize variables
					$vskb_cats_woo = '';
					$vskb_tags_woo = '';
					$vskb_cats = '';
					$vskb_tags = '';
					// get woocommerce product categories and tags for adding to post title
					if ( class_exists( 'woocommerce' ) && ($vskb_atts['post_type'] == "product") ) {
						$terms_cats_woo = get_the_terms( $single_post->ID, 'product_cat' );
						if ( $terms_cats_woo && ! is_wp_error( $terms_cats_woo ) ) {
							$cats_woo = array();
							foreach($terms_cats_woo as $cat_woo) {
								$cats_woo[] = $cat_woo->slug;
								$vskb_cats_woo = join( " ", $cats_woo );
							}
						}
						$terms_tags_woo = get_the_terms( $single_post->ID, 'product_tag' );
						if ( $terms_tags_woo && ! is_wp_error( $terms_tags_woo ) ) {
							$tags_woo = array();
							foreach($terms_tags_woo as $tag_woo) {
								$tags_woo[] = $tag_woo->slug;
								$vskb_tags_woo = ' '.join( " ", $tags_woo );
							}
						}
					// get post categories and tags for adding to post title
					} else {
						$terms_cats = get_the_terms( $single_post->ID, $vskb_atts['taxonomy'] );
						if ( $terms_cats && ! is_wp_error( $terms_cats ) ) {
							$cats_native = array();
							foreach($terms_cats as $cat_native) {
								$cats_native[] = $cat_native->slug;
								$vskb_cats = join( " ", $cats_native );
							}
						}
						$terms_tags = get_the_tags($single_post->ID);
						if ($terms_tags && ! is_wp_error( $terms_tags ) && ($vskb_atts['taxonomy'] != "post_tag") ) {
							$tags_native = array();
							foreach($terms_tags as $tag_native) {
								$tags_native[] = $tag_native->slug;
								$vskb_tags = ' '.join( " ", $tags_native );
							}
						}
					}
					// post title
					if (get_the_title( $single_post->ID ) == false) {
						$output .= '<li class="vskb-post-name '.$vskb_cats . $vskb_tags. $vskb_cats_woo. $vskb_tags_woo.'">';
						$output .= '<a href="'. get_permalink( $single_post->ID ) .'" rel="bookmark" title="'. esc_attr($vskb_atts['no_title_label']) .'">'. esc_attr($vskb_atts['no_title_label']) .'</a>';
						$output .= '</li>';
					} else {
						$output .= '<li class="vskb-post-name '.$vskb_cats . $vskb_tags. $vskb_cats_woo. $vskb_tags_woo.'">';
						$output .= '<a href="'. get_permalink( $single_post->ID ) .'" rel="bookmark" title="'. get_the_title( $single_post->ID ) .'">'. get_the_title( $single_post->ID ) .'</a>';
						$output .= '</li>';
					}
					// post meta
					if ($vskb_atts['meta'] == "true") {
						$output .= '<li class="vskb-post-meta">';
						$output .= '<span class="vskb-post-meta-date"><a href="'. esc_url( get_permalink( $single_post->ID ) ) .'">' . esc_attr( get_the_date(get_option( 'date_format' ), $single_post->ID) ). '</a></span>';
						$output .= '<span class="vskb-post-meta-sep">'. ' | ' .'</span>';
						$output .= '<span class="vskb-post-meta-author">'.sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_author_posts_url( $single_post->post_author ) ), esc_attr( get_the_author_meta( 'display_name', $single_post->post_author ) ) ).'</span>';
						$output .= '</li>';
					}
				endforeach;
				
				// view all link
				if ($vskb_atts['all_link'] == "true") {
					$output .= '<li class="vskb-all-link">';
					$output .= '<a href="'. get_category_link( $cat->cat_ID ) .'" title="'. $cat->name .'" >'. esc_attr($vskb_atts['all_link_label']) .'</a>';
					$output .= '</li>';
				}
			$output .=  '</ul>';
