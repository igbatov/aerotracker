<?php
interface ITransport {
  public function send($msg, $email);
}