<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;

if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

if (!$key) {exit;}

$options = array();

if(is_numeric($key)){
  
    $custom_field = get_term_by('id', $key, 'wpsc_ticket_custom_fields');
    $field_type   = get_term_meta( $custom_field->term_id, 'wpsc_tf_type', true);

    if($field_type=='0'){
      
        if ($custom_field->slug=='ticket_category') {
          
          $categories = get_terms([
            'taxonomy'   => 'wpsc_categories',
            'hide_empty' => false,
            'orderby'    => 'meta_value_num',
            'order'    	 => 'ASC',
            'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
          ]);
          foreach ( $categories as $category ){
            $options[] = array(
              'value' => $category->term_id,
              'label' => $category->name,
            );
          }
          
        }
        
        if ($custom_field->slug=='ticket_priority') {
          
          $priorities = get_terms([
            'taxonomy'   => 'wpsc_priorities',
            'hide_empty' => false,
            'orderby'    => 'meta_value_num',
            'order'    	 => 'ASC',
            'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
          ]);
          foreach ( $priorities as $priority ){
            $options[] = array(
              'value' => $priority->term_id,
              'label' => $priority->name,
            );
          }
          
        }
        
        if ($custom_field->slug=='ticket_status') {
          
          $statuses = get_terms([
            'taxonomy'   => 'wpsc_statuses',
            'hide_empty' => false,
            'orderby'    => 'meta_value_num',
            'order'    	 => 'ASC',
            'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
          ]);
          foreach ( $statuses as $status ){
            $options[] = array(
              'value' => $status->term_id,
              'label' => $status->name,
            );
          }
          
        }
        
        if ($custom_field->slug=='assigned_agent') {
          
          $agents = get_terms([
          	'taxonomy'   => 'wpsc_agents',
          	'hide_empty' => false,
            'orderby'    => 'meta_value',
            'order'    	 => 'ASC',
            'meta_query' => array('order_clause' => array('key' => 'label')),
          ]);
          foreach ( $agents as $agent ){
            $agent_name = get_term_meta( $agent->term_id, 'label', true );
            $options[] = array(
              'value' => $agent->term_id,
              'label' => $agent_name,
            );
          }
          
        }
        
    } else {
        
				switch( $field_type ){
					
						case 2:
						case 3:
						case 4:
							
								$wpsc_tf_options = get_term_meta( $custom_field->term_id, 'wpsc_tf_options', true);
								foreach ( $wpsc_tf_options as $option ){
									$options[] = array(
										'value' => str_replace('"', "&quot;", stripcslashes($option)),
										'label' => stripcslashes($option),
									);
								}
								break;
							
						default:
							$options = apply_filters( 'wpsc_condition_custom_field_dd_options', $options, $field_type, $custom_field );
					
				}
        
    }
  
}

if( $key == 'agent_created' ){
	$options = array(
		array(
			'value' => 'user',
			'label' => __('User himself','supportcandy'),
		),
		array(
			'value' => 'agent',
			'label' => __('Agent','supportcandy'),
		)
	);
}

if( $key == 'user_type' ){
	$options = array(
		array(
			'value' => 'user',
			'label' => __('Registered User','supportcandy'),
		),
		array(
			'value' => 'guest',
			'label' => __('Guest User','supportcandy'),
		)
	);
}

$options = apply_filters( 'wpsc_condition_dd_options', $options, $key );

