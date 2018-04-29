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
CREATE TABLE email (
  id INT NOT NULL AUTO_INCREMENT,
  email VARCHAR(255),
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