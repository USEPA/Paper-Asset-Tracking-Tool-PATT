<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction,$wpdb;

$wpsc_ticket_data = $wpscfunction->get_ticket($ticket_id);
$ln_flag   = false;
$tdec_flag = false;
$lr_flag   =  false;

preg_match_all("/{[^}]*}/" ,$str,$matches);
$matches = array_unique($matches[0]);

foreach($matches as $match){

	$field = get_term_by('slug',str_replace("{}"," ",$match),'wpsc_ticket_custom_fields');
	$tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type', true);

	if($tf_type > '0') {
		switch ($tf_type) {
			case '1':	
			case '2':
			case '4':
			case '9':
				$text = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
				$str  = preg_replace('/{'.$field->slug.'}/', $text, $str);
			  break;

			case '3':
				$text = $wpscfunction->get_ticket_meta($ticket_id,$field->slug);
				if($text){
					$str = preg_replace('/{'.$field->slug.'}/', implode(', ',$text), $str);
				} else {
					$str = preg_replace('/{'.$field->slug.'}/', '', $str);
				}
			  break;

			case '5':
				$text = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
				$str  = preg_replace('/{'.$field->slug.'}/', nl2br($text), $str);
			  break;

			case '6':
				$date = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
				$str = preg_replace('/{'.$field->slug.'}/', $this->datetimeToCalenderFormat($date), $str);
			  break;

			case '7':
				$text = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
				$text = $text ? '<a href="'.$text.'" target="_blank">'.$text.'</a>' : '';
				$str  = preg_replace('/{'.$field->slug.'}/', $text, $str);
			  break;

			case '8':
				$text = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
				$text = $text ? '<a href="mailto:'.$text.'" target="_blank">'.$text.'</a>' : '';
				$str  = preg_replace('/{'.$field->slug.'}/', $text, $str);
			  break;

			case '10':
				$attachments = $wpscfunction->get_ticket_meta($ticket_id, $field->slug);
				$auth_id = $wpscfunction->get_ticket_fields($ticket_id,'ticket_auth_code');
				$attach_url = array();
				if($attachments){
					foreach( $attachments as $attachment ):
						$attach = array();
						$attach_meta = get_term_meta($attachment);
						foreach ($attach_meta as $key => $value) {
							$attach[$key] = $value[0];
						}
						$upload_dir   = wp_upload_dir();
				
						$wpsp_file = get_term_meta($attachment,'wpsp_file');
				
						if ($wpsp_file) {
							$file_url = $upload_dir['baseurl']  . '/wpsp/'. $attach['save_file_name'];
						}else {
							if( isset($attach['is_restructured']) && $attach['is_restructured']){
								$updated_time = get_term_meta($attachment,'time_uploaded',true);
								$time  = strtotime($updated_time);
								$month = date("m",$time);
								$year  = date("Y",$time);
								$file_url = $upload_dir['baseurl'] . '/wpsc/'.$year.'/'.$month.'/'. $attach['save_file_name'];
							}else{
								$file_url  = $upload_dir['baseurl'] . '/wpsc/'. $attach['save_file_name'];
							}
						}
				
						$download_url = site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
						$attach_url[] = '<a href="'.$download_url.'" target="_blank">'.$attach['filename'].'</a>';
					
					endforeach;	
					
					$str = preg_replace('/{'.$field->slug.'}/', implode('<br />',$attach_url), $str);

				}  else {
					$str = preg_replace('/{'.$field->slug.'}/', ' ', $str);
				}
			  break;
	
			case '18':	
				$date =  $wpscfunction->get_ticket_meta( $ticket_id, $field->slug, true );
				if($date){
					$str = preg_replace('/{'.$field->slug.'}/', $date, $str);
				} else {
					$str = preg_replace('/{'.$field->slug.'}/', '', $str);
				}
			  break;

			case '21': 
				$time  = $wpscfunction->get_ticket_meta($ticket_id, $field->slug, true);
				$time_format = get_term_meta($field->term_id, 'wpsc_time_format',true);

				if($time){
					if($time_format == '12'){
						$str = preg_replace('/{'.$field->slug.'}/', date("h:i:s a", strtotime($time)), $str);
					} else{
						$str = preg_replace('/{'.$field->slug.'}/', $time, $str);
					}
				}else{
					$str = preg_replace('/{'.$field->slug.'}/', '', $str);
				}
			  break;

			default :
				$str = apply_filters('wpsc_replace_macro_custom',$str,$ticket_id,$tf_type,$field);
			  break;
		}
	} else{
	
		switch($match){
			
			// Ticket ID
			case '{ticket_id}':
				$str = preg_replace('/{ticket_id}/', $ticket_id, $str);
			  break;
	
			// Ticket Status
			case '{ticket_status}':
			
				$ticket_status = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['ticket_status'] ) ?  $this->get_status_name($wpsc_ticket_data['ticket_status']) : '';
				$str = preg_replace('/{ticket_status}/', $ticket_status, $str);
			  break;
	
			// Ticket Category
			case '{ticket_category}' :
				$ticket_category = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['ticket_category'] ) ?  $this->get_category_name($wpsc_ticket_data['ticket_category']) : '';
				$str = preg_replace('/{ticket_category}/', $ticket_category, $str);
			  break;
	
			// Ticket Priority
			case '{ticket_priority}':
				$ticket_priority = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['ticket_priority'] ) ?  $this->get_priority_name($wpsc_ticket_data['ticket_priority']) : '';
				$str = preg_replace('/{ticket_priority}/',$ticket_priority, $str);
			  break;
	
			// Customer Name
			case '{customer_name}':
				$customer_name = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['customer_name'] ) ? stripslashes($wpsc_ticket_data['customer_name']) : '';
				$str = preg_replace('/{customer_name}/',$customer_name, $str);
			  break;
	
			// Customer Email
			case '{customer_email}':
				$customer_email = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['customer_email'] ) ? $wpsc_ticket_data['customer_email'] : '';
				$str = preg_replace('/{customer_email}/',$customer_email, $str);
			  break;
	
			// Subject
			case '{ticket_subject}':
				$ticket_subject = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['ticket_subject'] ) ? str_replace('$','\$', $wpsc_ticket_data['ticket_subject']) : '';
				$str = preg_replace('/{ticket_subject}/', stripslashes($ticket_subject), $str);
			  break;

			//User Type
			case '{user_type}':
				$user_type = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['user_type'] ) ? $wpsc_ticket_data['user_type'] : '';
				$str = preg_replace('/{user_type}/', $user_type, $str);
			  break;
	
			// Ticket Description
			case '{ticket_description}':

				$ticket_description =  $this->get_ticket_description($ticket_id);
				if (strpos($str, 'ticket_description') !== false) {
					$tdec_flag = true;
				}
				$ticket_description['description'] = str_replace('$','\$', $ticket_description['description']);
				$str = preg_replace('/{ticket_description}/', $ticket_description['description'], $str);
			  break;
	
			// Last Reply
			case '{last_reply}':
				$last_reply = $this->get_last_reply($ticket_id);
				if (strpos($str, 'last_reply') !== false) {
					$lr_flag = true;
				}
				$last_reply['description'] = str_replace('$','\$',$last_reply['description']);
				$str = preg_replace('/{last_reply}/', $last_reply['description'],$str);
			  break;
	
			// Last Reply User Name
			case '{last_reply_user_name}':
				$last_reply = $this->get_last_reply($ticket_id);
				$str = preg_replace('/{last_reply_user_name}/', $last_reply['user_name'], $str);
			  break;
	
			// Last Reply User Email
			case '{last_reply_user_email}':
				$last_reply = $this->get_last_reply($ticket_id);
				$str = preg_replace('/{last_reply_user_email}/', $last_reply['user_email'], $str);
			  break;
			
			// Last Note
			case '{last_note}':	
				$last_note = $this->get_last_note($ticket_id);
				
				if (strpos($str, 'last_note') !== false) {
					$ln_flag = true;
				}
				$last_note['description'] = str_replace('$','\$',$last_note['description']);
				$str = preg_replace('/{last_note}/', $last_note['description'], $str);
			  break;
	
			// Last Note User Name
			case '{last_note_user_name}':
				$last_note = $this->get_last_note($ticket_id);
				$str = preg_replace('/{last_note_user_name}/', $last_note['user_name'], $str);
			  break;
			
			// Last Note User Email
			case '{last_note_user_email}':
				$last_note = $this->get_last_note($ticket_id);
				$str = preg_replace('/{last_note_user_email}/', $last_note['user_email'], $str);
			  break;
			
			// Current User Name
			case '{current_user_name}':
				$str = preg_replace('/{current_user_name}/', $current_user->display_name, $str);
			  break;
	
			// Current User Email
			case '{current_user_email}':
				$str = preg_replace('/{current_user_email}/', $current_user->user_email, $str);
			  break;	
	
			// Ticket History	
			case '{ticket_history}':
				$ticket_history = $this->get_ticket_history($ticket_id);
				$ticket_history = str_replace('$','\$',$ticket_history);
				$str = preg_replace('/{ticket_history}/', $ticket_history, $str);
			  break;
	
			// Assigned Agents
			case '{assigned_agent}':
				$assigned_agents = $this->get_assigned_agent_names($ticket_id);
				$str = preg_replace('/{assigned_agent}/', $assigned_agents, $str);
			  break;
	
			// Previously Assigned agents
			case '{previously_assigned_agent}':
				$previously_assigned_agent = $this->get_previously_assigned_agents_names($ticket_id);
				$str = preg_replace('/{previously_assigned_agent}/', $previously_assigned_agent, $str);
			  break;
			
			// Date created
			case '{date_created}':
				$date_created = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['date_created'] ) ? get_date_from_gmt( $wpsc_ticket_data['date_created'] ,$this->time_elapsed_timestamp($wpsc_ticket_data['date_created']) ) : '';
				$str = preg_replace('/{date_created}/',$date_created, $str);
			  break;
	
			// Date updated
			case '{date_updated}':
				$date_updated = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['date_updated'] ) ? get_date_from_gmt($wpsc_ticket_data['date_updated']) : '';
				$str = preg_replace('/{date_updated}/', $date_updated , $str);
			  break;
	
			// Agent created
			case '{agent_created}':
				$agent_created = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['agent_created'] ) ? $this->get_agent_name($wpsc_ticket_data['agent_created']) : '';
				$str = preg_replace('/{agent_created}/',$agent_created, $str);
			  break;	
	
			// All ticket history	
			case '{ticket_history_all}':
				$ticket_history_all = $this->get_ticket_history_all($ticket_id);
				$ticket_history_all = str_replace('$','\$', $ticket_history_all);
				$str = preg_replace('/{ticket_history_all}/', $ticket_history_all,$str); 
			  break;	
			
			// All ticket history with all notes
			case '{ticket_history_all_with_notes}':
				if ($current_user && $current_user->has_cap('wpsc_agent')) {
	
					$ticket_history_all_with_notes = $this->get_ticket_history_all_with_notes($ticket_id);
					$ticket_history_all_with_notes = str_replace('$', '\$', $ticket_history_all_with_notes);
					$str = preg_replace('/{ticket_history_all_with_notes}/', $ticket_history_all_with_notes, $str);
				
				} else {
					$str = preg_replace('/{ticket_history_all_with_notes}/',' ',$str);
				}
			  break;	
			
			// All ticket history of notes
			case '{ticket_notes_history}':
				if ($current_user && $current_user->has_cap('wpsc_agent')) {
	
					$ticket_notes_history = $this->ticket_notes_history($ticket_id);
					$ticket_notes_history = str_replace('$','\$',$ticket_notes_history);
					$str = preg_replace('/{ticket_notes_history}/',$ticket_notes_history , $str);
				
				} else {
					$str = preg_replace('/{ticket_notes_history}/',' ', $str);	
				}
			  break;
			
			// Ticket URL
			case '{ticket_url}':

				$ticket_auth_code = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['ticket_auth_code'] ) ?  $wpsc_ticket_data['ticket_auth_code'] : '';
				$ticket_url = get_permalink(get_option('wpsc_support_page_id')).'?support_page=open_ticket&ticket_id='.$ticket_id.'&auth_code='.$ticket_auth_code;
				$ticket_url = '<a href="'.$ticket_url.'" target="_blank">'.$ticket_url.'</a>';
				$str = preg_replace('/{ticket_url}/', $ticket_url, $str);
			  break;	
			
			// All ticket history with logs
			case '{ticket_history_all_with_logs}':
				if ($current_user && $current_user->has_cap('wpsc_agent')) {
					
					$ticket_history_all_with_logs = $this->get_ticket_history_all_with_logs($ticket_id);
					$ticket_history_all_with_logs = str_replace('$', '\$', $ticket_history_all_with_logs);
					$str = preg_replace('/{ticket_history_all_with_logs}/', $ticket_history_all_with_logs, $str);
	
				}else{
					$str = preg_replace('/{ticket_history_all_with_logs}/', ' ', $str);
				}	
			  break;
	
			// All ticket history with notes and log
			case '{ticket_history_all_with_notes_and_logs}':
				if ( $current_user && $current_user->has_cap('wpsc_agent')) {
	
					$ticket_history_all_with_notes_and_logs = $this->get_ticket_history_all_with_notes_and_logs($ticket_id);
					$ticket_history_all_with_notes_and_logs = str_replace('$', '\$', $ticket_history_all_with_notes_and_logs);
					$str = preg_replace('/{ticket_history_all_with_notes_and_logs}/', $ticket_history_all_with_notes_and_logs, $str);
				
				} else {
					$str = preg_replace('/{ticket_history_all_with_notes_and_logs}/',' ', $str);
				}
			  break;	
			
			// Ticket notes history with logs
			case '{ticket_notes_history_with_logs}':
				if ($current_user && $current_user->has_cap('wpsc_agent')) {
	
					$ticket_notes_history_with_logs = $this->get_ticket_notes_history_with_logs($ticket_id);
					$ticket_notes_history_with_logs = str_replace('$', '\$', $ticket_notes_history_with_logs);
					$str = preg_replace('/{ticket_notes_history_with_logs}/', $ticket_notes_history_with_logs, $str);
				
				}else {
					$str = preg_replace('/{ticket_notes_history_with_logs}/',' ', $str);
				}	
			  break;
	
			//Date Closed
			case '{date_closed}':
				$date_closed =  $wpscfunction->time_elapsed_timestamp($wpscfunction->get_ticket_meta($ticket_id, 'date_closed', true));
				$str  = preg_replace('/{date_closed}/', $date_closed, $str);	
			  break;
			
			case '{customer_first_name}':
				$customer_email = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['customer_email'] ) ? stripslashes($wpsc_ticket_data['customer_email']) : '';
				$user =  get_user_by( 'email', $customer_email );
				if($user){
					$first_name = get_user_meta( $user->ID,'first_name', true );
					if($first_name){
						$str = preg_replace('/{customer_first_name}/',$first_name, $str);
					}else{
						$customer_name = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['customer_name'] ) ? stripslashes($wpsc_ticket_data['customer_name']) : '';
						$str = preg_replace('/{customer_first_name}/',$customer_name, $str);
					}
				}else{
					$customer_name = (!empty($wpsc_ticket_data)) && isset( $wpsc_ticket_data['customer_name'] ) ? stripslashes($wpsc_ticket_data['customer_name']) : '';
					$str = preg_replace('/{customer_first_name}/',$customer_name, $str); 
				}			
			    break;

			case '{last_reply_user_first_name}':
				$last_reply = $this->get_last_reply($ticket_id);
				$user = get_user_by('email',$last_reply['user_email']);
				if( $user ){
					$first_name = get_user_meta( $user->ID,'first_name', true );
					if($first_name){
						$str = preg_replace('/{last_reply_user_first_name}/',$first_name, $str);
					}else{
						$str = preg_replace('/{last_reply_user_first_name}/',$last_reply['user_name'], $str);
					}
				}else{
					$str = preg_replace('/{last_reply_user_first_name}/', $last_reply['user_name'], $str);
				}
		    	break;

			case '{current_user_first_name}':
				$first_name = get_user_meta( $current_user->ID,'first_name', true );
				if($first_name){
					$str = preg_replace('/{current_user_first_name}/',$first_name, $str);
				}else{
					$str = preg_replace('/{current_user_first_name}/',$current_user->display_name, $str);
				}	
				break;
			
			case '{last_note_user_first_name}':
				$last_note = $this->get_last_note($ticket_id);
				$user = get_user_by('email',$last_note['user_email']);
				if( $user ){
					$first_name = get_user_meta( $user->ID,'first_name', true );
					if($first_name){
						$str = preg_replace('/{last_note_user_first_name}/',$first_name, $str);
					}else{
						$str = preg_replace('/{last_note_user_first_name}/',$last_note['user_name'], $str);
					}
				}else{
					$str = preg_replace('/{last_note_user_first_name}/', $last_note['user_name'], $str);
				}
		    	break;

			default:
			break;
		}
	}
}

if($tdec_flag){
	if(isset($ticket_description['thread_id'])){
	$report_thread_id   = $ticket_description['thread_id'];
	$report_attachments = get_post_meta($report_thread_id,'attachments',true);
	$auth_id            = $wpsc_ticket_data['ticket_auth_code'];
	$ticket_id          = get_post_meta($report_thread_id,'ticket_id',true);
	$attach_url         = array();
	if($report_attachments){
		$attach_url[]	 = 'Attachments:';
		foreach( $report_attachments as $attachment ):
			$attach      = array();
			$attach_meta = get_term_meta($attachment);
			foreach ($attach_meta as $key => $value) {
				$attach[$key] = $value[0];
			}
			$upload_dir   = wp_upload_dir();
			
			$wpsp_file = get_term_meta($attachment,'wpsp_file');
			
			if ($wpsp_file) {
				$file_url = $upload_dir['baseurl']  . '/wpsp/'. $attach['save_file_name'];
			}else {
				if( isset($attach['is_restructured']) && $attach['is_restructured']){
					$updated_time   = get_term_meta($attachment,'time_uploaded',true);
					$time  = strtotime($updated_time);
					$month = date("m",$time);
					$year  = date("Y",$time);
					$file_url = $upload_dir['baseurl'] . '/wpsc/'.$year.'/'.$month.'/'. $attach['save_file_name'];
				}else{
					$file_url = $upload_dir['baseurl'] . '/wpsc/'. $attach['save_file_name'];
				}
			}
			
			$download_url = site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
			$attach_url[] = '<a style="text-decoration:none;" href="'.$download_url.'" target="_blank">'.$attach['filename'].'</a>';
		endforeach;
	}
  $str = $str.implode("<br>",$attach_url);
}
}
if($lr_flag){
	$last_reply_thread_id   = $last_reply['thread_id'];
	$last_reply_attachments = get_post_meta($last_reply_thread_id,'attachments',true);
	$auth_id = $wpsc_ticket_data['ticket_auth_code'];
	$ticket_id = get_post_meta($last_reply_thread_id,'ticket_id',true);
	$attach_url = array();
	if($last_reply_attachments){
		$attach_url[]  = 'Attachments:';
		foreach( $last_reply_attachments as $attachment ):
			$attach      = array();
			$attach_meta = get_term_meta($attachment);
			foreach ($attach_meta as $key => $value) {
				$attach[$key] = $value[0];
			}
			$upload_dir   = wp_upload_dir();
			if( isset($attach['is_restructured']) && $attach['is_restructured']){
				$updated_time   = get_term_meta($attachment,'time_uploaded',true);
				$time  = strtotime($updated_time);
				$month = date("m",$time);
				$year  = date("Y",$time);
				$file_url     = $upload_dir['baseurl'] . '/wpsc/'.$year.'/'.$month.'/'.$attach['save_file_name'];
			}else{
				$file_url     = $upload_dir['baseurl'] . '/wpsc/'.$attach['save_file_name'];
			}
			
			$download_url = site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
			$attach_url[] = '<a  style="text-decoration:none;" href="'.$download_url.'" target="_blank">'.$attach['filename'].'</a>';
		endforeach;
	}
	$str = $str.implode("<br>",$attach_url);
}
 
if($ln_flag){
	$last_note_thread_id	 = $last_note['thread_id'];
	$last_note_attachments = get_post_meta($last_note_thread_id,'attachments',true);
	$auth_id	= $wpsc_ticket_data['ticket_auth_code'];
	$ticket_id = get_post_meta($last_note_thread_id,'ticket_id',true);
	$attach_url = array();
	if($last_note_attachments){
		$attach_url[] 	= 'Attachments:';
		foreach( $last_note_attachments as $attachment ):
			$attach      = array();
			$attach_meta = get_term_meta($attachment);
			foreach ($attach_meta as $key => $value) {
				$attach[$key] = $value[0];
			}
			$upload_dir   = wp_upload_dir();
			
			$wpsp_file = get_term_meta($attachment,'wpsp_file');
			
			if ($wpsp_file) {
				$file_url = $upload_dir['baseurl']  . '/wpsp/'. $attach['save_file_name'];
			}else {
				if( isset($attach['is_restructured']) && $attach['is_restructured']){
					$updated_time   = get_term_meta($attachment,'time_uploaded',true);
					$time  = strtotime($updated_time);
					$month = date("m",$time);
					$year  = date("Y",$time);
					$file_url     = $upload_dir['baseurl'] . '/wpsc/'.$year.'/'.$month.'/'. $attach['save_file_name'];
				}else{
					$file_url     = $upload_dir['baseurl'] . '/wpsc/'. $attach['save_file_name'];
				}
			}
			
			$download_url = site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
			$attach_url[] = '<a  style="text-decoration:none;" href="'.$download_url.'" target="_blank">'.$attach['filename'].'</a>';
		endforeach;
	 }
	$str = $str.implode("<br>",$attach_url); 
}

$str = apply_filters('wpsc_replace_macro',$str,$ticket_id);

