<?php
class Model {
  private $db;
  public function __construct(EscapeDB $db)
  {
    $this->db = $db;
  }

  /**
   * Отдает массив маршрутов с датами
   */
  public function getRequests() {

  }
}