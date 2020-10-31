<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb, $current_user, $wpscfunction;

if (!$current_user->ID){
	die();
}
$filter = $wpscfunction->get_current_filter();

$upload_dir = wp_upload_dir();
$path_to_export = $upload_dir['basedir'].'/wpsc_export_ticket.csv';
$url_to_export  = $upload_dir['baseurl'].'/wpsc_export_ticket.csv';

$filename=$path_to_export;
$fp=fopen($filename,"w");

$field_id=sanitize_text_field($_POST['field_id']);

$field_ids=explode(',', $field_id);

$export_colomn_name = array();
foreach ($field_ids as $field){ 
	
	$label = get_term_by('slug',$field,'wpsc_ticket_custom_fields');
	$c_name = get_term_meta($label->term_id,'wpsc_tf_label',true); 
	$c_name = apply_filters('wpsc_change_set_et_field_name',$c_name,$field);
  $export_colomn_name[] = $c_name;  
}
fputcsv($fp,$export_colomn_name);

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
				'value'          => $agents[0]->term_id,
				'compare'        => '='
			);
		}
		
		if ($agent_permissions['view_assigned_others']) {
			$restrict_rules[] = array(
				'key'            => 'assigned_agent',
				'value'          => array(0,$agents[0]->term_id),
				'compare'        => 'NOT IN'
			);
		}
		
		$restrict_rules = apply_filters('wpsc_tl_agent_restrict_rules',$restrict_rules);
		
	} else {	
		
		$restrict_rules = apply_filters('wpsc_tl_customer_restrict_rules',$restrict_rules);
		
	}

	$meta_query[] = $restrict_rules;
}

// Merge default filter label
if($filter['query']){
	$meta_query = array_merge($meta_query, $filter['query']);
}

// Merge search
$search = '';
$search_query = trim($filter['custom_filter']['s']) ? trim($filter['custom_filter']['s']) : '';
if($search_query){
	$search = $search_query;
}

//delete condition
$active = 1;
if ($filter['label'] == 'deleted') {
	$active = 0;
}

$meta_query[] = array(
	'key'     => 'active',
	'value'   => $active,
	'compare' => '='
);

if ($current_user->has_cap('wpsc_agent')) {
	
	$fields = get_terms([
		'taxonomy'   => 'wpsc_ticket_custom_fields',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'meta_key'	 => 'wpsc_filter_agent_load_order',
		'order'    	 => 'ASC',
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'       => 'wpsc_allow_ticket_filter',
				'value'     => '1',
				'compare'   => '='
			),
			array(
				'key'       => 'wpsc_agent_ticket_filter_status',
				'value'     => '1',
				'compare'   => '='
			)
		),
		]);
	
} else {
	
	$fields = get_terms([
		'taxonomy'   => 'wpsc_ticket_custom_fields',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'meta_key'	 => 'wpsc_filter_customer_load_order',
		'order'    	 => 'ASC',
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'       => 'wpsc_allow_ticket_filter',
				'value'     => '1',
				'compare'   => '='
			),
			array(
				'key'       => 'wpsc_customer_ticket_filter_status',
				'value'     => '1',
				'compare'   => '='
			)
		),
		]);
	
}

foreach ( $fields as $field ){
	
	$label       = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
	$filter_type = get_term_meta( $field->term_id, 'wpsc_ticket_filter_type', true);
	if ($filter_type=='string' || $filter_type=='number') {
		if($field->slug == 'ticket_id'){
			$field->slug = 'id';
		}
		if(isset($filter['custom_filter'][$field->slug])):
			
			$meta_query[] = array(
				'key'     => $field->slug,
				'value'   => $filter['custom_filter'][$field->slug],
				'compare' => 'IN'
			);
		endif;
	}else {
		
		if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
			$meta_query[] = array(
				'key'     => $field->slug,
				'value'   => array( 
					get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['from'])),
					get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['to'])),
				),
				'compare' => 'BETWEEN',
				'type' => 'datetime',
			);
		}
	}
}

$select_str = 'DISTINCT t.*';

$sql = $wpscfunction->get_sql_query( $select_str, $meta_query,$search);

$tickets = $wpdb->get_results($sql);
$tickets_list = json_decode(json_encode($tickets), true);

foreach($tickets_list as $ticket){

	$export_colomn_value = array();
	$result= $ticket['id'];
	
	foreach ($field_ids as  $value) {
		if($value =='ticket_id'){
			$value     = 'id';
			$ticket_id = $ticket['id'];
			$export_colomn_value[]=$ticket_id;
		}
		elseif($value=='ticket_status'){
			$ticket_status    = $wpscfunction->get_ticket_fields($ticket['id'],'ticket_status');
			$status           = get_term_by('id',$ticket_status,'wpsc_statuses');      
			$export_colomn_value[] = $status->name;
		}
		elseif($value=='ticket_subject'){
			$ticket_subject  = $wpscfunction->get_ticket_fields($ticket['id'],'ticket_subject');
			$export_colomn_value[]= $ticket_subject;
		}
		elseif($value=='customer_name'){
			$customer_name   = $wpscfunction->get_ticket_fields($ticket['id'],'customer_name');
			$export_colomn_value[] = $customer_name;
		}
		elseif($value=='customer_email'){
			$customer_name   = $wpscfunction->get_ticket_fields($ticket['id'],'customer_email');
			$export_colomn_value[] = $customer_name;
		}
		elseif($value=='ticket_category'){
			$ticket_category = $wpscfunction->get_ticket_fields($ticket['id'],'ticket_category');
			$category = get_term_by('id',$ticket_category,'wpsc_categories');
			$export_colomn_value[] = $category->name;
		}
		elseif($value=='ticket_priority'){
			$ticket_priority = $wpscfunction->get_ticket_fields($ticket['id'],'ticket_priority');
			$priority        = get_term_by('id',$ticket_priority,'wpsc_priorities');
			$export_colomn_value[] = $priority->name;
		}
		elseif($value=='assigned_agent'){
			$agent_name = array();
			$assigned_agent  = $wpscfunction->get_ticket_meta($ticket['id'],'assigned_agent');
			if($assigned_agent[0]!='0'){
				foreach ($assigned_agent as $value) {
					$agent = get_term_by('id',$value,'wpsc_agents');
					if ($agent) {
						$arr   = get_term_meta($agent->term_id,'label',true);
						$agent_name[]=$arr;
					}
				}
				$export_colomn_value[]=implode(',',$agent_name);
			}
			else{
				$arr=__('None','wpsc-export-ticket');
				$export_colomn_value[]=$arr;
			}										  	  
		}
		elseif($value=='date_updated'){
			$date_updated    = $wpscfunction->get_ticket_fields($ticket['id'],'date_updated');
			$date_update_val= $wpscfunction->time_elapsed_string($date_updated);
			$export_colomn_value[]=$date_update_val;
		}
		elseif($value=='date_created'){
			$date_created    = $wpscfunction->get_ticket_fields($ticket['id'],'date_created');
			$date_created_val= get_date_from_gmt($date_created);
			$export_colomn_value[]=$date_created_val;
		}elseif ($value == 'user_type') {
			$ticket_user_type = $wpscfunction->get_ticket_fields($ticket['id'],'user_type');
			$export_colomn_value[] = ucfirst($ticket_user_type);
		}
		elseif ($value=='sf_rating') {
			$export_colomn_value = apply_filters('wpsc_sf_rating_ticket_fields',$export_colomn_value,$ticket['id'],$value);
		}
		elseif ($value=='timer') {
			$export_colomn_value = apply_filters('wpsc_timer_export_ticket_fields',$export_colomn_value,$ticket['id'],$value);
		}elseif($value == 'date_closed'){
			$custom_field_val = $wpscfunction->get_ticket_meta($ticket['id'],$value,true);
			if($custom_field_val){
				$wpsc_thread_date_format = get_option('wpsc_thread_date_format');
				if($wpsc_thread_date_format == 'timestamp'){
					$custom_field_val = $wpscfunction->time_elapsed_timestamp($custom_field_val);
				}else{
					$custom_field_val = $wpscfunction->time_elapsed_string($custom_field_val);
				}
			}
			$export_colomn_value[]=$custom_field_val;
		}elseif($value=='usergroup'){
			$export_colomn_value = apply_filters('wpsc_export_usergroup_ticket_fields',$export_colomn_value,$ticket['id'],$value);
		}else{
			$term = get_term_by('slug',$value,'wpsc_ticket_custom_fields');
			$tf_type = get_term_meta($term->term_id, 'wpsc_tf_type',true);
			if($tf_type == '3'){
				$check_vals = $wpscfunction->get_ticket_meta($ticket['id'],$value);
				$custom_field_val =implode(', ',$check_vals);
			}elseif($tf_type == '21'){
				$time_format = get_term_meta($term->term_id,'wpsc_time_format',true);
				if($time_format == '12'){
					$custom_field_val = date("h:i:s a", strtotime($wpscfunction->get_ticket_meta($ticket['id'],$value,true)));
				}else{
					$custom_field_val = $wpscfunction->get_ticket_meta($ticket['id'],$value,true);
				}
			}elseif($tf_type=='11'){
				$product_id  = $wpscfunction->get_ticket_meta($ticket['id'],$value,true);
				$custom_field_val = $product_id ? get_the_title($product_id) : 'None';
			}else{
				$custom_field_val = $wpscfunction->get_ticket_meta($ticket['id'],$value,true);
			}
			
			$export_colomn_value[]=$custom_field_val;
		}
	}	
	fputcsv($fp,$export_colomn_value);			    
}
fclose($fp);
echo '{"url_to_export":"'.$url_to_export.'"}';
?>