<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $app->get('/test', function (Request $request, Response $response) {
        $jsonFile = file_get_contents(__DIR__ . '/../data/statement.json');
        // recover query parameters
        $queryParams = $request->getQueryParams();
        error_log(print_r($queryParams, true));

        $let =  json_decode($jsonFile, true);
        error_log($jsonFile);
        $response->getBody()->write(json_encode($let));
        return $response->withHeader('Content-Type', 'application/json');
    });
};
