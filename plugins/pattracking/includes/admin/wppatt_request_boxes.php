<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$subfolder_path = site_url( '', 'relative'); 

global $wpdb, $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
		exit;
}
$ticket_id 	 = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0 ;
$raisedby_email = $wpscfunction->get_ticket_fields($ticket_id, 'customer_email');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
$wpsc_appearance_ticket_list = get_option('wpsc_appearance_ticket_list');

$agent_permissions = $wpscfunction->get_current_agent_permissions();
$current_agent_id  = $wpscfunction->get_current_user_agent_id();

$restrict_rules = array(
	'relation' => 'AND',
	array(
		'key'            => 'customer_email',
		'value'          => $raisedby_email,
		'compare'        => '='
	),
	array(
		'key'            => 'active',
		'value'          => 1,
		'compare'        => '='
	)
);
$ticket_permission = array(
	'relation' => 'OR'
);
if ($agent_permissions['view_unassigned']) {
	$ticket_permission[] = array(
		'key'            => 'assigned_agent',
		'value'          => 0,
		'compare'        => '='
	);
}

if ($agent_permissions['view_assigned_me']) {
	$ticket_permission[] = array(
		'key'            => 'assigned_agent',
		'value'          => $current_agent_id,
		'compare'        => '='
	);
}

if ($agent_permissions['view_assigned_others']) {
	$ticket_permission[] = array(
		'key'            => 'assigned_agent',
		'value'          => array(0,$current_agent_id),
		'compare'        => 'NOT IN'
	);
}

$restrict_rules [] = $ticket_permission;
$select_str        = 'DISTINCT t.*';
$sql               = $wpscfunction->get_sql_query( $select_str, $restrict_rules);
$tickets           = $wpdb->get_results($sql);
$ticket_list       = json_decode(json_encode($tickets), true);

$ticket_list_items = get_terms([
  'taxonomy'   => 'wpsc_ticket_custom_fields',
  'hide_empty' => false,
  'orderby'    => 'meta_value_num',
  'meta_key'	 => 'wpsc_tl_agent_load_order',
  'order'    	 => 'ASC',
  'meta_query' => array(
    'relation' => 'AND',
    array(
      'key'       => 'wpsc_allow_ticket_list',
      'value'     => '1',
      'compare'   => '='
    ),
    array(
      'key'       => 'wpsc_agent_ticket_list_status',
      'value'     => '1',
      'compare'   => '='
    ),
  ),
]);
ob_start();
?>

<?php //echo $status_id ?>
<style>
.datatable_header {
background-color: rgb(66, 73, 73) !important; 
color: rgb(255, 255, 255) !important; 
width: 204px;
}
.bootstrap-iso .alert {
    padding: 8px;
}
</style>
<?php
//unauthorized destruction notification
$box_details = $wpdb->get_row(
"SELECT count(wpqa_wpsc_epa_folderdocinfo.id) as count
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_folderdocinfo ON wpqa_wpsc_epa_boxinfo.id = wpqa_wpsc_epa_folderdocinfo.box_id
WHERE wpqa_wpsc_epa_folderdocinfo.unauthorized_destruction = 1 AND wpqa_wpsc_epa_boxinfo.ticket_id = '" . $ticket_id . "'"
			);

$unauthorized_destruction_count = $box_details->count;

if($unauthorized_destruction_count > 0){
?>
<div class="alert alert-danger" role="alert">
<span style="font-size: 1em; color: #8b0000;"><i class="fas fa-flag" title="Unauthorized Destruction"></i></span> One or more documents related to this request contains a unauthorized destruction flag.
</div>
<?php
}
?>

<?php
//freeze notification
$box_freeze = $wpdb->get_row("SELECT count(wpqa_wpsc_epa_folderdocinfo.id) as count
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_folderdocinfo ON wpqa_wpsc_epa_boxinfo.id = wpqa_wpsc_epa_folderdocinfo.box_id
WHERE wpqa_wpsc_epa_folderdocinfo.freeze = 1 AND wpqa_wpsc_epa_boxinfo.ticket_id = '" . $ticket_id . "'");
$freeze_count = $box_freeze->count;

if($freeze_count > 0) {
?>
<div class="alert alert-info" role="alert">
<span style="font-size: 1em; color: #009ACD;"><i class="fas fa-snowflake" title="Freeze"></i></span> One or more documents related to this request contains a frozen folder/file.
</div>
<?php
}
?>

<h4>Boxes Related to Request</h4>

<?php
	//$box_details = Patt_Custom_Func::fetch_box_details($ticket_id);

$box_details = $wpdb->get_results(
"SELECT 
wpqa_wpsc_epa_boxinfo.id as id, 
(SELECT sum(unauthorized_destruction = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = wpqa_wpsc_epa_boxinfo.id) as ud,
(SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = wpqa_wpsc_epa_boxinfo.id) as val_sum,
(SELECT sum(freeze = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = wpqa_wpsc_epa_boxinfo.id) as freeze_sum,
(SELECT count(id) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = wpqa_wpsc_epa_boxinfo.id) as doc_total,
wpqa_wpsc_epa_boxinfo.box_id as box_id, 
wpqa_terms.name as digitization_center, 
wpqa_terms.slug as digitization_center_slug, 
wpqa_wpsc_epa_storage_location.aisle as aisle, 
wpqa_wpsc_epa_storage_location.bay as bay, 
wpqa_wpsc_epa_storage_location.shelf as shelf, 
wpqa_wpsc_epa_storage_location.position as position, 
wpqa_wpsc_epa_location_status.locations as physical_location,
wpqa_wpsc_epa_boxinfo.box_destroyed as bd
FROM wpqa_wpsc_epa_boxinfo 
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id 
INNER JOIN wpqa_wpsc_epa_location_status ON wpqa_wpsc_epa_boxinfo.location_status_id = wpqa_wpsc_epa_location_status.id 
INNER JOIN wpqa_terms ON wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center 
WHERE wpqa_wpsc_epa_boxinfo.ticket_id = '" . $ticket_id . "'"
			);

			$tbl = '
<div class="table-responsive" style="overflow-x:auto;">
	<table id="tbl_templates_boxes" class="table table-striped table-bordered" cellspacing="5" cellpadding="5">
<thead>
  <tr>
    	  			<th class="datatable_header">ID</th>
    	  			<th class="datatable_header">Physical Location</th>
    	  			<th class="datatable_header">Assigned Location</th>
    	  			<th class="datatable_header">Digitization Center</th>
    	  		    <th class="datatable_header">Validation</th>
  </tr>
 </thead><tbody>
';

			foreach ($box_details as $info) {
			    $boxlist_dbid = $info->id;
			    $boxlist_id = $info->box_id;
			    $boxlist_dc = $info->digitization_center;
			    $boxlist_dc_val = strtoupper($info->digitization_center_slug);
			    $boxlist_aisle = $info->aisle;
			    $boxlist_bay = $info->bay;
				$boxlist_shelf = $info->shelf;
				$boxlist_position = $info->position;
			    $boxlist_physical_location = $info->physical_location;
			    $boxlist_val_sum = $info->val_sum;
			    $boxlist_doc_total = $info->doc_total;
			    
				if (($info->aisle == '0') || ($info->bay == '0') || ($info->shelf == '0') || ($info->position == '0')) {
				$boxlist_location = 'Currently Unassigned';
				} else {
                $boxlist_location = $info->aisle . 'A_' .$info->bay .'B_' . $info->shelf . 'S_' . $info->position .'P_'.$boxlist_dc_val;
				}
				
				if ($info->digitization_center == '') {
				$boxlist_dc_location = 'Currently Unassigned';
				} else {
                $boxlist_dc_location = $info->digitization_center;
				}
				$boxlist_unathorized_destruction = $info->ud;
				$boxlist_box_destroyed = $info->bd;
				$boxlist_freeze_sum = $info->freeze_sum;
			/*	
			if($boxlist_unathorized_destruction > 0) {
			$tbl .= '<tr class="wpsc_tl_row_item" style="background-color: #e7c3c3;">';
			} else {
			$tbl .= '<tr class="wpsc_tl_row_item">';
			}
			*/
			$tbl .= '<tr class="wpsc_tl_row_item">';
			
            if($boxlist_box_destroyed > 0 && $boxlist_freeze_sum == 0) {
                 $tbl .= '
            <td><a href="' . $subfolder_path . '/wp-admin/admin.php?page=boxdetails&pid=requestdetails&id=' . $boxlist_id . '" style="color:#FF0000 !important; text-decoration: line-through;">' . $boxlist_id . '</a>';

            } else if ($boxlist_box_destroyed > 0 && $boxlist_freeze_sum > 0){
                $tbl .= '
            <td><a href="' . $subfolder_path . '/wp-admin/admin.php?page=boxdetails&pid=requestdetails&id=' . $boxlist_id . '" style="color:#FF0000 !important;">' . $boxlist_id . '</a>';
                
            } else {
                $tbl .= '
            <td><a href="' . $subfolder_path . '/wp-admin/admin.php?page=boxdetails&pid=requestdetails&id=' . $boxlist_id . '">' . $boxlist_id . '</a>';

            }
            
            if($boxlist_box_destroyed > 0) {
            $tbl .= ' <span style="font-size: 1em; color: #FF0000;"><i class="fas fa-ban" title="Box Destroyed"></i></span>';
            }
            
            if($boxlist_unathorized_destruction > 0) {
            $tbl .= ' <span style="font-size: 1em; color: #8b0000;"><i class="fas fa-flag" title="Unauthorized Destruction"></i></span>';
            }
            
            if($boxlist_freeze_sum > 0) {
                $tbl .= ' <span style="font-size: 1em; color: #009ACD;"><i class="fas fa-snowflake" title="Freeze"></i></span>';
            }
            
            $tbl .= '</td>';
           
            $tbl .= '<td>' . $boxlist_physical_location . '</td>';   
			if (($boxlist_unathorized_destruction == 0)&&($boxlist_box_destroyed == 0)&&($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
            {
            if ($boxlist_location != 'Currently Unassigned' || $boxlist_dc_location != 'Not Unassigned') {
            $tbl .= '<td>' . $boxlist_location;
            if ($boxlist_dc_location != 'Not Assigned') {
            $tbl .= ' <a href="#" onclick="wpsc_get_inventory_editor(' . $boxlist_dbid . ')"><i class="fas fa-edit"></i></a>';
            }
            $tbl .= '</td>';
            $tbl .= '<td>' . $boxlist_dc_location . ' <a href="#" onclick="wpsc_get_digitization_editor_final(' . $boxlist_dbid . ')"><i class="fas fa-exchange-alt"></i></a></td>';

            
            } elseif ($boxlist_location == 'Currently Unassigned' && $boxlist_dc_location == 'Currently Unassigned') {
            $tbl .= '<td>' . $boxlist_location . '</td>';   
            $tbl .= '<td>' . $boxlist_dc_location . '</td>';
            }
            } else {
            $tbl .= '<td>' . $boxlist_location . '</td>';   
            $tbl .= '<td>' . $boxlist_dc_location . '</td>';
            }
            
            if($boxlist_doc_total == 0){
            $tbl .= '<td data-sort="4">-</td>';
            } else if($boxlist_val_sum != 0 && $boxlist_val_sum < $boxlist_doc_total){
            $tbl .= '<td data-sort="1"><span style="font-size: 1.3em; color: #FF8C00;"><i class="fas fa-times-circle" title="Not Validated"></i></span> ' . $boxlist_val_sum . '/' . $boxlist_doc_total . '</td>';
            } else if($boxlist_val_sum == 0 && $boxlist_val_sum < $boxlist_doc_total){
            $tbl .= '<td data-sort="2"><span style="font-size: 1.3em; color: #8b0000;"><i class="fas fa-times-circle" title="Not Validated"></i></span> ' . $boxlist_val_sum . '/' . $boxlist_doc_total . '</td>';
            } else if($boxlist_val_sum == $boxlist_doc_total){
            $tbl .= '<td data-sort="3"><span style="font-size: 1.3em; color: #008000;"><i class="fas fa-check-circle" title="Validated"></i></span> ' . $boxlist_val_sum . '/' . $boxlist_doc_total . '</td>';
            }

            
            $tbl .= '</tr>';

			}
			$tbl .= '</tbody></table></div>';

			echo $tbl;
            
?>			

<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>
<script>
 jQuery(document).ready(function() {
	 jQuery('#tbl_templates_boxes').DataTable({
		 "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]]
		});
} );

		function wpsc_get_digitization_editor_final(box_id){
		  wpsc_modal_open('Re-assign Digitization Center');
		  var data = {
		    action: 'wpsc_get_digitization_editor_final',
		    box_id: box_id
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  });  
		}
   
   		function wpsc_get_inventory_editor(box_id){
		  wpsc_modal_open('Assigned Location Editor');
		  var data = {
		    action: 'wpsc_get_inventory_editor',
		    box_id: box_id
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  });  
		}
		
	</script>