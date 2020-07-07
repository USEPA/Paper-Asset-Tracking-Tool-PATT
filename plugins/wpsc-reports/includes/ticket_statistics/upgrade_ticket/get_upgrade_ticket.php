<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb, $post;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

$page = isset($_POST['page']) ? sanitize_text_field($_POST['page']) : '';
$total_result = isset($_POST['total_result']) ? sanitize_text_field($_POST['total_result']) : '';

$sql = " SELECT SQL_CALC_FOUND_ROWS t.id FROM {$wpdb->prefix}wpsc_ticket t WHERE  t.id NOT IN (SELECT DISTINCT tm.ticket_id FROM {$wpdb->prefix}wpsc_ticketmeta tm
WHERE  tm.meta_key = 'ticket_counted') LIMIT 10";

$the_query = $wpdb->get_results($sql);

if ($the_query) {
    foreach ($the_query as $ticket) {
        
        $wpscfunction->add_ticket_meta($ticket->id, 'ticket_counted', 1);

        $post_created = $wpscfunction->get_ticket_fields($ticket->id, 'date_created');
        $post_created = date('Y-m-d', strtotime($post_created));
        
        $is_imported = $wpdb->get_var("SELECT ticket_count FROM {$wpdb->prefix}wpsc_reports WHERE result_date ='".$post_created."' AND report_type='no_of_tickets'");
        if(!$is_imported){
            $wpdb->insert($wpdb->prefix . 'wpsc_reports',
                array(
                    'report_type' => 'no_of_tickets',
                    'type' => 'daily',
                    'ticket_count' => 1,
                    'result_date' => $post_created
                ));
        }else{
            $count = $wpdb->get_var("SELECT ticket_count from {$wpdb->prefix}wpsc_reports WHERE result_date ='".$post_created."' AND report_type='no_of_tickets'");
            $count++;
            $values = array(
                'ticket_count' => $count
            );
            
            $wpdb->update($wpdb->prefix . 'wpsc_reports',$values,array('result_date'=>$post_created,'report_type' => 'no_of_tickets'));

        }
    }
} 

$percentage = (($page * 2) / $total_result) * 100;
$is_next = $page < ($total_result / 5) ? 1 : 0;

$response = array(
    'is_next' => $is_next,
    'percentage' => ceil($percentage),
    'page' => $page + 1,
);

echo json_encode($response);
