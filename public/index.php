<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require(__DIR__ . '/../_config/config.php');
require(__DIR__ . '/../vendor/autoload.php');


$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);


// use LRSDA\Server\LRSConnector\LRSConnectionCheck;

// $lrsChecker = new LRSConnectionCheck();

// echo "Pinging LRS... ";
// echo $lrsChecker->pingLRS() ? 'LRS is reachable.' : 'Failed to reach LRS.';

//  error_log($_SERVER["REQUEST_URI"]);


$app->get('/', function (Request $request, Response $response,) {
    return $response->withHeader('Location', 'Views/home.html')->withStatus(302);
});

require_once('../src/Server/Controllers/route_test.php');

$app->run();
