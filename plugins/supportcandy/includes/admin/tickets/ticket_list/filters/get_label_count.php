<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb, $current_user, $wpscfunction;

$filter_count_flag = true;
$label_counts = array();
if (!$current_user->ID || ( $current_user->ID && !$current_user->has_cap('wpsc_agent') ) ){
	$filter_count_flag = false;
}

if($filter_count_flag):
	
	// Get user meta history
	$label_count_history      = get_option( 'wpsc_label_count_history' );
	$label_count_last_history = get_user_meta( $current_user->ID, 'wpsc_'.get_current_blog_id().'_label_count_last_history', true );

	if( $label_count_history && $label_count_history == $label_count_last_history ){
		$label_counts  = get_user_meta( $current_user->ID, 'wpsc_'.get_current_blog_id().'_label_counts', true );
		$filter_count_flag = false;
	}
	
	if($filter_count_flag):
		$all_labels = $wpscfunction->get_ticket_filter_labels();

		// Initialize meta query
		$meta_query = array(
			'relation' => 'AND',
		);

		if ( !is_multisite() || !is_super_admin($current_user->ID)) {
			
				// Initialie restrictions. Everyone should able to see their own tickets.
				$restrict_rules = array(
				  'relation' => 'OR',
				  array(
				    'key'            => 'customer_email',
				    'value'          => $current_user->user_email,
				    'compare'        => '='
				  ),
				);

				if ($current_user->has_cap('wpsc_agent')) {

				  $agent_permissions = $wpscfunction->get_current_agent_permissions();

				  $agents = get_terms([
				   'taxonomy'   => 'wpsc_agents',
				   'hide_empty' => false,
				   'meta_query' => array(
				     'relation' => 'AND',
				     array(
				       'key'       => 'user_id',
				       'value'     => $current_user->ID,
				       'compare'   => '='
				     )
				   ),
				  ]);

				  if(!$agents) die();
				  $current_agent = $agents[0];
				  
				  if ($agent_permissions['view_unassigned']) {
				    $restrict_rules[] = array(
				      'key'            => 'assigned_agent',
				      'value'          => 0,
				      'compare'        => '='
				    );
				  }

				  if ($agent_permissions['view_assigned_me']) {
				    $restrict_rules[] = array(
				      'key'            => 'assigned_agent',
				      'value'          => $current_agent->term_id,
				      'compare'        => '='
				    );
				  }

				  if ($agent_permissions['view_assigned_others']) {
				    $restrict_rules[] = array(
				      'key'            => 'assigned_agent',
				      'value'          => array(0,$current_agent->term_id),
				      'compare'        => 'NOT IN'
				    );
				  }

				  $restrict_rules = apply_filters('wpsc_tl_agent_restrict_rules',$restrict_rules);

				} else {
				  
				  $restrict_rules = apply_filters('wpsc_tl_customer_restrict_rules',$restrict_rules);
				  
				}

				$meta_query[] = $restrict_rules;
			
		}
		
		$labels = array();

		foreach ($all_labels as $key => $label) {
			  
				if( $label['has_badge'] ){
					
					switch ($key) {
						 
						 case 'unresolved_agent':
							$unresolved_agent  = get_option('wpsc_tl_agent_unresolve_statuses');
							$unresolved_agent_rules = array(
								'relation' => 'OR',
							);
							if($unresolved_agent){
								$unresolved_agent_rules[] = array(
									'key'            => 'ticket_status',
									'value'          => $unresolved_agent,
									'compare'        => 'IN'
								);
							}
							$meta_query[] = apply_filters('wpsc_unresolved_agent_label_count', $unresolved_agent_rules, $key);
							break;
				
					  default:
					    $meta_query = apply_filters('wpsc_filter_after_label_default', $meta_query, $key );
					    break;
				
					}
					
					$meta_query[] = array(
						'key'     => 'active',
						'value'   => 1,
						'compare' => '='
					);
				 	
					$sql          = $wpscfunction->get_sql_query( 'COUNT(DISTINCT t.id)', $meta_query );
					$ticket_count = $wpdb->get_var($sql);	
					$labels[$key] = $ticket_count;
					
				}
		}

		if(!$label_count_history){
			$label_count_history = 1;
			update_option( 'wpsc_label_count_history', $label_count_history );
		}
		update_user_meta( $current_user->ID, 'wpsc_'.get_current_blog_id().'_label_count_last_history', $label_count_history );
		update_user_meta( $current_user->ID, 'wpsc_'.get_current_blog_id().'_label_counts', $labels );

		$label_counts = $labels;
		
	endif;
	
endif;