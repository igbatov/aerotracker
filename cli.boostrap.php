<?php

// path to this file
$path = dirname(__FILE__);

// init config
require_once ($path.'/'.'config.php');
$c = new Config();

// set default log file
ini_set('error_log', $c->getDefaultPath('log')."/error_log.log");
/**
 * Include all our classes
 */
require_once ($path.'/'.'db.php');
require_once ($path.'/'.'escapedb.php');
require_once ($path.'/'.'logger.php');
require_once ($path.'/'.'model.php');

// init helper modules
$db = new EscapeDB(new DB(
    $c->getDbConf()
));

$logger = new Logger($db, dirname(__FILE__), "cli.bootstrap.php");
$model = new Model($db);