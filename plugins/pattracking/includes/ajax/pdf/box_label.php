<?php

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');
include($path . 'wp-content/plugins/pattracking/includes/class-wppatt-custom-function.php');
$subfolder_path = site_url( '', 'relative'); 

global $wpdb;

//Check to see if URL has the correct Request ID
if (isset($_GET['id']))
{

    //Set SuperGlobal ID variable to be used in all functions below
    $GLOBALS['id'] = $_GET['id'];

    //Function to obtain asset_tag value from database
    function fetch_request_id()
    {
        global $wpdb;
        $request_id = $wpdb->get_row( "SELECT * FROM wpqa_wpsc_ticket WHERE id = " . $GLOBALS['id']);

        $asset_id = $request_id->request_id;

        return $asset_id;
    }

    //Function to obtain serial number (box ID) from database based on Request ID
    function fetch_box_id()
    {
        global $wpdb;
        $array = array();
        
        $box_result = $wpdb->get_results( "SELECT * FROM wpqa_wpsc_epa_boxinfo a INNER JOIN wpqa_wpsc_epa_storage_location b ON a.storage_location_id = b.id WHERE b.aisle <> 0 AND b.bay <> 0 AND b.shelf <> 0 AND b.position <> 0 AND b.digitization_center <> 666 AND a.ticket_id = " . $GLOBALS['id']);

        foreach ( $box_result as $box )
            {
                array_push($array, $box->box_id);
            }

        return $array;

    }
    
    //Function to obtain box details from box ID
    function fetch_box_id_a()
    {
        global $wpdb;

        $boxidArray = explode(',', $GLOBALS['id']);

        $array = array();
        
        $box_result = $wpdb->get_results( "SELECT a.box_id as box_id FROM wpqa_wpsc_epa_boxinfo a LEFT JOIN wpqa_wpsc_epa_storage_location b ON a.storage_location_id = b.id WHERE b.aisle <> 0 AND b.bay <> 0 AND b.shelf <> 0 AND b.position <> 0 AND b.digitization_center <> 666");

        foreach ( $box_result as $box )
            {
            
                array_push($array, $box->box_id);
            }

        $filteredresult = array_intersect($array, $boxidArray);
        
        return array_values($filteredresult);
        
    }
    
    //Function to obtain location value from database
    //don't draw pdf page if location = 'Not Assigned'
    function fetch_location()
    {
        global $wpdb;
        $array = array();
        $box_digitization_center = $wpdb->get_results( "
        SELECT wpqa_terms.name as digitization_center
        FROM wpqa_wpsc_epa_boxinfo
        INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
        INNER JOIN wpqa_terms ON wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center
        WHERE
        wpqa_wpsc_epa_storage_location.aisle <> 0 AND 
        wpqa_wpsc_epa_storage_location.bay <> 0 AND 
        wpqa_wpsc_epa_storage_location.shelf <> 0 AND 
        wpqa_wpsc_epa_storage_location.position <> 0 AND 
        wpqa_wpsc_epa_storage_location.digitization_center <> 666 AND
        wpqa_wpsc_epa_boxinfo.ticket_id = " . $GLOBALS['id']);

                foreach ( $box_digitization_center as $location )
            {
                array_push($array, strtoupper($location->digitization_center));
            }

        return $array;
    }
    
    
    //Function to obtain program office from database
    function fetch_program_office()
    {
        global $wpdb;
        $array = array();

        $request_program_office = $wpdb->get_results("SELECT b.organization_acronym FROM wpqa_wpsc_epa_boxinfo a
        INNER JOIN wpqa_wpsc_epa_program_office b ON a.program_office_id = b.office_code
        INNER JOIN wpqa_wpsc_epa_storage_location c ON a.storage_location_id = c.id
        WHERE a.program_office_id = b.office_code AND 
        c.aisle <> 0 AND 
        c.bay <> 0 AND 
        c.shelf <> 0 AND 
        c.position <> 0 AND 
        c.digitization_center <> 666 AND
        a.ticket_id = " . $GLOBALS['id']);
        
        
        
        foreach($request_program_office as $program_office)
        {
            array_push($array, strtoupper($program_office->organization_acronym));
        }
        
        return $array;
    }
    
    //Function to obtain shelf from database
    function fetch_aisle_bay_shelf_position()
    {
        global $wpdb;
        $array = array();
        //$request_shelf = $wpdb->get_results("SELECT aisle, bay, shelf, position FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_program_office WHERE wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.id AND ticket_id = " . $GLOBALS['id']);

$request_shelf = $wpdb->get_results("
SELECT
wpqa_wpsc_epa_boxinfo.id as box_data_id,
wpqa_wpsc_epa_storage_location.aisle as aisle, 
wpqa_wpsc_epa_storage_location.bay as bay, 
wpqa_wpsc_epa_storage_location.shelf as shelf, 
wpqa_wpsc_epa_storage_location.position as position,
(SELECT UPPER(wpqa_terms.slug) FROM wpqa_terms, wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_storage_location WHERE wpqa_wpsc_epa_storage_location.digitization_center = wpqa_terms.term_id AND wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id AND wpqa_wpsc_epa_boxinfo.id = box_data_id) as digitization_center
FROM wpqa_wpsc_epa_boxinfo 
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id 
INNER JOIN wpqa_wpsc_epa_location_status ON wpqa_wpsc_epa_boxinfo.location_status_id = wpqa_wpsc_epa_location_status.id
INNER JOIN wpqa_terms ON wpqa_terms.term_id = wpqa_wpsc_epa_boxinfo.box_status      
WHERE 
aisle <> 0 AND 
bay <> 0 AND 
shelf <> 0 AND 
position <> 0 AND 
digitization_center <> 666 AND
wpqa_wpsc_epa_boxinfo.ticket_id = " . $GLOBALS['id']);

        
        foreach($request_shelf as $location)
        {
            array_push($array, strtoupper($location->aisle.'A_'.$location->bay.'B_'.$location->shelf.'S_'.$location->position.'P_'.$location->digitization_center));
        }
        
        return $array;
    }
    
    //Function to obtain create month and year from database
    function fetch_create_date()
    {
        global $wpdb;
        $request_create_date = $wpdb->get_row( "SELECT a.date_created FROM wpqa_wpsc_ticket a 
INNER JOIN wpqa_wpsc_epa_boxinfo b ON b.ticket_id = a.id
INNER JOIN wpqa_wpsc_epa_storage_location c ON b.storage_location_id = c.id
        WHERE
        c.aisle <> 0 AND 
        c.bay <> 0 AND 
        c.shelf <> 0 AND 
        c.position <> 0 AND 
        c.digitization_center <> 666 AND
        a.id = " . $GLOBALS['id']);
        
        $create_date = $request_create_date->date_created;
        $date = strtotime($create_date);
        
        return strtoupper(date('M y', $date));
    }

    //Function to obtain request key
    function fetch_request_key()
    {
        global $wpdb;
        $request_key = $wpdb->get_row( "SELECT ticket_auth_code FROM wpqa_wpsc_ticket WHERE id = " . $GLOBALS['id']);
        
        $key = $request_key->ticket_auth_code;
        
        return $key;
    }

    //Function to obtain request key
    function fetch_box_count()
    {
        global $wpdb;
        $box_count = $wpdb->get_row( "SELECT COUNT(ticket_id) as count FROM wpqa_wpsc_epa_boxinfo WHERE ticket_id = " . $GLOBALS['id']);
        
        $count_val = $box_count->count;
        
        return $count_val;
    }
    
    //Function to pull ECMS/SEMS
    function fetch_ecms_sems(){
        global $wpdb;
        $get_ecms_sems = $wpdb->get_row("SELECT a.request_id, b.meta_key, b.meta_value as ecms_sems
        FROM wpqa_wpsc_ticket a
        INNER JOIN wpqa_wpsc_ticketmeta b ON b.ticket_id = a.id
        WHERE b.meta_key = 'super_fund' AND a.id = " . $GLOBALS['id']);
        $ecms_sems = $get_ecms_sems->ecms_sems;
        
        return $ecms_sems;
    }
    
    //Pull in the TCPDF library
    require_once ('tcpdf/tcpdf.php');

    //Set styles
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
        $style_line = array(
            'width' => 1,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => '0',
            'phase' => 10,
            'color' => array(
                0,
                0,
                0
            )
        );
        $style_box_dash = array(
            'width' => 1,
            'cap' => 'butt',
            'join' => 'round',
            'dash' => '2,10',
            'color' => array(
                211,
                211,
                211
            )
        );

        //Set overall values for PDF
        $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $obj_pdf->SetCreator(PDF_CREATOR);
        $obj_pdf->SetTitle("Box Labels - Paper Asset Tracking Tool");
        $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $obj_pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $obj_pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        $obj_pdf->SetDefaultMonospacedFont('helvetica');
        $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
        $obj_pdf->setPrintHeader(false);
        $obj_pdf->setPrintFooter(false);
        $obj_pdf->SetAutoPageBreak(true, 10);
        $obj_pdf->SetFont('helvetica', '', 11);
        
$box_not_assigned = fetch_location();
if($box_not_assigned != 'Not Assigned') {

if ((preg_match('/^\d+$/', $GLOBALS['id'])) || (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id']))) {
    
        $obj_pdf->AddPage();

if (preg_match('/^\d+$/', $GLOBALS['id'])) {
        //Obtain array of Box ID's
        $box_array = fetch_box_id();
        $box_location = fetch_location();
        $box_program_office = fetch_program_office();
        $box_location_position = fetch_aisle_bay_shelf_position();
        $box_date = fetch_create_date();
        $box_count = fetch_box_count();
        $box_ecms_sems = fetch_ecms_sems();
}

if (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {  
       $box_array = fetch_box_id_a();
}

        //Set count to 0. This count determine odd or even components of the array
        $c = 0;
        
        //Begin for loop to iterate through Box ID's array
        for ($i = 0;$i < count($box_array);$i++)
        {
            
if (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {           

        $asset_ticket_id = $wpdb->get_row( "SELECT DISTINCT ticket_id FROM wpqa_wpsc_epa_boxinfo WHERE box_id = '" . $box_array[$i] ."'");
                
        $asset_request_id = $wpdb->get_row( "SELECT * FROM wpqa_wpsc_ticket WHERE id = " . $asset_ticket_id->ticket_id);

        $asset_id = $asset_request_id->request_id;
        
        
        $box_digitization_center = $wpdb->get_row( "
        SELECT wpqa_terms.name as digitization_center
        FROM wpqa_wpsc_epa_boxinfo
        INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
        INNER JOIN wpqa_terms ON wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center
        WHERE wpqa_wpsc_epa_boxinfo.box_id = '" . $box_array[$i] ."'");
        
        $box_location_a = strtoupper($box_digitization_center->digitization_center);


        $request_program_office = $wpdb->get_row("SELECT organization_acronym as acronym FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_program_office WHERE wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.office_code AND box_id = '" . $box_array[$i] ."'");
        
        $box_program_office_a = $request_program_office->acronym;
    
        //$request_location_position = $wpdb->get_row("SELECT aisle, bay, shelf, position FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_program_office WHERE wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.id AND box_id = '" . $box_array[$i] ."'");
        $request_location_position = $wpdb->get_row("SELECT wpqa_wpsc_epa_storage_location.aisle as aisle, wpqa_wpsc_epa_storage_location.bay as bay, wpqa_wpsc_epa_storage_location.shelf as shelf, wpqa_wpsc_epa_storage_location.position as position,
            UPPER(wpqa_terms.slug) as digitization_center FROM wpqa_wpsc_epa_storage_location, wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_program_office, wpqa_terms  WHERE wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center AND wpqa_wpsc_epa_storage_location.id = wpqa_wpsc_epa_boxinfo.storage_location_id AND wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.office_code AND box_id =  '" . $box_array[$i] ."'");
        
        $request_location_position_a = $request_location_position->aisle.'A_'.$request_location_position->bay.'B_'.$request_location_position->shelf.'S_'.$request_location_position->position.'P_'.$request_location_position->digitization_center;

        $request_create_date = $wpdb->get_row( "SELECT date_created FROM wpqa_wpsc_ticket WHERE id = " . $asset_ticket_id->ticket_id);
        
        $create_date = $request_create_date->date_created;
        $date = strtotime($create_date);
        
        $box_date_a = strtoupper(date('M y', $date));
        
        $get_box_count = $wpdb->get_row( "SELECT COUNT(ticket_id) as count FROM wpqa_wpsc_epa_boxinfo WHERE ticket_id = " . $asset_ticket_id->ticket_id);
        
        $box_count_a = $get_box_count->count;
        
}

            //Begin if statement to determine where to add new pages
            if ($c == 2)

            {

                $obj_pdf->AddPage();

                $c = 0;
            }

            $c++;
            //Define cordinates which need to be different for odd or even components of the Box ID array
            if ($i % 2 == 0)
            {
                // Even
                //1D barcode coordinates
                $x_loc_1d = 55;
                $y_loc_1d = 70;
                //QR barcode coordinates
                $x_loc_2d = 155;
                $y_loc_2d = 12;
                //Box x of y text coordinates
                $x_loc_b = 40;
                $y_loc_b = 115;
                //Box ID Printout coordinates
                $x_loc_c = 80;
                $y_loc_c = 97;
                //Line seperator coordinates
                $x_loc_l1 = 39;
                $y_loc_l1 = 110;
                $x_loc_l2 = 195;
                $y_loc_l2 = 110;
                //Box_a RFID coordinates
                $x_loc_ba1 = 11;
                $y_loc_ba1 = 55;
                $x_loc_la2 = 23;
                $y_loc_la2 = 68.5;
                //Location Coordinates
                $x_loc_l = 152;
                $y_loc_l = 90;
                //Creation Date Coordinates
                $x_loc_cd = 79;
                $y_loc_cd = 21;
                //Request ID Coordinates
                $x_loc_rid = 165;
                $y_loc_rid = 50;
                //EPC Vertical Text Coordinates
                $x_loc_rfid = 12;
                $y_loc_rfid = 124;
                //Digitization center box regular border
                $x_loc_digi_box_regular = 164;
                $y_loc_digi_box_regular = 89;
                //Digitization center box dashed border
                $x_loc_digi_box_dashed = 148.5;
                $y_loc_digi_box_dashed = 87;
                //Black rectangle containing program office and month/year of request
                $x_loc_black_rectangle = 10;
                $y_loc_black_rectangle = 15;
                //White rectangle containing program office
                $x_loc_white_rectangle = 19;
                $y_loc_white_rectangle = 20;
                //Program office
                $x_loc_program_office = 38;
                $y_loc_program_office = 23;
                //Bay
                $x_loc_bay = 134;
                $y_loc_bay = 115;
                //Shelf
                $x_loc_shelf = 161;
                $y_loc_shelf = 115;
                //Dashed border around aisle/bay/shelf/position
                $x_loc_dashed_border = 86;
                $y_loc_dashed_border = 113;
                //aisle/bay/shelf/position
                $x_loc_box_position = 88.5;
                $y_loc_box_position = 115;
                //ECMS/SEMS
                $x_loc_ecms_sems = 38.5;
                $y_loc_ecms_sems = 105;
            }
            else
            {
                // Odd
                //1D barcode coordinates
                $x_loc_1d = 55;
                $y_loc_1d = 200;
                //QR barcode coordinates
                $x_loc_2d = 155;
                $y_loc_2d = 142;
                //Box x of y text coordinates
                $x_loc_b = 40;
                $y_loc_b = 245;
                //Box ID Printout coordinates
                $x_loc_c = 80;
                $y_loc_c = 227;
                //Line seperator coordinates
                $x_loc_l1 = 39;
                $y_loc_l1 = 240;
                $x_loc_l2 = 195;
                $y_loc_l2 = 240;
                //Box_a RFID coordinates
                $x_loc_ba1 = 11;
                $y_loc_ba1 = 185;
                $x_loc_la2 = 23;
                $y_loc_la2 = 70;
                //Location Coordinates
                $x_loc_l = 152;
                $y_loc_l = 220;                
                //Creation Date Coordinates
                $x_loc_cd = 79;
                $y_loc_cd = 152;
                //Request ID Coordinates
                $x_loc_rid = 165;
                $y_loc_rid = 180;
                //EPC Vertical Text Coordinates
                $x_loc_rfid = 12;
                $y_loc_rfid = 254;
                //Digitization center box regular border
                $x_loc_digi_box_regular = 164;
                $y_loc_digi_box_regular = 219;
                //Digitization center box dashed border
                $x_loc_digi_box_dashed = 148.5;
                $y_loc_digi_box_dashed = 217;
                //Black rectangle containing program office and month/year of request
                $x_loc_black_rectangle = 10;
                $y_loc_black_rectangle = 145;
                //White rectangle containing program office
                $x_loc_white_rectangle = 19;
                $y_loc_white_rectangle = 150;
                //Program office
                $x_loc_program_office = 38;
                $y_loc_program_office = 153;
                //Bay
                $x_loc_bay = 134;
                $y_loc_bay = 245;
                //Shelf
                $x_loc_shelf = 161;
                $y_loc_shelf = 245;
                //Dashed border around aisle/bay/shelf/position
                $x_loc_dashed_border = 86;
                $y_loc_dashed_border = 243;
                //aisle/bay/shelf/position
                $x_loc_box_position = 88.5;
                $y_loc_box_position = 245;
                //ECMS/SEMS
                $x_loc_ecms_sems = 40;
                $y_loc_ecms_sems = 235;
            }
            //Determine box count out of total
            
            $initial_box = substr($box_array[$i], strpos($box_array[$i], "-") + 1);
            
if (preg_match('/^\d+$/', $GLOBALS['id'])) {
            $total_box = $box_count;
}

if (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {
           $total_box = $box_count_a;
}

            $obj_pdf->SetFont('helvetica', 'B', 25);
            $obj_pdf->Text($x_loc_b, $y_loc_b, "Box " . $initial_box . " of " . $total_box);
            $obj_pdf->Line($x_loc_l1, $y_loc_l1, $x_loc_l2, $y_loc_l2, $style_line);
            //RFID Box Location
            //$obj_pdf->Rect($x_loc_ba1, $y_loc_ba1, $x_loc_la2, $y_loc_la2, 'D', array(
                //'all' => $style_box_dash
            //));
            
            //ECMS/SEMS indicator
            $ecms_sems_indicator = '';
            if($box_ecms_sems == 'true') {
                $ecms_sems_indicator = 'SEMS';
            }
            else {
                $ecms_sems_indicator = 'ECMS';
            }
            
            $obj_pdf->SetFont('helvetica', 'B', 40);
            $obj_pdf->StartTransform();
            $obj_pdf->Rotate(90, $x_loc_ecms_sems, $y_loc_ecms_sems);
            $obj_pdf->Text($x_loc_ecms_sems, $y_loc_ecms_sems, $ecms_sems_indicator);
            $obj_pdf->StopTransform();
            
            //Digitization center box regular border
            //$obj_pdf->Rect($x_loc_digi_box_regular, $y_loc_digi_box_regular, 30, 10, '', '', array(0, 0, 0));
            
            //Digitization center box dashed border
            $obj_pdf->RoundedRect($x_loc_digi_box_dashed, $y_loc_digi_box_dashed, 46.5, 16, 2, '1111', null, $style_box_dash);
            
            //Black rectangle containing program office and month/year of request
            $obj_pdf->Rect($x_loc_black_rectangle, $y_loc_black_rectangle, 140, 35, 'F', '', array(0,0,0));
            
            //Rectangle containing bay
            $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
            
            //White Rectangle containing program office
            $obj_pdf->SetLineStyle(array('width' => 5, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => array(255, 255, 255)));
            $obj_pdf->SetXY($x_loc_white_rectangle, $y_loc_white_rectangle);
            $obj_pdf->SetFillColor(255,255,255);
            $obj_pdf->SetFont('helvetica', 'B', 45);

if (preg_match('/^\d+$/', $GLOBALS['id'])) {
            $obj_pdf->Cell(55, 5, $box_program_office[$i], 1, 0, 'C', 1);
}

if (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {
            $obj_pdf->Cell(55, 5, $box_program_office_a, 1, 0, 'C', 1);
}

            //$obj_pdf->Cell(w, h = 0, txt = '', border = 0, ln = 0, align = '', fill = 0, link = nil, stretch = 0, ignore_min_height = false, calign = 'T', valign = 'M')
            
            //Cell containing bay
            /*$obj_pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'butt', 'dash' => 0, 'color' => array(0, 0, 0)));
            $obj_pdf->SetXY($x_loc_bay, $y_loc_bay);
            $obj_pdf->SetFont('helvetica', 'B', 30);*/

//Top half of page containing cell with aisle/bay/shelf/position  
if (preg_match('/^\d+$/', $GLOBALS['id'])) {
            $obj_pdf->SetXY($x_loc_box_position, $y_loc_box_position);
            $obj_pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'butt', 'dash' => 0, 'color' => array(0, 0, 0)));
            $obj_pdf->SetFont('helvetica', 'B', 20);
            $obj_pdf->Cell(105, 13, $box_location_position[$i], 1, 0, 'C', 1);

}

//Bottom half of page containing cell with aisle/bay/shelf/position  
if (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {
            $obj_pdf->SetXY($x_loc_box_position, $y_loc_box_position);
            $obj_pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'butt', 'dash' => 0, 'color' => array(0, 0, 0)));
            $obj_pdf->SetFont('helvetica', 'B', 20);
            $obj_pdf->Cell(105, 13, $request_location_position_a, 1, 0, 'C', 1);

}
            
/*            //Cell containing shelf
            $obj_pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'butt', 'dash' => 0, 'color' => array(0, 0, 0)));
            $obj_pdf->SetXY($x_loc_shelf, $y_loc_shelf);
            $obj_pdf->SetFillColor(0,0,0);
            $obj_pdf->SetFont('helvetica', 'B', 30);
            $obj_pdf->SetTextColor(255,255,255);
            
if (preg_match('/^\d+$/', $GLOBALS['id'])) {
            $obj_pdf->Cell(27, 0, $box_shelf[$i], 1, 0, 'C', 1);
}

if (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {
            $obj_pdf->Cell(27, 0, $box_shelf_a, 1, 0, 'C', 1);
}
*/           
            
            //set text color back to black
            $obj_pdf->SetTextColor(0,0,0);
            
            //Dashed border around aisle/bay/shelf/position
            $obj_pdf->RoundedRect($x_loc_dashed_border, $y_loc_dashed_border, 110, 18, 2, '1111', null, $style_box_dash);
            
            //RFID Box Location
            $obj_pdf->RoundedRect($x_loc_ba1, $y_loc_ba1, $x_loc_la2, $y_loc_la2, 5, '1111', null, $style_box_dash);
            
            //convert box id to epc id
            $epc = Patt_Custom_Func::convert_pattboxid_epc($box_array[$i]);

            //EPC Vertical Text
            $obj_pdf->StartTransform();
            //$obj_pdf->SetFont('helvetica', '', 14);
            $obj_pdf->Rotate(90, $x_loc_rfid, $y_loc_rfid);
            //switch out with epc barcode
            //$obj_pdf->Text($x_loc_rfid,$y_loc_rfid,'Place RFID Tag Here');
            //$obj_pdf->Text($x_loc_rfid,$y_loc_rfid, $epc);
            $obj_pdf->write1DBarcode($epc, 'C128', $x_loc_rfid, $y_loc_rfid, '', 20, 0.3, $style_barcode, 'N');
            $obj_pdf->StopTransform();
            
            //1D Box ID Barcode
            $obj_pdf->SetFont('helvetica', '', 11);
            $obj_pdf->write1DBarcode($box_array[$i], 'C128', $x_loc_1d, $y_loc_1d, '', 30, 0.7, $style_barcode, 'N');
            //$obj_pdf->Cell($x_loc_c, $y_loc_c, $box_array[$i], 0, 1);
            //1D Box ID Printout
            $obj_pdf->SetFont('helvetica', 'B', 24);
            $obj_pdf->Text($x_loc_c, $y_loc_c, $box_array[$i]);
            $obj_pdf->SetFont('helvetica', '', 14);
            
if (preg_match('/^\d+$/', $GLOBALS['id'])) {
$num = fetch_request_id();
}

if (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {
$num = $asset_id;
}

            

            $obj_pdf->Text($x_loc_rid, $y_loc_rid, $num);
            
            $obj_pdf->SetFont('helvetica', '', 11);
if (preg_match('/^\d+$/', $GLOBALS['id'])) {
$url_id = fetch_request_id();
}

if (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {
$url_id = $asset_id;
}
            //$url_key = fetch_request_key();
            //QR Code of Request
            $url = 'http://' . $_SERVER['SERVER_NAME'] . $subfolder_path .'/wp-admin/admin.php?page=wpsc-tickets&id=' . $num;
            //$obj_pdf->writeHTML($url);
            $obj_pdf->write2DBarcode($url, 'QRCODE,H', $x_loc_2d, $y_loc_2d, '', 50, $style_barcode, 'N');
            //$obj_pdf->Cell(150, 50, $url, 0, 1);
            $obj_pdf->SetFont('helvetica', 'B', 18);
            
if (preg_match('/^\d+$/', $GLOBALS['id'])) {
            //Obtain array of box locations
            //$obj_pdf->Text($x_loc_l, $y_loc_l, $box_location[$i]);
            
            //prints digitization center for all box labels in a request
            $obj_pdf->SetXY($x_loc_l, $y_loc_l);
            $obj_pdf->SetLineStyle(array('width' => 0.8, 'cap' => 'butt', 'join' => 'butt', 'dash' => 0, 'color' => array(0, 0, 0)));
            $obj_pdf->SetFont('helvetica', 'B', 20);
            $obj_pdf->Cell(40, 10, $box_location[$i], 1, 0, 'C', 1);
}

if (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {
            //Obtain array of box locations
            //$obj_pdf->Text($x_loc_l, $y_loc_l, $box_location_a);
            
            //prints digitization center for specific box labels
            $obj_pdf->SetXY($x_loc_l, $y_loc_l);
            $obj_pdf->SetLineStyle(array('width' => 0.8, 'cap' => 'butt', 'join' => 'butt', 'dash' => 0, 'color' => array(0, 0, 0)));
            $obj_pdf->SetFont('helvetica', 'B', 20);
            $obj_pdf->Cell(40, 10, $box_location_a, 1, 0, 'C', 1);
}

            //set month/year text color = white
            $obj_pdf->SetTextColor(255,255,255);
            $obj_pdf->SetFont('helvetica', 'B', 47);
            
if (preg_match('/^\d+$/', $GLOBALS['id'])) {
            $obj_pdf->Text($x_loc_cd, $y_loc_cd, $box_date); 
}

if (preg_match("/^([0-9]{7}-[0-9]{1,4})(?:,\s*(?1))*$/", $GLOBALS['id'])) {
            $obj_pdf->Text($x_loc_cd, $y_loc_cd, $box_date_a);
}

            $obj_pdf->SetFont('helvetica', '', 11);
            //set text color back to black
            $obj_pdf->SetTextColor(0,0,0);
        }
        
        //Generate PDF
        $obj_pdf->Output('patt_box_label_printout.pdf', 'I');     
}
} else {
echo "Pass a valid ID in URL";
}

    }
    else
    {
        //Define message for when no ID exists in URL
        echo "Pass request ID in URL";
    }
?>
