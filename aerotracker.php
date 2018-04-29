<?php
include "transport.php";

$data = array (
  'routes' => 
  array (
    0 => 
    array (
      'origin' => 'MOW',
      'destination' => 'PKC',
      'departure_date' => '2018-08-03',
    ),
  ),
  'cabin' => 'econom',
  'award' => false,
  'country' => 'ru',
  'adults' => 1,
  'children' => 0,
  'infants' => 0,
  'combined' => false,
  'coupon_codes' => 
  array (
  ),
  'lang' => 'ru',
  'extra' => 
  array (
  ),
  'client' => 
  array (
    'ga_client_id' => 'GA1.2.1885073241.1519708151',
    'loyalty_id' => '',
    'loyalty_level' => '',
  ),
);

try {
  $data_string = json_encode($data);
  $ch = curl_init('https://www.aeroflot.ru/sb/booking/api/app/search/v3');
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                
    'Content-Type: application/json', 
    'Content-Length: ' . strlen($data_string))
  );                                                  
  $result = curl_exec($ch);
  $jsonResult = json_decode($result, true);
} catch (\Throwable $e) {
  send($e->getMessage());
}

if (!$jsonResult || !$jsonResult['success']) {
  send("Bad json. Original ".var_export($jsonResult, true));
}

$minPrices = reset($jsonResult['data']['min_prices']);
var_export($jsonResult['data']['min_prices']);

$toSend = [];
foreach($minPrices as $date => $price){
  if (
	in_array($date, ['2018-07-31', '2018-08-01', '2018-08-02', '2018-08-03', '2018-08-04', '2018-08-05']) &&
        intval($price['amount']) <= 20000
     ) {
	$toSend[] = $date.' '.var_export($price, true);
  }
}

if (!empty($toSend)) {
  send(var_export($toSend, true));
}

