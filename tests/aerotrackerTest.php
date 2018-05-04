<?php
include_once "cli.bootstrap.php";

$transport = new TransportMock();
$aeroflot = new AeroflotTracker($transport);

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
$toSend = $aeroflot->splitData(
    [
      '2018-08-03' => ['amount' => '21000'],
      '2018-08-04' => ['amount' => '11000'],
      '2018-08-05' => ['amount' => '11000'],
      '2018-07-20' => ['amount' => '11000'],
    ],
    'MOW',
    'PKC',
    $email_routes,
    $minPrice
);

var_export($toSend);
