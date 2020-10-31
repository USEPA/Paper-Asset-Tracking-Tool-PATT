<?php

// Set error logging in db.php to yes or no.  Logs are written to log.txt

$WP_PATH = implode("/", (explode("/", $_SERVER["PHP_SELF"], -8)));
require_once($_SERVER['DOCUMENT_ROOT'].$WP_PATH.'/wp-config.php');

$path = preg_replace('/wp-content.*$/', '', __DIR__);
include ($path . 'wp-load.php');
include ($path . 'wp-content/plugins/pattracking/includes/class-wppatt-custom-function.php');


$host = DB_HOST; /* Host name */
$user = DB_USER; /* User */
$password = DB_PASSWORD; /* Password */
$dbname = DB_NAME; /* Database name */

$con = mysqli_connect($host, $user, $password,$dbname);
// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

date_default_timezone_set('America/New_York');

$rawPostData = file_get_contents('php://input');

// Database variables located in db.php
    $readerName = $_POST['reader_name'];
    $macAddress = $_POST['mac_address'];

    $lineEnding = $_POST['line_ending'];
    $fieldDelim = $_POST['field_delim'];
    $fieldNames = $_POST['field_names'];
    $fieldValues = $_POST['field_values'];

// Replace the field delimiter with a comma.
    str_replace($fieldDelim, ",", $fieldNames);

// Break the field values up into rows.
    $rows = explode("\n", $fieldValues);

// Remove the last row. It's always blank
    if (sizeof($rows))
        array_pop($rows);
    $fieldNames = "reader_name,mac_address,box_id," . $fieldNames;
    foreach ($rows as $row) {
        
        $array = explode(',', $row);
        $array_strip = str_replace('"', '', $array[1]);
        $box_id = Patt_Custom_Func::convert_epc_pattboxid($array_strip);
        
        $row = $readerName . "," . $macAddress . ",'" . $box_id . "',"  . $row;
        $fld_value = str_replace("\\", "", $row);
        $row = $fld_value;

$check_boxid = "SELECT count(box_id) as count FROM wpqa_wpsc_epa_boxinfo WHERE box_id = '".$box_id."'";
$boxid_result = mysqli_query($con,$check_boxid);
$rowcount=mysqli_num_rows($boxid_result);

if ($rowcount > 0) {
$query = "INSERT INTO wpqa_wpsc_epa_rfid_data ($fieldNames) VALUES ($row)";
//$query = "INSERT INTO wpqa_wpsc_epa_rfid_data (Reader_Name) VALUES ('".$rowcount."')";
$retval = mysqli_query($con,$query);
}

    }
    mysqli_close($con);

?>