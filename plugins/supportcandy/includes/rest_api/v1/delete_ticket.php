<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction;


$params    = $request->get_params();
$ticket_id = isset( $params['id'] ) ? $params['id'] : 0;

$response = array();

if ($wpscfunction->is_ticket($ticket_id)) {
  
    if($wpscfunction->has_permission('delete_ticket',$ticket_id)){
    	 $wpscfunction->delete_tickets($ticket_id);
       $response  = array(
         'ticket_deleted',
         'Ticket id '. $ticket_id .' is deleted successfully',
         array(
             'status' => 200,
         )
       );
    }else{
      $response  = array(
        'unauthorized',
        'You do not have permission to delete this ticket.',
        array(
            'status' => 404,
        )
      );
    }

} else {
    
    $response = new WP_Error(
        'not_found',
        'Ticket not found for id '.$ticket_id,
        array(
            'status' => 404,
        )
    );

}