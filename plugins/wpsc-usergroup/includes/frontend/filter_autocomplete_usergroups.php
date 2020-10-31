<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb, $wpscfunction;
if (!($current_user->ID)) {exit;}

$term       = isset($_REQUEST) && isset($_REQUEST['term']) ? sanitize_text_field($_REQUEST['term']) : '';
$field_slug = isset($_REQUEST) && isset($_REQUEST['field']) ? sanitize_text_field($_REQUEST['field']) : '';

if($field_slug == 'ticket_id'){
	$field_slug = 'id';
}
$output = array();

switch ($field_slug) {
	
	case 'usergroups':
	
      $usergroups = get_terms([
        'taxonomy'   => 'wpsc_usergroup_data',
        'hide_empty' => false,
        'search'     => $term,
      ]);
      
			foreach($usergroups as $groups){
				$output[] = array(
					'label'    => $groups->name,
					'value'    => '',
					'flag_val' => $groups->term_id,
					'slug'     => $field_slug,
				);
			}
			break;
	
  default:
  	break;
				
}


if (!$output) {
  $output[] = array(
		'label' => __('No matching data','wpsc-usergroup'),
		'value' => '',
		'slug'  => '',
	);
}

if ($output) {
	$output = array_unique($output,SORT_REGULAR);
}
echo json_encode($output);
