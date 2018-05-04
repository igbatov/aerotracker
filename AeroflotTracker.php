<?php
include_once "ITransport.php";

class AeroflotTracker {
  private $transport;
  public function __construct(ITransport $transport)
  {
    $this->transport = $transport;
  }

  public function run($src, $dst, $date, $email_routes, $admin_email, $minPrice) {
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
      $this->transport->send($e->getMessage(), $admin_email);
    }

    if (!$jsonResult || !$jsonResult['success']) {
      $this->transport->send("Bad json. Original ".var_export($result, true), $admin_email);
    }

    $minPrices = reset($jsonResult['data']['min_prices']);
    var_export($jsonResult['data']['min_prices']);

    $toSend = $this->splitData($minPrices, $src, $dst, $email_routes, $minPrice);

    if (!empty($toSend)) {
      foreach ($toSend as $email => $ar) {
        $this->transport->send(var_export($ar, true), $email);
      }
    }
  }

  /**
   * Разбивает данные по email для отсылки
   */
  public function splitData($minPrices, $src, $dst, $email_routes, $minPrice) {
    $toSend = [];
    foreach($minPrices as $date => $price){
      if (intval($price['amount']) <= $minPrice) {
        foreach ($email_routes as $email => $route_dates) {
          foreach ($route_dates as $route => $dates) {
            if (
                $route === $this->routeToKey($src, $dst) &&
                in_array($date, $dates)
            ) {
              if (!isset($toSend[$email])) {
                $toSend[$email] = [];
              }
              $toSend[$email][] = $date.' '.var_export($price, true);
            }
          }
        }
      }
    }
    return $toSend;
  }

  public function keyToRoute($str) {
    $srcdst = explode('_', $str);
    return [
        'src' => $srcdst[0],
        'dst' => $srcdst[1]
    ];
  }

  public function routeToKey($src, $dst) {
    return $src."_".$dst;
  }
}
