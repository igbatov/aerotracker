<?php
class Model {
  private $db;
  /**
   * некоторые API позволяют за один запрос получить $range дат по данному направлению
   */
  private $range;
  public function __construct(EscapeDB $db, $range)
  {
    $this->db = $db;
    $this->range = $range;
  }

  public function getRouteDates() {
    $q = <<<SQL
SELECT trd.route_id, d.flight_date
FROM transport_route_date AS  trd
  JOIN date AS d ON (trd.date_id = d.id)
GROUP BY route_id, date_id ORDER BY trd.route_id, d.flight_date ASC
SQL;

    $route_dates = [];
    $rows = $this->db->exec($q);
    foreach ($rows as $row) {
      if (!isset($route_dates[$row['route_id']])) {
        $route_dates[$row['route_id']] = [];
      }
      $route_dates[$row['route_id']][] = $row['flight_date'];
    }

    foreach ($route_dates as $k => $route_date) {
      sort($route_dates[$k]);
    }

    return $route_dates;
  }

  public function getRoutes() {
    $q = <<<SQL
SELECT id, src, dst FROM route
SQL;

    $routes = [];
    $rows = $this->db->exec($q);
    foreach ($rows as $row) {
      $routes[$row['id']] = [
          'src' => $row['src'],
          'dst' => $row['dst'],
      ];
    }

    return $routes;
  }
}