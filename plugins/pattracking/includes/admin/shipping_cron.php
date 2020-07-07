<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//$path = preg_replace('/wp-content.*$/','',__DIR__);
//include($path.'wp-load.php');

global $current_user, $wpscfunction, $wpdb;

$shippingArray = ["usps", "fedex", "ups", "dhl"];

// Begin going through the different shipping carriers
foreach ($shippingArray as $shippingCompany)  {

switch ($shippingCompany) {
    case "usps":

$shipping_query = $wpdb->get_results(
"SELECT *
FROM wpqa_wpsc_epa_shipping_tracking
WHERE company_name = 'usps'"
);

foreach ($shipping_query as $item) {

$trackingNumber = $item->tracking_number;

if($item->shipped == 0) {
$url = "http://production.shippingapis.com/shippingAPI.dll";
$service = "TrackV2";

$xml = rawurlencode("
<TrackRequest USERID='xxxxxxxx'>
    <TrackID ID=\"".$trackingNumber."\"></TrackID>
    </TrackRequest>");

$request = $url . "?API=" . $service . "&XML=" . $xml;
// send the POST values to USPS
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL,$request);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_HTTPGET, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// parameters to post

$result = curl_exec($curl);
//var_dump($result);
curl_close($curl);

$response = new SimpleXMLElement($result);

$deliveryStatus = $response->TrackInfo->TrackSummary;
$status_delivered_array = array('DELIVERY', 'DELIVERED');
$status_shipped_array = array('ACCEPTED', 'POSSESSION');
$table_name = 'wpqa_wpsc_epa_shipping_tracking';

if ( preg_match('('.implode('|',$status_shipped_array).')', strtoupper($deliveryStatus))){
$wpdb->update( $table_name, array( 'shipped' => 1),array('ID'=>$item->id));
$wpdb->update( $table_name, array( 'status' => $deliveryStatus),array('ID'=>$item->id));
}

if ( preg_match('('.implode('|',$status_delivered_array).')', strtoupper($deliveryStatus))){
$wpdb->update( $table_name, array( 'delivered' => 1),array('ID'=>$item->id));
$wpdb->update( $table_name, array( 'status' => $deliveryStatus),array('ID'=>$item->id));
}
}
}
        break;
    case "fedex":
$shipping_query = $wpdb->get_results(
"SELECT *
FROM wpqa_wpsc_epa_shipping_tracking
WHERE company_name = 'fedex'"
);

foreach ($shipping_query as $item) {
    //Set SuperGlobal ID variable to be used in all functions below

$trackingNumber = $item->tracking_number;

if($item->shipped == 0) {
    
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://ws.fedex.com:443/web-services",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS =>"
  <SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns=\"http://fedex.com/ws/track/v18\">\r\n
  <SOAP-ENV:Body>\r\n
  <TrackRequest>\r\n
  <WebAuthenticationDetail>\r\n
  <UserCredential>\r\n
  <Key>xxxxxxxx</Key>\r\n
  <Password>xxxxxxxx</Password>\r\n
  </UserCredential>\r\n
  </WebAuthenticationDetail>\r\n
  <ClientDetail>\r\n
  <AccountNumber>xxxxxxxx</AccountNumber>\r\n
  <MeterNumber>xxxxxxxx</MeterNumber>\r\n
  </ClientDetail>\r\n
  <TransactionDetail>\r\n
  <CustomerTransactionId>Track By Number_v18</CustomerTransactionId>\r\n
  <Localization>\r\n
  <LanguageCode>EN</LanguageCode>\r\n
  </Localization>\r\n
  </TransactionDetail>\r\n
  <Version>\r\n
  <ServiceId>trck</ServiceId>\r\n
  <Major>18</Major>\r\n
  <Intermediate>0</Intermediate>\r\n
  <Minor>0</Minor>\r\n
  </Version>\r\n
  <SelectionDetails>\r\n
  <PackageIdentifier>\r\n
  <Type>TRACKING_NUMBER_OR_DOORTAG</Type>\r\n
  <Value>".$trackingNumber."</Value>\r\n
  </PackageIdentifier>\r\n
  </SelectionDetails>\r\n
  <ProcessingOptions>INCLUDE_DETAILED_SCANS</ProcessingOptions>\r\n
  </TrackRequest>\r\n
  </SOAP-ENV:Body>\r\n
  </SOAP-ENV:Envelope>",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/xml"
  ),
));

$response = curl_exec($curl);

curl_close($curl);

$xml = new SimpleXMLElement($response);
$body = $xml->xpath('//SOAP-ENV:Body')[0];
$array = json_decode(json_encode((array)$body), TRUE);

$deliveryCode = $array['TrackReply']['CompletedTrackDetails']['TrackDetails']['StatusDetail']['Code'];
$deliveryStatus = $array['TrackReply']['CompletedTrackDetails']['TrackDetails']['StatusDetail']['Description'].' : '
.$array['TrackReply']['CompletedTrackDetails']['TrackDetails']['StatusDetail']['CreationTime'];

$status_delivered_array = array('AD', 'AR', 'DL');
$status_shipped_array = array('PF', 'AA', 'PL', 'AC', 'PM', 'PU', 'AF', 'PX', 'AP', 'AR', 'CH', 'DD', 'DE', 'SE', 'DR', 'SF', 'DY', 'TR', 'EA', 'ED', 'CC', 'EO', 'CD', 'CP', 'IP');
$table_name = 'wpqa_wpsc_epa_shipping_tracking';

if ( preg_match('('.implode('|',$status_shipped_array).')', strtoupper($deliveryCode))){
$wpdb->update( $table_name, array( 'shipped' => 1),array('ID'=>$item->id));
$wpdb->update( $table_name, array( 'status' => $deliveryStatus),array('ID'=>$item->id));
}

if ( preg_match('('.implode('|',$status_delivered_array).')', strtoupper($deliveryCode))){
$wpdb->update( $table_name, array( 'delivered' => 1),array('ID'=>$item->id));
$wpdb->update( $table_name, array( 'status' => $deliveryStatus),array('ID'=>$item->id));
}

}

}
        break;
    case "ups":
$shipping_query = $wpdb->get_results(
"SELECT *
FROM wpqa_wpsc_epa_shipping_tracking
WHERE company_name = 'ups'"
);

foreach ($shipping_query as $item) {
    //Set SuperGlobal ID variable to be used in all functions below

$trackingNumber = $item->tracking_number;

if($item->shipped == 0) {

$data ="<?xml version=\"1.0\"?>
        <AccessRequest xml:lang='en-US'>
                <AccessLicenseNumber>9D8172DB2FD319B5</AccessLicenseNumber>
                <UserId>xxxxxxxx</UserId>
                <Password>xxxxxxxx</Password>
        </AccessRequest>
        <?xml version=\"1.0\"?>
        <TrackRequest>
                <Request>
                        <TransactionReference>
                                <CustomerContext>
                                        <InternalKey>blah</InternalKey>
                                </CustomerContext>
                                <XpciVersion>1.0</XpciVersion>
                        </TransactionReference>
                        <RequestAction>Track</RequestAction>
                </Request>
        <TrackingNumber>".$trackingNumber."</TrackingNumber>
        </TrackRequest>";
$curl = curl_init("https://www.ups.com/ups.app/xml/Track");
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl,CURLOPT_POST,1);
curl_setopt($curl,CURLOPT_TIMEOUT, 60);
curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
$result=curl_exec ($curl);
// echo '<!-- '. $result. ' -->';
$data = strstr($result, '<?');
$xml_parser = xml_parser_create();
xml_parse_into_struct($xml_parser, $data, $vals, $index);
xml_parser_free($xml_parser);
$array = array();
$level = array();
foreach ($vals as $xml_elem) {
 if ($xml_elem['type'] == 'open') {
if (array_key_exists('attributes',$xml_elem)) {
         list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
} else {
         $level[$xml_elem['level']] = $xml_elem['tag'];
}
 }
 if ($xml_elem['type'] == 'complete') {
$start_level = 1;
$php_stmt = '$array';
while($start_level < $xml_elem['level']) {
         $php_stmt .= '[$level['.$start_level.']]';
         $start_level++;
}
$php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
eval($php_stmt);
 }
}
curl_close($curl);

//print_r($array);

$deliveryCode = $array['TRACKRESPONSE']['SHIPMENT']['PACKAGE']['ACTIVITY']['STATUS']['STATUSTYPE']['CODE'];
$deliveryStatus = $array['TRACKRESPONSE']['SHIPMENT']['PACKAGE']['ACTIVITY']['STATUS']['STATUSTYPE']['DESCRIPTION'].' : '
.$array['TRACKRESPONSE']['SHIPMENT']['PACKAGE']['ACTIVITY']['GMTDATE'].'T'
.$array['TRACKRESPONSE']['SHIPMENT']['PACKAGE']['ACTIVITY']['GMTTIME'];

$status_delivered_array = array('D');
$status_shipped_array = array('I', 'X', 'P', 'M');
$table_name = 'wpqa_wpsc_epa_shipping_tracking';

if ( preg_match('('.implode('|',$status_shipped_array).')', strtoupper($deliveryCode))){
$wpdb->update( $table_name, array( 'shipped' => 1),array('ID'=>$item->id));
$wpdb->update( $table_name, array( 'status' => $deliveryStatus),array('ID'=>$item->id));
}

if ( preg_match('('.implode('|',$status_delivered_array).')', strtoupper($deliveryCode))){
$wpdb->update( $table_name, array( 'delivered' => 1),array('ID'=>$item->id));
$wpdb->update( $table_name, array( 'status' => $deliveryStatus),array('ID'=>$item->id));
}

}

}
    case "dhl":
$shipping_query = $wpdb->get_results(
"SELECT *
FROM wpqa_wpsc_epa_shipping_tracking
WHERE company_name = 'dhl'"
);

foreach ($shipping_query as $item) {
    //Set SuperGlobal ID variable to be used in all functions below

$trackingNumber = $item->tracking_number;

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api-eu.dhl.com/track/shipments?trackingNumber=".$trackingNumber,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "DHL-API-Key: xxxxxxxx",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $json = json_decode($response, true);

$deliveryStatus = $json['shipments']['0']['status']['description'].' : '
.$json['shipments']['0']['status']['timestamp'];

$status_delivered_array = array('DELIVERY', 'DELIVERED', 'HOME', 'PICKED');
$status_shipped_array = array('PICKED','ARRIVAL','ARRIVED','RECEIVED','PROCESSED','ACCEPTED','DEPARTED','DEPARTURE','DEPART','EN ROUTE', 'PROCESSED', 'TENDERED', 'TRANSIT','ARRIVAL','ARRIVED','PROCESSED','LOADED','CUSTOMS');
$table_name = 'wpqa_wpsc_epa_shipping_tracking';

if ( preg_match('('.implode('|',$status_shipped_array).')', strtoupper($deliveryStatus))){
$wpdb->update( $table_name, array( 'shipped' => 1),array('ID'=>$item->id));
$wpdb->update( $table_name, array( 'status' => $deliveryStatus),array('ID'=>$item->id));
}

if ( preg_match('('.implode('|',$status_delivered_array).')', strtoupper($deliveryStatus))){
$wpdb->update( $table_name, array( 'delivered' => 1),array('ID'=>$item->id));
$wpdb->update( $table_name, array( 'status' => $deliveryStatus),array('ID'=>$item->id));
}
}

}
        break;
}

        }
        
// Change the status of request from Initial Review Complete to Shipped
$shipped_array = array();
$delivered_array = array();

$get_unique_tickets = $wpdb->get_results(
	"SELECT DISTINCT ticket_id
FROM wpqa_wpsc_epa_shipping_tracking WHERE id <> '-99999'"
);

foreach ($get_unique_tickets as $item) {

$ticket_id = $item->ticket_id ;
$ticket_data = $wpscfunction->get_ticket($ticket_id);
$status_id   	= $ticket_data['ticket_status'];

$get_shipped_status = $wpdb->get_results(
 	"SELECT shipped
 FROM wpqa_wpsc_epa_shipping_tracking
 WHERE ticket_id = " . $item->ticket_id
 );

foreach ($get_shipped_status as $shipped) {
	array_push($shipped_array, $shipped->shipped);
	}
	
if (($status_id == 4) && (!in_array(0, $shipped_array))) {
$wpscfunction->change_status($item->ticket_id, 5);   
}
	}
	
	print_r($shipped_array);
	
$get_delivered_status = $wpdb->get_results(
 	"SELECT delivered
 FROM wpqa_wpsc_epa_shipping_tracking
 WHERE ticket_id = " . $item->ticket_id
 );

foreach ($get_delivered_status as $delivered) {
	array_push($delivered_array, $delivered->delivered);
	}
	
if (($status_id == 4) && (!in_array(0, $delivered_array))) {
$wpscfunction->change_status($item->ticket_id, 63);   
}
	
	print_r($delivered_array);
?>