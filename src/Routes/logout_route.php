<?php

use LRSDA\Server\Controllers\LogoutController;
use Slim\app;

return function(App $app){
    $app->get('/api/logout', [LogoutController::class, 'logout']);
};