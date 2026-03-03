<?php

namespace LRSDA\Server\Middlewares;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Psr7\Response as Psr7Response;
use SimpleSAML\Session;
use SimpleSAML\Auth\Simple;


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

    public function logout(Request $request, RequestHandler $handler): Response
    {
        $auth = new Simple('default-sp');
        $auth->logout();
        Session::getSessionFromRequest()->cleanup();

        // Redirige vers la page d'accueil après la déconnexion
        return new Psr7Response(302, ['Location' => '/']);
    }
}
