<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction, $current_user, $wpdb;

/**
 * Exit if logged in user do not have administrator capabilities
 */
if( !$current_user->has_cap('edit_published_posts') ) exit();

$post_per_page = 20;
$page_no       = isset($_POST['page_no']) ? intval($_POST['page_no']) : 1;
$offset        = $post_per_page * ($page_no-1);

$args = array(
	'post_type'      => 'wpsc_ticket',
  'post_status'    => array('publish','trash'),
	'posts_per_page' => $post_per_page,
	'offset'         => $offset,
	'meta_key'       => 'ticket_id',
  'orderby'        => 'meta_value_num',
	'order'          => 'ASC',
);
if(class_exists('CPTO')){
	$args['ignore_custom_sort'] = true;
}

$tickets = new WP_Query( $args );
$tickets_list = $tickets->posts;


/**
 * Bigin importing ticket table entries
 */
foreach ($tickets_list as $ticket) :
  
  $is_imported = get_post_meta($ticket->ID,'wpsc_v2_is_imported',true);
  
  if(!$is_imported) :
  
      $customer_email = get_post_meta($ticket->ID,'customer_email',true);
      $user           = get_user_by('email',$customer_email);
      $user_type      = $user ? 'user' : 'guest';
      $active         = $ticket->post_status == 'publish' ? 1 : 0;
      
      $values = array(
        'id'               => get_post_meta($ticket->ID,'ticket_id',true),
        'ticket_status'    => get_post_meta($ticket->ID,'ticket_status',true),
        'customer_name'    => get_post_meta($ticket->ID,'customer_name',true),
        'customer_email'   => $customer_email,
        'ticket_subject'   => get_post_meta($ticket->ID,'ticket_subject',true),
        'user_type'        => $user_type,
        'ticket_category'  => get_post_meta($ticket->ID,'ticket_category',true),
        'ticket_priority'  => get_post_meta($ticket->ID,'ticket_priority',true),
        'date_created'     => get_post_meta($ticket->ID,'date_created',true),
        'date_updated'     => get_post_meta($ticket->ID,'date_updated',true),
        'ip_address'       => '',
        'agent_created'    => get_post_meta($ticket->ID,'agent_created',true),
        'ticket_auth_code' => get_post_meta($ticket->ID,'ticket_auth_code',true),
        'active'           => $active,
      );
      
      $ticket_id = $wpscfunction->create_new_ticket($values);
      
      $assigned_agents = get_post_meta($ticket->ID,'assigned_agent');
      foreach($assigned_agents as $agent_id){
          $wpscfunction->add_ticket_meta($ticket_id,'assigned_agent',$agent_id);
      }
      
      $sf_rating = get_post_meta($ticket->ID,'sf_rating',true);
      if($sf_rating) {
        $wpscfunction->add_ticket_meta($ticket_id,'sf_rating',$sf_rating);
      }
      
      $sla = get_post_meta($ticket->ID,'sla',true);
      if($sla) {
        $wpscfunction->add_ticket_meta($ticket_id,'sla',$sla);
      }
      
      $sla_term = get_post_meta($ticket->ID,'sla_term',true);
      if ($sla_term) {
        $wpscfunction->add_ticket_meta($ticket_id,'sla_term',$sla_term);
      }
      
      $frt = get_post_meta($ticket->ID,'first_response',true);
      if($frt) {
        $wpscfunction->add_ticket_meta($ticket_id,'first_response',$frt);
      }
      
      // Custom fields
      $fields = get_terms([
      	'taxonomy'   => 'wpsc_ticket_custom_fields',
      	'hide_empty' => false,
      	'orderby'    => 'meta_value_num',
      	'meta_key'	 => 'wpsc_tf_load_order',
      	'order'    	 => 'ASC',
      	'meta_query' => array(
      		'relation' => 'AND',
      		array(
            'key'       => 'agentonly',
            'value'     => array(0,1),
            'compare'   => 'IN'
          ),
      		array(
            'key'       => 'wpsc_tf_type',
            'value'     => '0',
            'compare'   => '>'
          ),
      	),
      ]);
      
      if($fields){
        	
          foreach ($fields as $field) {
            		$tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type', true);
            		switch ($tf_type) {
              			case '1':	
              			case '2':
              			case '4':
              			case '5':
              			case '7':
              			case '8':
              	    case '9':
                    case '11':
                    case '12':
                    case '13':
                    case '14':
              						$text = get_post_meta($ticket->ID,$field->slug,true);
              						if($text){
              							$wpscfunction->add_ticket_meta($ticket_id, $field->slug,$text);
              						}
              						break;
              			case '3':
              			case '10':
                  				$arrVal = get_post_meta($ticket->ID,$field->slug);
                  				if($arrVal){
                  					foreach ($arrVal as $key => $value) {
                  						$wpscfunction->add_ticket_meta($ticket_id, $field->slug,$value);
                  						update_term_meta ($value, 'active', '1');
                  					}
                  				}
                  				break;

              			case '6':
                  				$date = get_post_meta($ticket->ID,$field->slug,true);
                          if($date) {
                  					$date = $wpscfunction->calenderDateFormatToDateTime($date);
                  					$wpscfunction->add_ticket_meta($ticket_id, $field->slug,$date);
                  				}
                  				break;
                          
                    default : 
                          do_action('wpsc_db_v2_upgade_custom_field',$ticket_id,$tf_type,$field,$ticket->ID);
                          break;
          			}
        	}
          
      }
      
      update_post_meta( $ticket->ID, 'wpsc_v2_is_imported', 1 );
      do_action('wpsc_db_upgrade_data',$ticket_id,$ticket->ID);
  endif;
endforeach;

$current_page = $page_no;
$total_items  = $tickets->found_posts;
$total_pages  = $total_items ? ceil($total_items/$post_per_page) : 1;
$completed    = ceil(($current_page/$total_pages)*100);
$is_next      = $current_page < $total_pages ? 1 : 0;

if(!$is_next){
  $label_count_history = get_option( 'wpsc_label_count_history' );
  update_option( 'wpsc_label_count_history', ++$label_count_history );
  update_option( 'wpsc_db_version', '2.0' );
}

$response = array(
  'completed' => $completed,
  'is_next' => $is_next,
  'page_no' => $current_page+1,
);

echo json_encode($response);
