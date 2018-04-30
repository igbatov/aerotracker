<?php
include_once "cli.boostrap.php";
include_once "aeroflot.php";

$route_dates = $model->getRouteDates();
$routes = $model->getRouteDates();
$client = new \GuzzleHttp\Client();
$notifier = new Notifier();
$aeroflot = new Aeroflot($notifier, $client);
$aeroflot->processRouteDates($route_dates, $routes);