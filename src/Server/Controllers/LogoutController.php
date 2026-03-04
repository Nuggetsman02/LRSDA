<?php

namespace LRSDA\Server\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SimpleSAML\Auth\Simple;

class LogoutController
{
    public function logout(Request $request, Response $response): Response
    {
        try {
            $auth = new Simple('default-sp');
            $auth->logout('/');
        } catch (\Exception $e) {
            // Log the error if needed
            error_log("Logout error: " . $e->getMessage());
        }

        // Redirection vers la page d'accueil après la déconnexion
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
