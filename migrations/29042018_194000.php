<?php
include "../cli.boostrap.php";

$q = <<<'SQL'
CREATE TABLE route (
  id INT NOT NULL AUTO_INCREMENT,
  src VARCHAR(255),
  dst VARCHAR(255),
  PRIMARY KEY (id)
);
SQL;
$db->exec($q);

$q = <<<'SQL'
CREATE TABLE transport (
  id INT NOT NULL AUTO_INCREMENT,
  type VARCHAR(255),
  account VARCHAR(255),
  PRIMARY KEY (id)
);
SQL;
$db->exec($q);

$q = <<<'SQL'
CREATE TABLE date (
  id INT NOT NULL AUTO_INCREMENT,
  flight_date DATE,
  PRIMARY KEY (id)
);
SQL;
$db->exec($q);

$q = <<<'SQL'
CREATE TABLE transport_route_date (
  id INT NOT NULL AUTO_INCREMENT,
  transport_id INT NOT NULL,
  route_id INT NOT NULL,
  date_id INT NOT NULL,
  PRIMARY KEY (id)
);
SQL;
$db->exec($q);