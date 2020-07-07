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
    
    //Set styles
      $style_barcode = array('border' => 0,'vpadding' => 'auto','hpadding' => 'auto','fgcolor' => array(0,0,0),'bgcolor' => false,'module_width' => 1,'module_height' => 1);
 
    //Function to get folderdocinfo_id from database
    function fetch_folderdocinfo()
    {
        global $wpdb;
        $array = array();
        
        $request_folderdocinfo = $wpdb->get_results("SELECT folderdocinfo_id 
        FROM wpqa_wpsc_epa_folderdocinfo, wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_storage_location 
        WHERE wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id AND wpqa_wpsc_epa_storage_location.id = wpqa_wpsc_epa_boxinfo.storage_location_id AND 
        ((index_level = 1 AND freeze = 1) OR (index_level = 1 AND aisle <> 0 AND bay <> 0 AND shelf <> 0 AND position <> 0 AND digitization_center <> 666)) AND 
        wpqa_wpsc_epa_boxinfo.ticket_id = " . $GLOBALS['id']);
        
        foreach($request_folderdocinfo as $folderdocinfo)
        {
            array_push($array, strtoupper($folderdocinfo->folderdocinfo_id));
        }
        
        return $array;
    }
    
    //Function to get folder/file title from database
    function fetch_title()
    {
        global $wpdb;
        $array = array();
        
        $request_title = $wpdb->get_results("SELECT title
        FROM wpqa_wpsc_epa_folderdocinfo, wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_storage_location 
        WHERE wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id AND wpqa_wpsc_epa_storage_location.id = wpqa_wpsc_epa_boxinfo.storage_location_id AND 
        ((index_level = 1 AND freeze = 1) OR (index_level = 1 AND aisle <> 0 AND bay <> 0 AND shelf <> 0 AND position <> 0 AND digitization_center <> 666)) AND 
        wpqa_wpsc_epa_boxinfo.ticket_id = " .$GLOBALS['id']);
        
        foreach($request_title as $folder_title)
        {
            array_push($array, $folder_title->title);
        }
        
        return $array;
    }
    
    //Set overall values for PDF
    $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $obj_pdf->SetCreator(PDF_CREATOR);
    $obj_pdf->SetTitle("Folder Labels - Paper Asset Tracking Tool");
    $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN,'',PDF_FONT_SIZE_MAIN));
    $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA,'',PDF_FONT_SIZE_DATA));
    $obj_pdf->SetDefaultMonospacedFont('helvetica');
    $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
    $obj_pdf->setPrintHeader(false);
    $obj_pdf->setPrintFooter(false);
    $obj_pdf->SetAutoPageBreak(true, 10);
    $obj_pdf->SetFont('helvetica', '', 11);
    //$obj_pdf->AddPage();
    
    //1D barcode coordinates
    $x_loc_1d = 35;
    $y_loc_1d = 70;
    //Folderdocinfo_id coordinates
    $x_loc_folderdocinfo = 50;
    $y_loc_folderdocinfo = 100;
    //"Title" coordinates
    $x_loc_title = 30;
    $y_loc_title = 130;
    //Folderdocinfo title coordinates
    $x_loc_folderdocinfo_title = 32;
    $y_loc_folderdocinfo_title = 130;

if ((preg_match('/^\d+$/', $GLOBALS['id'])) || (preg_match("/^([0-9]{7}-[0-9]{1,4}-01-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id']))) {
 
if (preg_match('/^\d+$/', $GLOBALS['id'])) {   
    //Obtain array of Box ID's
    $folderdocinfo_array = fetch_folderdocinfo();
    
    $title_array = fetch_title();
}

if (preg_match("/^([0-9]{7}-[0-9]{1,4}-01-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {

$folderdocinfo_array = explode(',', $GLOBALS['id']);

}

    //Begin for loop to iterate through folderdocinfo's arrayb
    for ($i = 0;$i < count($folderdocinfo_array);$i++)
    {
        //checks to see if folderdocinfo_id is empty, if so won't reprint file labels
        $folderdocinfo_new = $wpdb->get_row("SELECT folderdocinfo_id 
        FROM wpqa_wpsc_epa_folderdocinfo, wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_storage_location 
        WHERE wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id AND wpqa_wpsc_epa_storage_location.id = wpqa_wpsc_epa_boxinfo.storage_location_id AND 
        ((index_level = 1 AND freeze = 1) OR (index_level = 1 AND aisle <> 0 AND bay <> 0 AND shelf <> 0 AND position <> 0 AND digitization_center <> 666)) AND 
        folderdocinfo_id = '" .$folderdocinfo_array[$i]."'");
            $folderdoc_id = $folderdocinfo_new->folderdocinfo_id;
        
        if($folderdoc_id != '') {
        //Begin if statement to determine # of new pages based on length of array
        if ($folderdocinfo_array[$i] > 0)

        {
            $obj_pdf->AddPage();
        }
        
        //1D Box ID Barcode
        $obj_pdf->SetFont('helvetica', '', 30);
        $obj_pdf->write1DBarcode($folderdocinfo_array[$i], 'C128', $x_loc_1d, $y_loc_1d, '', 30, 0.7, $style_barcode, 'N');
        //Folderdocinfo_id
        $obj_pdf->SetXY($x_loc_folderdocinfo, $y_loc_folderdocinfo);
        $obj_pdf->SetFont('helvetica', '', 30);
        $obj_pdf->SetFillColor(255,255,255);
        $obj_pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'butt', 'dash' => 0, 'color' => array(255, 255, 255)));
        $obj_pdf->Cell(100, 0, $folderdocinfo_array[$i], 1, 0, 'C', 1);
        

        //Folderdocinfo title printout
        $obj_pdf->SetFont('helvetica', '', 30);
        
    if (preg_match("/^([0-9]{7}-[0-9]{1,4}-01-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {
        
$folderfile_info = $wpdb->get_row("SELECT title
        FROM wpqa_wpsc_epa_folderdocinfo
        WHERE folderdocinfo_id = '" .$folderdocinfo_array[$i]."'");
$txt = '<strong>Title:</strong> ' . ((strlen($folderfile_info->title) > 150) ? substr($folderfile_info->title, 0, 150) . "...": $folderfile_info->title);
    }
    
    if (preg_match('/^\d+$/', $GLOBALS['id'])) {
        $txt = '<strong>Title:</strong> ' . ((strlen($title_array[$i]) > 150) ? substr($title_array[$i], 0, 150) . "...": $title_array[$i]);
    }
    
        $obj_pdf->MultiCell(145, 0, $txt, 0, 'L', 0, 0, $x_loc_folderdocinfo_title, $y_loc_folderdocinfo_title, true, 0, true);
    }
    }
    
    //Generate PDF
    $obj_pdf->Output('folder_seperator_printout.pdf', 'I');
    
} else {
echo "Pass a valid ID in URL";
}

} else {
    //Define message for when no ID exists in URL
    echo "Pass request ID in URL";
}

?>