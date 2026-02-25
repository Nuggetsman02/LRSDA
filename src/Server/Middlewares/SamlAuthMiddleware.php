<?php

namespace LRSDA\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use SimpleSAML\Auth\Simple;
use Slim\Psr7\Response;

class SamlAuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $auth = new Simple('default-sp');

        // Vérifie si l'utilisateur est connecté
        if (!$auth->isAuthenticated()) {

            $auth->requireAuth();
        }

        // Récupère les attributs de l'utilisateur
        $attributes = $auth->getAttributes();

        // Injection des attributs dans la requête
        $request = $request->withAttribute('user_attributes', $attributes);

        return $handler->handle($request);
    }
}
