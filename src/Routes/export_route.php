<?php

use LRSDA\Server\Controllers\ExportController;
use Slim\app;

return function(App $app){
    $app->post('/api/export', [ExportController::class, 'export']);
};