<?php

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');
$subfolder_path = site_url( '', 'relative'); 

//Check to see if URL has the correct Request ID
if (isset($_GET['id']))
{
    //Set SuperGlobal ID variable to be used in all functions below
    $GLOBALS['id'] = $_GET['id'];
    
    //Pull in the TCPDF library
    require_once ('tcpdf/tcpdf.php');
    
    //Set styles
      $style_barcode = array('border' => 0,'vpadding' => 'auto','hpadding' => 'auto','fgcolor' => array(0,0,0),'bgcolor' => false,'module_width' => 1,'module_height' => 1);
    
    //Set overall values for PDF
    $obj_pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $obj_pdf->SetCreator(PDF_CREATOR);
    $obj_pdf->SetTitle("Box List Labels - Paper Asset Tracking Tool");
    $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN,'',PDF_FONT_SIZE_MAIN));
    $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA,'',PDF_FONT_SIZE_DATA));
    $obj_pdf->SetDefaultMonospacedFont('helvetica');
    $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $obj_pdf->SetMargins(6, '10', 5);
    $obj_pdf->setPrintHeader(false);
    $obj_pdf->setPrintFooter(false);
    $obj_pdf->SetAutoPageBreak(true, 10);
    $obj_pdf->SetFont('helvetica', '', 11);
        
        $record_schedules = $wpdb->get_results("SELECT DISTINCT wpqa_wpsc_epa_boxinfo.record_schedule_id as record_schedule_id, wpqa_epa_record_schedule.Record_Schedule_Number as rsnum 
FROM wpqa_epa_record_schedule, wpqa_wpsc_epa_boxinfo 
WHERE wpqa_wpsc_epa_boxinfo.record_schedule_id = wpqa_epa_record_schedule.id AND wpqa_wpsc_epa_boxinfo.ticket_id =" .$GLOBALS['id']);
//print_r($record_schedules);

foreach($record_schedules as $rs_num)
    {
        
$box_list = $wpdb->get_results("SELECT wpqa_wpsc_epa_program_office.office_acronym as program_office, wpqa_wpsc_epa_folderdocinfo.index_level as index_level, wpqa_wpsc_epa_folderdocinfo.folderdocinfo_id as id, SUBSTR(wpqa_wpsc_epa_boxinfo.box_id, INSTR(wpqa_wpsc_epa_boxinfo.box_id, '-') + 1) as box, wpqa_wpsc_epa_folderdocinfo.title as title, wpqa_wpsc_epa_folderdocinfo.date as date, wpqa_wpsc_epa_folderdocinfo.site_name as site, wpqa_wpsc_epa_boxinfo.lan_id as contact, wpqa_wpsc_epa_folderdocinfo.source_format as source_format 
FROM wpqa_wpsc_epa_folderdocinfo, wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_program_office  
WHERE 
wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id AND 
wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.office_code AND 
wpqa_wpsc_epa_boxinfo.record_schedule_id = " .$rs_num->record_schedule_id ." AND
wpqa_wpsc_epa_boxinfo.ticket_id = ".$GLOBALS['id']
);
        
$box_list_get_count = $wpdb->get_row("SELECT count(distinct wpqa_wpsc_epa_folderdocinfo.box_id) as box_count
FROM wpqa_wpsc_epa_folderdocinfo, wpqa_wpsc_epa_boxinfo, wpqa_epa_record_schedule
WHERE 
wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id AND 
wpqa_wpsc_epa_boxinfo.record_schedule_id = " .$rs_num->record_schedule_id . " AND
wpqa_wpsc_epa_boxinfo.ticket_id = ".$GLOBALS['id']);
//print_r($box_list_get_count);

$box_list_count = $box_list_get_count->box_count;
//echo $box_list_count;

$program_office_array_id = array();
        
$boxlist_get_po = $wpdb->get_results("SELECT DISTINCT wpqa_wpsc_epa_program_office.office_acronym as program_office
FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_program_office
WHERE wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.office_code AND wpqa_wpsc_epa_boxinfo.ticket_id = " .$GLOBALS['id']);
//print_r($boxlist_get_po);

foreach ($boxlist_get_po as $item) {
	array_push($program_office_array_id, $item->program_office);
	}
	
$boxlist_po = join(", ", $program_office_array_id);


       $style_barcode = array(
        'border' => 0,
        'vpadding' => 'auto',
        'hpadding' => 'auto',
        'fgcolor' => array(
            0,
            0,
            0
        ),
        'bgcolor' => false,
        'module_width' => 1,
         'module_height' => 1 
         );
         
//$str_length = 7;
//$request_id = substr("000000{$GLOBALS['id']}", -$str_length);
$request_id = Patt_Custom_Func::ticket_to_request_id($GLOBALS['id']);

// $request_key = $wpdb->get_row( "SELECT ticket_auth_code FROM wpqa_wpsc_ticket WHERE id = " . $GLOBALS['id']);
        $args = [
            'select' => 'DISTINCT wpqa_wpsc_epa_program_office.office_acronym as program_office',
            'where' => ['id', $GLOBALS['id']],
        ];
        $wpqa_wpsc_ticket = new WP_CUST_QUERY('wpqa_wpsc_ticket');
        $request_key = $wpqa_wpsc_ticket->get_row($args, false);


//$key = $request_key->ticket_auth_code;

$url = 'http://' . $_SERVER['SERVER_NAME'] . $subfolder_path .'/wp-admin/admin.php?page=wpsc-tickets&id=' . $request_id;

$request_id_barcode =  $obj_pdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', '', 30, $style_barcode, 'N'));

//ECMS/SEMS indicator
$get_ecms_sems = $wpdb->get_row("SELECT a.request_id, b.meta_key, b.meta_value as ecms_sems
    FROM wpqa_wpsc_ticket a
    INNER JOIN wpqa_wpsc_ticketmeta b ON b.ticket_id = a.id
    WHERE b.meta_key = 'super_fund' AND a.id = " . $GLOBALS['id']);
$ecms_sems_indicator = $get_ecms_sems->ecms_sems;

$ecms_sems = '';
if($ecms_sems_indicator == 'true') {
    $ecms_sems = 'SEMS';
}
else {
    $ecms_sems = 'ECMS';
}

$tbl = '

<table style="width:745px">
  <tr>
    <td><h1 style="font-size: 40px">Box List</h1></td>
    <td><strong>Record Schedule:</strong> '.$rs_num->rsnum.'<br /><br /><strong>Total Boxes in Accession:</strong> '.$box_list_count.'<br /><br /><strong>Program Office:</strong> '.$boxlist_po.' <br /><br /><strong>ECMS or SEMS:</strong> '.$ecms_sems.'</td>
    <td align="right"><tcpdf method="write2DBarcode" params="'.$request_id_barcode.'" /><strong>&nbsp; &nbsp; &nbsp; &nbsp; '.$request_id.'</strong><br /></td>
  </tr>
</table>

<table style="width: 638px;" cellspacing="0" nobr="true">
  <tr>
    <th style="border: 1px solid #000000; width: 180px; background-color: #f5f5f5; font-weight: bold;">ID</th>
    <th style="border: 1px solid #000000; width: 45px; background-color: #f5f5f5; font-weight: bold;">Box #</th>
    <th style="border: 1px solid #000000; width: 45px; background-color: #f5f5f5; font-weight: bold;">Index Level</th>
    <th style="border: 1px solid #000000; width: 150px; background-color: #f5f5f5; font-weight: bold;">Title</th>
    <th style="border: 1px solid #000000; width: 75px; background-color: #f5f5f5; font-weight: bold;">Date</th>
    <th style="border: 1px solid #000000; width: 120px; background-color: #f5f5f5; font-weight: bold;">Contact</th>
    <th style="border: 1px solid #000000; width: 80px; background-color: #f5f5f5; font-weight: bold;">Source Format</th>    
    <th style="border: 1px solid #000000; width: 65px; background-color: #f5f5f5; font-weight: bold;">Program Office</th>  
  </tr>
';

foreach($box_list as $info){
    $boxlist_id = $info->id;
    $boxlist_barcode =  $obj_pdf->serializeTCPDFtagParameters(array($boxlist_id, 'C128', '', '', 62, 20, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>1, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
    $boxlist_box = $info->box;
    $boxlist_title = $info->title;
    $boxlist_date = $info->date;
    $boxlist_site = $info->site;
    $boxlist_contact = $info->contact;
    $boxlist_sf = $info->source_format;
    $boxlist_po = $info->program_office;
    $boxlist_il = $info->index_level;
    $boxlist_il_val = '';
    if($boxlist_il == 1) {
        $boxlist_il_val = "(Folder)"; 
        
    } else {
        $boxlist_il_val = "(File)";
    }
    
    $tbl .= '<tr>
            <td style="border: 1px solid #000000; width: 180px;"><tcpdf method="write1DBarcode" params="'.$boxlist_barcode.'" /></td>
            <td style="border: 1px solid #000000; width: 45px;">'.$boxlist_box.'</td>
            <td style="border: 1px solid #000000; width: 45px;">'.$boxlist_il_val.'</td>
            <td style="border: 1px solid #000000; width: 150px;">'.$boxlist_title.'</td>
            <td style="border: 1px solid #000000; width: 75px;">'.$boxlist_date.'</td>
            <td style="border: 1px solid #000000; width: 120px;">'.$boxlist_contact.'</td>
            <td style="border: 1px solid #000000; width: 80px;">'.$boxlist_sf.'</td>
            <td style="border: 1px solid #000000; width: 65px;">'.$boxlist_po.'</td>
            </tr>';
    
}
$tbl .= '</table>';

$obj_pdf->AddPage();
$obj_pdf->writeHTML($tbl, true, false, false, false, '');

    }
    
    //Generate PDF
    $obj_pdf->Output('patt_box_list_printout.pdf', 'I');
}

else
{
    //Define message for when no ID exists in URL
    echo "Pass request ID in URL";
}

?>
