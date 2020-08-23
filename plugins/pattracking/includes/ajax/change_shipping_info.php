<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

if (!isset($_SESSION)) {
    session_start();    
}


ob_start();

$db_id = $_POST['db_id'];

    $get_shipping_details = $wpdb->get_row("SELECT company_name, tracking_number
    FROM wpqa_wpsc_epa_shipping_tracking
    WHERE id = '" . $db_id[0] . "'");
    $tracking_number = $get_shipping_details->tracking_number;
    $company_name = $get_shipping_details->company_name;

?>

<strong>Tracking Number:</strong><br />

<input type="text" id="tracking_number" name="tracking_number" value='<?php echo $tracking_number; ?>'>

<br></br>

<strong>Company:</strong><br />

		<select id="company_name" name="company_name">
			<?php

$company_arry = [
    "UPS" => "ups",
    "FedEx" => "fedex",
    "USPS" => "usps",
    "DHL" => "dhl",
]; // list of acceptable companies

      foreach ( $company_arry as $term=>$company ) :

if ($company_name == $company ) {
    $selected = 'selected'; 
} else {
    $selected = ''; 
}

echo '<option '.$selected.' value="'.$company.'">'.$term.'</option>';
			endforeach;
			?>
		</select>
		
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_edit_shipping_details();"><?php _e('Save','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);