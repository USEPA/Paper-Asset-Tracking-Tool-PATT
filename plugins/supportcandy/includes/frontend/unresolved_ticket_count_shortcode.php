<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
global $wpdb, $current_user, $wpscfunction;
if(!$current_user->has_cap('wpsc_agent')){echo '0';return;}

$count = get_user_meta( $current_user->ID, 'wpsc_'.get_current_blog_id().'_label_counts');
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

  }
  
  $meta_query[] = $restrict_rules;
}

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
$meta_query[] = $unresolved_agent_rules;
$meta_query[] = array(
  'key'     => 'active',
  'value'   => 1,
  'compare' => '='
);
    
$sql          = $wpscfunction->get_sql_query( 'COUNT(DISTINCT t.id)', $meta_query );

$ticket_count = $wpdb->get_var($sql);	
?>
<span id="wpsc_unresolved_ticket_count"><?php echo $ticket_count;?></span>