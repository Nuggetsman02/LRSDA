<?php

date_default_timezone_set("europe/brussels");
define("SERVICE_PATH", './');

error_reporting(E_ALL );
ini_set('error_log', __DIR__ . '/../logs/' . 'error_log.log');
ini_set('log_errors', 'On');