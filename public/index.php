<?php

require(__DIR__ . '/../_config/config.php');
require(__DIR__ . '/../vendor/autoload.php');

use LRSDA\Server\LRSConnector\LRSConnectionCheck;

$lrsChecker = new LRSConnectionCheck();

echo "Pinging LRS... ";
echo $lrsChecker->pingLRS() ? 'LRS is reachable.' : 'Failed to reach LRS.';