<?php
include 'transport.php';

$dates = [
  '2018-08-01',
  '2018-08-02',
  '2018-08-03',
  '2018-08-04',
  '2018-08-05',
];
foreach($dates as $date) {
  try {
    $ch = curl_init('https://api.travelbar.tools/v1/avia/prices?origin=MOW&destination=PKC&depart_date='.$date.'&return_date=&currency=RUB&locale=en&partnerId=fg');
    $result = curl_exec($ch);
    $jsonResult = json_decode($result, true);
  } catch (\Throwable $e) {
    send($e->getMessage());
  }

  if (!$jsonResult || !$jsonResult['success']) {
    send("Bad json. Original ".var_export($jsonResult, true));
  }

var_dump($jsonResult);
exit();
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
}

if (!empty($toSend)) {
  send(var_export($toSend, true));
}
