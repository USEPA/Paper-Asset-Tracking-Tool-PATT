<?php
// Code to inject label button

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$flag_btn = false;

$current_agent_id      = $wpscfunction->get_current_user_agent_id();
$agent_permissions = $wpscfunction->get_current_agent_permissions();

$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$ticket_data = $wpscfunction->get_ticket($ticket_id);
$status_id   	= $ticket_data['ticket_status'];

// Change Status ID when going to production to reflect the term_id of the "New" status

$status_array = array(3, 670, 69);
if (!in_array($status_id, $status_array)) {
    $flag_btn = true;
}

if($flag_btn):

?>

<?php //echo $status_id ?>
    <?php		
    if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
    {
    ?>
	<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_box_status_label_btn" style="<?php echo $action_default_btn_css ?>" onclick="#"><i class="fas fa-heartbeat"></i> Assign Box Status</button>
	<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_box_assign_label_btn" style="<?php echo $action_default_btn_css ?>" onclick="#"><i class="fas fa-user-plus"></i> Assign Staff</button>
    <?php
    }
    ?>
    
	<?php
endif;
