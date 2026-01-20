<?php

use PHPUnit\Util\Json;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/test', function (Request $request, Response $response) {
    $var = ['message' => 'ce que tu broutes']; 
    $response->getBody()->write(json_encode($var));
    return $response->withHeader('Content-Type', 'application/json');
});

