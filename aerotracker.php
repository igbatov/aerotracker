<?php
include "transport.php";
include "AeroflotTracker.php";

$admin_email = 'igbatov@gmail.com';

$route_dates = [
    'MOW_PKC' => ['2018-08-03', '2018-07-20', '2018-08-17'],
    'PKC_MOW' => ['2018-08-12', '2018-08-06', '2018-09-02']
];

$email_routes = [
    'friendlyin@gmail.com' => [
        'MOW_PKC' => ['2018-08-03', '2018-08-04'],
        'PKC_MOW' => ['2018-08-12'],
    ],
    'thatdau@gmail.com' => [
        'MOW_PKC' => ['2018-07-20'],
        'PKC_MOW' => ['2018-08-06'],
    ],
    'Pospelova97@gmail.com' => [
        'MOW_PKC' => ['2018-07-20'],
        'PKC_MOW' => ['2018-08-06'],
    ],
    'Miroshd-am@yandex.ru' => [
        'MOW_PKC' => ['2018-08-17'],
        'PKC_MOW' => ['2018-09-02'],
    ],
];
$minPrice = 20000;

$transport = new Transport();
$aeroflotTracker = new AeroflotTracker($transport);
foreach ($route_dates as $route => $date) {
  $srcdst = $aeroflotTracker->keyToRoute($route);
  $aeroflotTracker->run(
      $srcdst['src'],
      $srcdst['dst'],
      $date,
      $email_routes,
      $admin_email,
      $minPrice
  );
  sleep(2);
}