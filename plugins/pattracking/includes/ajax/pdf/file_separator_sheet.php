<?php

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

//Check to see if URL has the correct Request ID
if (isset($_GET['id']))
{
    //Set SuperGlobal ID variable to be used in all functions below
    $GLOBALS['id'] = $_GET['id'];
    
    //Pull in the TCPDF library
    require_once ('tcpdf/tcpdf.php');
    
    //Set overall values for PDF
    $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $obj_pdf->SetCreator(PDF_CREATOR);
    $obj_pdf->SetTitle("File Labels - Paper Asset Tracking Tool");
    $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN,'',PDF_FONT_SIZE_MAIN));
    $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA,'',PDF_FONT_SIZE_DATA));
    $obj_pdf->SetDefaultMonospacedFont('helvetica');
    $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
    $obj_pdf->setPrintHeader(false);
    $obj_pdf->setPrintFooter(false);
    $obj_pdf->SetAutoPageBreak(true, 10);
    $obj_pdf->SetFont('helvetica', '', 11);

if ((preg_match('/^\d+$/', $GLOBALS['id'])) || (preg_match("/^([0-9]{7}-[0-9]{1,4}-02-[0-9]{1,4}(-[a][0-9]{1,4})?)(?:,\s*(?1))*$/", $GLOBALS['id']))) {

if (preg_match('/^\d+$/', $GLOBALS['id'])) {
    $box_ids = $wpdb->get_results("
    SELECT DISTINCT a.id
    FROM wpqa_wpsc_epa_boxinfo a
    LEFT JOIN wpqa_wpsc_epa_folderdocinfo b ON b.box_id = a.id
    RIGHT JOIN wpqa_wpsc_epa_storage_location s ON a.storage_location_id = s.id
    INNER JOIN wpqa_wpsc_epa_folderdocinfo_files c ON c.folderdocinfo_id = b.folderdocinfo_id
    WHERE ((c.index_level = 2 AND c.freeze = 1) OR (c.index_level = 2 AND s.aisle <> 0 AND s.bay <> 0 AND s.shelf <> 0 AND s.position <> 0 AND s.digitization_center <> 666)) AND 
    a.ticket_id = " .$GLOBALS['id']);

//print_r($box_ids);
 
    foreach($box_ids as $item)
    {

$folderfile_info = $wpdb->get_results("SELECT d.folderdocinfofile_id, d.title
FROM wpqa_wpsc_epa_folderdocinfo a 
INNER JOIN wpqa_wpsc_epa_boxinfo b ON b.id = a.box_id
INNER JOIN wpqa_wpsc_epa_storage_location c ON c.id = b.storage_location_id
INNER JOIN wpqa_wpsc_epa_folderdocinfo_files d ON d.folderdocinfo_id = a.folderdocinfo_id
WHERE ((d.index_level = 2 AND c.aisle <> 0 AND c.bay <> 0 AND c.shelf <> 0 AND c.position <> 0 AND c.digitization_center <> 666) OR 
(d.index_level = 2 AND d.freeze = 1)) AND
a.box_id = " .$item->id);

//print_r($folderfile_info);

$maxcols = 3;
$i = 0;

$batch_of = 30;

$batch = array_chunk($folderfile_info, $batch_of);
//print_r($batch);
foreach($batch as $b) {

//set table margins
$obj_pdf->SetMargins(8,16,0);
//$obj_pdf->SetHeaderMargin(10);
//$obj_pdf->SetFooterMargin(16);

//Open the table and its first row

$tbl   =  '<style>
                .tableWithOuterBorder{
                    border-spacing: 80px 2px;
                }
                </style>';
                
$tbl .= '<table class="tableWithOuterBorder" style="width: 638px; font-size: 9px;" cellspacing="10" nobr="true">';
$tbl .= '<tr>';

foreach($b as $info){

    $folderfile_id = $info->folderdocinfofile_id;
    $folderfile_barcode =  $obj_pdf->serializeTCPDFtagParameters(array($folderfile_id, 'C128', '', '', 57, 17, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>1, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
    $folderfile_title = $info->title;
    $folderfile_title_truncate = (strlen($folderfile_title) > 30) ? substr($folderfile_title, 0, 30) . '...' : $folderfile_title;

    if ($i == $maxcols) {
        $i = 0;
        $tbl .= '</tr><tr>';
    }

    $tbl .= '<td style="width: 180px;"><tcpdf method="write1DBarcode" params="'.$folderfile_barcode.'" /><span style="text-align: center;">'. $folderfile_title_truncate .'</span></td>';

    $i++;

}

//Add empty <td>'s to even up the amount of cells in a row:
while ($i <= $maxcols-1) {
    $tbl .= '<td style="width: 180px;">&nbsp;</td>';
    $i++;
}

//Close the table row and the table
$tbl .= '</tr>';
$tbl .= '</table>';

$obj_pdf->AddPage();

$obj_pdf->writeHTML($tbl, true, false, false, false, '');

}       //endforeach
    
} 

} //endforeach_regex ticket id

if (preg_match("/^([0-9]{7}-[0-9]{1,4}-02-[0-9]{1,4}(-[a][0-9]{1,4})?)(?:,\s*(?1))*$/", $GLOBALS['id'])) {

$final_array = array();

$folderfile_array= explode(',', $GLOBALS['id']);

foreach($folderfile_array as $item) {

$folderfile_info = $wpdb->get_row("SELECT d.folderdocinfofile_id, d.title
FROM wpqa_wpsc_epa_folderdocinfo a 
INNER JOIN wpqa_wpsc_epa_boxinfo b ON b.id = a.box_id
INNER JOIN wpqa_wpsc_epa_storage_location c ON c.id - b.storage_location_id
INNER JOIN wpqa_wpsc_epa_folderdocinfo_files d ON d.folderdocinfo_id = a.folderdocinfo_id
WHERE ((d.index_level = 2 AND c.aisle <> 0 AND c.bay <> 0 AND c.shelf <> 0 AND c.position <> 0 AND c.digitization_center <> 666) OR (d.index_level = 2 AND d.freeze = 1)) AND
d.folderdocinfofile_id = '" .$item."'");

$parent = new stdClass;
$parent->folderdocinfofile_id = $folderfile_info->folderdocinfofile_id;
$parent->title = $folderfile_info->title;
$final_array[] = $parent;

}

$maxcols = 3;
$i = 0;

$batch_of = 30;

$batch = array_chunk($final_array, $batch_of);

//print_r($batch);
foreach($batch as $b) {

//Open the table and its first row
$tbl   =  '<style>
                .tableWithOuterBorder{
                    border-spacing: 80px 2px;
                }
                </style>';
                
$tbl .= '<table class="tableWithOuterBorder" style="width: 638px; font-size: 9px;" cellspacing="10" nobr="true">';
$tbl .= '<tr>';

foreach($b as $info){

    $folderfile_id = $info->folderdocinfofile_id;
 $folderfile_barcode =  $obj_pdf->serializeTCPDFtagParameters(array($folderfile_id, 'C128', '', '', 57, 17, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>1, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
$folderfile_title = $info->title;
    $folderfile_title_truncate = (strlen($folderfile_title) > 30) ? substr($folderfile_title, 0, 30) . '...' : $folderfile_title;

    if ($i == $maxcols) {
        $i = 0;
        $tbl .= '</tr><tr>';
    }

    $tbl .= '<td style="width: 180px;"><tcpdf method="write1DBarcode" params="'.$folderfile_barcode.'" /><span style="text-align: center;">'. $folderfile_title_truncate .'</span></td>';

    $i++;

}

//Add empty <td>'s to even up the amount of cells in a row:
while ($i <= $maxcols-1) {
    $tbl .= '<td style="width: 180px;">&nbsp;</td>';
    $i++;
}

//Close the table row and the table
$tbl .= '</tr>';
$tbl .= '</table>';

$obj_pdf->AddPage();

$obj_pdf->writeHTML($tbl, true, false, false, false, '');

}

}  //end box id regex

    //Generate PDF
    $obj_pdf->Output('patt_file_seperator_printout.pdf', 'I');

} else {
echo "Pass a valid ID in URL";
}

} else {
    //Define message for when no ID exists in URL
    echo "Pass request ID in URL";
}

?>
