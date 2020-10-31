<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if (strpos($str, '{usergroup}') !== false) {
  
  global $wpscfunction;

  $usergroups = get_terms([
    'taxonomy'   => 'wpsc_usergroup_data',
    'hide_empty' => false,
    'orderby'    => 'meta_value_num',
    'order'    	 => 'ASC'
  ]);

  $customer_email = $wpscfunction->get_ticket_fields($ticket_id, 'customer_email');
  $user           = get_user_by('email', $customer_email);
  $replace_data   = array();

  foreach ($usergroups as $usergroup) {
    $wpsc_usergroup_userid     = get_term_meta($usergroup->term_id, 'wpsc_usergroup_userid');
    if ($user){
      if (in_array($user->ID, $wpsc_usergroup_userid)) {
        $replace_data[]  = $usergroup->name;
      }
    }
  }

  if (!$replace_data) {
    $r_data = __('Usergroup Not Found', 'wpsc-usergroup');
  } else {
    $r_data = implode(',', $replace_data);
  }

  $str = preg_replace('/{usergroup}/', $r_data, $str);
}
?>