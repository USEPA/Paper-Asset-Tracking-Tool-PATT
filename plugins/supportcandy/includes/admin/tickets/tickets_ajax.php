<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$setting_action =  isset($_REQUEST['setting_action']) ? sanitize_text_field($_REQUEST['setting_action']) : '';

switch ($setting_action) {
  
  case 'init': include WPSC_ABSPATH . 'includes/admin/tickets/init.php';
    break;
		
	case 'sign_in': include WPSC_ABSPATH . 'includes/admin/tickets/sign_in/sign_in.php';
    break;
		
	case 'ticket_list': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/ticket_list.php';
    break;
		
	case 'individual_ticket': include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/individual_ticket.php';
    break;
		
	case 'create_ticket': include WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/create_ticket.php';
    break;
		
	case 'sign_up_user':
		$user_registration = get_option('wpsc_user_registration_method');
		
		if($user_registration == '1'){
			include WPSC_ABSPATH . 'includes/admin/tickets/sign_in/sign_up_user.php';
		}
		

    break;
		
	case 'submit_user':include WPSC_ABSPATH . 'includes/admin/tickets/sign_in/submit_user.php';
    break;
	
	case 'upload_file': include WPSC_ABSPATH . 'includes/admin/tickets/upload_file.php';
    break;
		
	case 'get_captcha_code': include WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/get_captcha_code.php';
    break;
		
	case 'get_users': include WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/get_users.php';
    break;
		
	case 'submit_ticket': include WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/submit_ticket.php';
    break;
		
	case 'set_default_filter': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/set_default_filter.php';
    break;
		
	case 'get_ticket_list': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/get_ticket_list.php';
    break;
		
	case 'filter_autocomplete': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/filter_autocomplete.php';
    break;
		
	case 'set_custom_filter': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/set_custom_filter.php';
    break;
		
	case 'get_save_ticket_filter': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/get_save_ticket_filter.php';
    break;
		
	case 'set_save_ticket_filter': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/set_save_ticket_filter.php';
    break;
  
	case 'set_saved_filter': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/set_saved_filter.php';
    break;
		
	case 'delete_saved_filter': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/delete_saved_filter.php';
    break;
		
	case 'submit_note': include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/submit_note.php';
    break;
		
	case 'submit_reply': include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/submit_reply.php';
    break;
	
	case 'get_bulk_change_status' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_bulk_change_status.php';
		break;
			
	case 'set_bulk_change_status' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_bulk_change_status.php';
		break;
	  
	case 'set_user_login': include WPSC_ABSPATH . 'includes/admin/tickets/sign_in/set_user_login.php';
    break;
		
	case 'get_delete_bulk_ticket': 	include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/get_delete_bulk_ticket.php';
		break;
		
	case 'set_delete_bulk_ticket' :	include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/set_delete_bulk_ticket.php';
		break;
		
	case 'get_bulk_assign_agent' :	include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_bulk_assign_agent.php';
	  break;
				
	case 'set_bulk_assign_agent' :  include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_bulk_assign_agent.php';
		break;
	
	case 'get_change_ticket_status' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_change_ticket_status.php';
		break;
		
	case 'set_change_ticket_status' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_change_ticket_status.php';
		break;
	
	case 'get_edit_ticket_subject' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_edit_ticket_subject.php';
		break;	
		
	case 'set_edit_ticket_subject' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_edit_ticket_subject.php';
		break;
		
  case 'get_close_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_close_ticket.php';
		break;
		
	case 'set_close_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_close_ticket.php';
		break;
		
	case 'get_change_assign_agent' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_change_assign_agent.php';
	  break;
	
	case 'set_change_assign_agent' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_change_assign_agent.php';
	  break;
		
	case 'get_edit_thread' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_edit_thread.php';
		break;
			
	case 'set_edit_thread' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_edit_thread.php';
	 break;
	 
	case 'get_delete_thread' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_delete_thread.php';
	break;
		
	case 'set_delete_thread' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_delete_thread.php';
	 break;
	 
	case 'get_clone_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_clone_ticket.php';
		break;
	
 	case 'set_clone_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_clone_ticket.php';
 		break;
	
  case 'get_delete_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_delete_ticket.php';
		break;
		
  case 'set_delete_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_delete_ticket.php';
		break;

	case 'get_change_raised_by' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_change_raised_by.php';
		break;
			
	case 'set_change_raised_by'	: include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_change_raised_by.php';
		break;	  	

	case 'get_restore_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_restore_ticket.php';
		break;
			
  case 'set_restore_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_restore_ticket.php';
		break;

	case 'get_change_ticket_fields' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_change_ticket_fields.php';
	break;
					
  case 'set_change_ticket_fields' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_change_ticket_fields.php';
		break;	

	case 'set_delete_attached_files'    : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_delete_attached_files.php';
		break;			
			
  case 'get_bulk_restore_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_bulk_restore_ticket.php';
	  break;
			
  case 'set_bulk_restore_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_bulk_restore_ticket.php';
		break;
	
	case 'get_change_agent_fields' : 	include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_change_agent_fields.php';
		break;
		
	case 'set_change_agent_fields' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_change_ticket_fields.php';
		break;
	
	case 'get_delete_ticket_permanently' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_delete_ticket_permanently.php';
		break;
		
	case 'set_delete_ticket_permanently' : 	include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_delete_ticket_permanently.php';
		break;
	
	case 'get_delete_permanently_bulk_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_delete_permanently_bulk_ticket.php';
	  break;
	
	case 'set_delete_permanently_bulk_ticket'  : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_delete_permanently_bulk_ticket.php';
		break;
				
	case 'rb_upload_file': include WPSC_ABSPATH . 'includes/admin/tickets/upload_file_rb.php';
		break;
	
	case 'get_create_thread' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_create_ticket_thread.php';
		break;
		
	case 'set_thread_ticket' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/set_create_ticket_thread.php';
	 	break;
	
	case 'get_add_ticket_users' : include WPSC_ABSPATH . 	'includes/admin/tickets/individual_ticket/get_add_ticket_users.php';
		break;
	
	case 'set_add_ticket_users' : include WPSC_ABSPATH . 	'includes/admin/tickets/individual_ticket/set_add_ticket_users.php';
		break;	
	
	case 'get_thread_info' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_thread_info.php';
		break;	
		
	case 'get_all_tickets_of_user' : include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/get_all_tickets_of_user.php';
		break;
	
	case 'remove_attachment': include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/remove_attachment.php';
		break;

	case 'update_saved_filter': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/update_saved_filter.php';
		break;
		
	case 'update_filter': include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/get_update_filter.php';
		break;

  default:
    _e('Invalid Action','supportcandy');
    break;
    
}
