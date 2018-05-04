<?php
include_once "ITransport.php";

class Transport implements ITransport {
  public function send($msg, $email) {
    var_dump($msg);
    $subject = "Aeroflot Tracker";
    file_put_contents('debug.log', $msg);
    mail($email, $subject, $msg);
  }
}

