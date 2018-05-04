<?php
ini_set('error_log', "../error_log.log");
include_once "../AeroflotTracker.php";
include_once "../ITransport.php";
class TransportMock implements ITransport{
  public function send($msg, $email) {
    echo $msg;
    echo $email;
  }
}
