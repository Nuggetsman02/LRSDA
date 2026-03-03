<?php

use LRSDA\Server\Controllers\ExportController;
use Slim\app;

return function(App $app){
    $app->post('/api/logout', [ExportController::class, 'logout']);
};