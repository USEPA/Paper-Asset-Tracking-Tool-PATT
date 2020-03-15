<?php

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

$host = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
$connect = new PDO($host, DB_USER, DB_PASSWORD);

$method = $_SERVER['REQUEST_METHOD'];


if($method == 'GET')
{

 $data = array(
  ':company_name'   => "%" . $_GET['company_name'] . "%",
  ':tracking_number'   => "%" . $_GET['tracking_number'] . "%",
  ':status'     => "%" . $_GET['status'] . "%",
  ':ticket_id'    => $_GET['ticket_id']
 );

 $query = 'SELECT * FROM wpqa_wpsc_epa_shipping_tracking WHERE company_name LIKE :company_name AND tracking_number LIKE :tracking_number AND status LIKE :status AND ticket_id = :ticket_id ORDER BY id DESC';

 $statement = $connect->prepare($query);
 $statement->execute($data);
 $result = $statement->fetchAll();
 foreach($result as $row)
 {
  $output[] = array(
   'id'    => $row['id'],
   'ticket_id'    => $row['ticket_id'], 
   'company_name'  => $row['company_name'],
   'tracking_number'   =>  $row['tracking_number'],
   'status'    => $row['status']
  );
 }
 header("Content-Type: application/json");
 echo json_encode($output);
}

if($method == "POST")
{
 $data = array(
  ':ticket_id'  => $_GET['ticket_id'],
  ':company_name'  => $_POST["company_name"],
  ':tracking_number'    => $_POST["tracking_number"]
 );

 $query = "INSERT INTO wpqa_wpsc_epa_shipping_tracking (ticket_id, company_name, tracking_number) VALUES (:ticket_id, :company_name, :tracking_number)";
 $statement = $connect->prepare($query);
 $statement->execute($data);
}

if($method == 'PUT')
{
 parse_str(file_get_contents("php://input"), $_PUT);
 $data = array(
  ':id'   => $_PUT['id'],
  ':company_name' => $_PUT['company_name'],
  ':tracking_number' => $_PUT['tracking_number']
 );
 $query = "
 UPDATE wpqa_wpsc_epa_shipping_tracking 
 SET
 company_name = :company_name, 
 tracking_number = :tracking_number
 WHERE id = :id
 ";
 $statement = $connect->prepare($query);
 $statement->execute($data);
}

if($method == "DELETE")
{
 parse_str(file_get_contents("php://input"), $_DELETE);
 $query = "DELETE FROM wpqa_wpsc_epa_shipping_tracking WHERE id = '".$_DELETE["id"]."'";
 $statement = $connect->prepare($query);
 $statement->execute();
}

?>