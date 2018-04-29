<?php

function send($msg) {
  var_dump($msg);
  $subject = "Aeroflot Tracker";
  file_put_contents('debug.log', $msg);
//  mail("i.batov@megaplan.ru", $subject, $msg);
//  mail("sem-oksana@bk.ru", $subject, $msg);
//  mail("Miroshd-am@yandex.ru", $subject, $msg);
//  mail("thatdau@gmail.com", $subject, $msg);
//  mail("Pospelova97@gmail.com", $subject, $msg);
}

