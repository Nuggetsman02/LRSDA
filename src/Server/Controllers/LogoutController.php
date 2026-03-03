<?php

namespace LRSDA\Server\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SimpleSAML\Auth\Simple;

class LogoutController
{
    public function logout(Request $request, Response $response): Response
    {
        $auth = new Simple('default-sp');

        $auth->logout('/'); 
        
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}