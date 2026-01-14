<?php

require(__DIR__ . '/../_config/config.php');
require(__DIR__ . '/../vendor/autoload.php');

use src\server\LRSConnectionCheck;

$lrsChecker = new LRSConnectionCheck('endpoint', 'user', 'password');

echo $lrsChecker->pingLRS() ? 'LRS is reachable.' : 'Failed to reach LRS.';