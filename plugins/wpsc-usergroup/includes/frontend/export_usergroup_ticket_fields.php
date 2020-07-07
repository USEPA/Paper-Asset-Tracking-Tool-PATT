<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $wpdb, $wpscfunction;

if ($value == 'usergroup') {
    $customer_email = $wpdb->get_results("SELECT customer_email FROM {$wpdb->prefix}wpsc_ticket WHERE  id= '" . $ticket_id . "'");
    $user = get_user_by('email', $customer_email[0]->customer_email);
    $usergroups = get_terms([
        'taxonomy'    => 'wpsc_usergroup_data',
        'hide_empty'  => false,
        'meta_query'  => array(
           'relation' => 'AND',
            array(
                'key'     => 'wpsc_usergroup_userid',
                'value'   => $user->ID,
                'compare' => '=',
            ),
        ),
    ]);

    $usergroup_name = array();
    if ($usergroups) {
        foreach ($usergroups as $usergroup) {
            $usergroup_name[] = $usergroup->name;
        }
        $usergroup_name = implode(',', $usergroup_name);
        $export_colomn_value[] = $usergroup_name;
    } else {
        $arr = __('None', 'wpsc-usergroup');
        $export_colomn_value[] = $arr;
    }
}
