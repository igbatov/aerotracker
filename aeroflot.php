<?php

class Aeroflot {
  private $notifier;
  private $client;

  public function __construct($notifier, \GuzzleHttp\Client $client)
  {
    $this->client = $client;
  }

  public function processRouteDates($routeDates, $routes) {
    $requests = $this->getRequests($routeDates);
    foreach ($requests as $routeId => $dates) {
      foreach ($dates as $date) {
        $this->sendRequest($routes[$routeId]['src'], $routes[$routeId]['dst'], $date, 2000);
        sleep(2);
      }
    }
  }

  private function sendRequest($src, $dst, $date, $sum) {
    $data = array (
        'routes' =>
            array (
                0 =>
                    array (
                        'origin' => $src,
                        'destination' => $dst,
                        'departure_date' => $date,
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
      $this->notifier->send($e->getMessage());
    }

    if (!$jsonResult || !$jsonResult['success']) {
      $this->notifier->send("Bad json. Original ".var_export($result, true));
    }

    $minPrices = reset($jsonResult['data']['min_prices']);
    var_export($jsonResult['data']['min_prices']);

    $toSend = [];
    foreach($minPrices as $date => $price){
      if (
          intval($price['amount']) <= $sum
      ) {
        $toSend[] = $date.' '.var_export($price, true);
      }
    }

    if (!empty($toSend)) {
      $this->notifier->send(var_export($toSend, true));
    }

  }

  /**
   * Разбивает $routeDates на массив маршрут - дата который в совокупности
   * покрывает все перидоы дат
   */
  public function getRequests($routeDates) {
    $result = [];
    $maxAlreadyRespectedDate = '';
    foreach ($routeDates as $route_id => $dates) {
      $result[$route_id] = [];
      foreach ($dates as $date) {
        if ($date <= $maxAlreadyRespectedDate) {
          continue;
        } else {
          $result[$route_id][] = $this->addDays($date, floor($this->range/2));
          $maxAlreadyRespectedDate = $this->addDays($date, $this->range);
        }
      }
    }

    return $result;
  }

  /**
   * Добавляет n дней к дате
   */
  private function addDays($date, $n) {
    return date('Y-m-d', strtotime($date. ' + '.$n.' days'));
  }

  /**
   * Удаляет n дней от даты
   */
  private function removeDays($date, $n) {
    return date('Y-m-d', strtotime($date. ' - '.$n.' days'));
  }
}

