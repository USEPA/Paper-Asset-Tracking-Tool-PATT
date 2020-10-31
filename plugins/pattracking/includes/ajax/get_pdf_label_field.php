<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

$ticket_id  = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

$list_array = array();
        
$box_index_result = $wpdb->get_results( "SELECT DISTINCT wpqa_wpsc_epa_folderdocinfo.index_level FROM wpqa_wpsc_epa_boxinfo INNER JOIN wpqa_wpsc_epa_folderdocinfo ON wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id  WHERE wpqa_wpsc_epa_boxinfo.ticket_id = " . $ticket_id);

/*
$args = [
    'select' => 'SELECT DISTINCT index_level',
    'where' => ['ticket_id', $ticket_id],
];
$wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY('wpqa_wpsc_epa_boxinfo');
$box_index_result = $wpqa_wpsc_epa_boxinfo->get_results($args, false);
*/


foreach ( $box_index_result as $box_index )
    {
        array_push($list_array, $box_index->index_level);
    }
        
        
ob_start();
?>

<?php

//error handling: if there are no boxes in a request then steps won't display
$total_box_count = $wpdb->get_row("SELECT COUNT(a.box_id) as box_count
FROM wpqa_wpsc_epa_boxinfo a
INNER JOIN wpqa_wpsc_ticket b ON b.id = a.ticket_id
INNER JOIN wpqa_wpsc_epa_storage_location c on a.storage_location_id = c.id 
WHERE ((c.aisle <> 0 AND c.bay <> 0 AND c.shelf <> 0 AND c.position <> 0 AND c.digitization_center <> 666) AND a.box_destroyed = 0)
AND b.id = " . $ticket_id);
$box_count = $total_box_count->box_count;

if($box_count == 0) {
?>
<h4>All boxes in this request have been destroyed or do not currently have an assigned location and cannot be printed.</h4>
<?php
}
else {
?>

<h3>Step 1</h3>
<!--<p>Print box label and afix it to the side of the box.</p>-->
<p>Print box label and attach the label on the inside of the box lid in each box. Box labels should not be placed on the exterior to prevent carrier labels from obscuring barcodes with their shipping labels.</p>
<strong><a href="<?php echo WPPATT_PLUGIN_URL . 'includes/ajax/pdf/box_label.php?id=' . htmlentities($ticket_id); ?>" target="_blank">Box Label</a></strong>

<h3>Step 2</h3>
<p>Print Box list and place it into the first box of earch record schedule series.</p>
<strong><a href="<?php echo WPPATT_PLUGIN_URL . 'includes/ajax/pdf/box_list.php?id=' . htmlentities($ticket_id); ?>" target="_blank">Box List Printout</a></strong>

<h3>Step 3</h3>
<!--<p>Print folder/file labels. Folder seperate sheets must be placed as the first document in the folder. File labels must be placed on the top right of each document within the box.</p>-->
<p>Print folder/file labels. Folder separator sheets must be placed as the first document in the folder. File labels must be placed on the lower right hand corner of each document. Labels cannot cover any writing on the record, and location may deviate from this standard as necessary for accuracy. See box preparation/label placing Working Instruction for additional information.</p>

<?php
$box_index_value = current($list_array);
$list_array_count = count($list_array);

?>

<?php
if ($list_array_count == 1 && $box_index_value == 1 && $list_array_count != 0) {
?>
    <strong><a href="<?php echo WPPATT_PLUGIN_URL . 'includes/ajax/pdf/folder_separator_sheet.php?id=' . htmlentities($ticket_id); ?>" target="_blank">Folder Labels</a></strong>
<?php
} elseif ($list_array_count == 1 && $box_index_value == 2 && $list_array_count != 0) {
?>
    <strong><a href="<?php echo WPPATT_PLUGIN_URL . 'includes/ajax/pdf/file_separator_sheet.php?id=' . htmlentities($ticket_id); ?>" target="_blank">File Labels</a></strong>
<?php
} else {
?>
<?php
if ($list_array_count>1) {
    ?>
    <strong><a href="<?php echo WPPATT_PLUGIN_URL . 'includes/ajax/pdf/folder_separator_sheet.php?id=' . htmlentities($ticket_id); ?>" target="_blank">Folder Labels</a></strong><br />
    <strong><a href="<?php echo WPPATT_PLUGIN_URL . 'includes/ajax/pdf/file_separator_sheet.php?id=' . htmlentities($ticket_id); ?>" target="_blank">File Labels</a></strong>
<?php
} else {
?>
<strong>Box not assigned to request.</strong>

<?php
}
}

?>

<h3>Step 4</h3>
<p>Print shipping label and ensure that tracking number is properly entered into the Paper Asset Tracking Tool.</p>

<h3>Shipping Label Placement</h3>

<ol>
<li>Adhere the shipping label to the box using self-adhesive labels only. Do not use tape or glue.</li>  
<li>Be sure all edges are secure.</li>  
<li>Do not cover the barcode with tape or plastic wrap. Doing so will make your barcode un-scannable.</li>  
<li>Place the shipping label so it does not wrap around the edge of the package. The surface area of the address side of the parcel must be large enough to contain the entire label.</li>  
</ol>


<?php 

//end of steps 1-4
}

$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);
