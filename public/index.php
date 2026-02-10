<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use LRSDA\Server\Controllers\ExportController;
use LRSDA\Server\Services\StatementService;
use LRSDA\Server\Configs\Configuration;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

// 1. CHARGEMENT DE LA CONFIGURATION
try {
    $xapiSettings = Configuration::getInstance()->api_v2(); 
} catch (\Exception $e) {
    die("Erreur critique de configuration : " . $e->getMessage());
}

$containerBuilder = new ContainerBuilder();

// 2. DÉFINITIONS DU CONTENEUR (Injection de Dépendances)
$containerBuilder->addDefinitions([
    
    'settings.xapi' => $xapiSettings,

    // Configuration du Client Guzzle
    Client::class => function ($c) {
        $config = $c->get('settings.xapi');
        
        $baseUri = rtrim($config['uri'], '/') . '/';
        $authHeader = $config['auth_key'];
        
        // Sécurité : Ajout auto du préfixe "Basic " si manquant
        if (stripos($authHeader, 'Basic ') !== 0) {
            $authHeader = 'Basic ' . $authHeader;
        }

        return new Client([
            'base_uri' => $baseUri,
            'headers'  => [
                'X-Experience-API-Version' => '1.0.1',
                'Authorization'            => $authHeader,
                'Content-Type'             => 'application/json',
            ],
            'timeout'  => 30.0, // Augmenté à 30s pour les gros exports CSV
        ]);
    },

    // Autowiring : PHP-DI crée automatiquement ces classes avec leurs dépendances
    StatementService::class => \DI\autowire(StatementService::class),
    ExportController::class => \DI\autowire(ExportController::class),
]);

$container = $containerBuilder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

// 3. MIDDLEWARES
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

/**
 * Gestion des erreurs
 * En développement : true, true, true (affiche les détails)
 * En production : false, false, false
 */
$app->addErrorMiddleware(true, true, true);

// 4. ROUTES

// Route Accueil : Redirection vers l'interface
$app->get('/', function (Request $request, Response $response) {
    return $response
        ->withHeader('Location', 'Views/home.html')
        ->withStatus(302);
});

// Chargement des fichiers de routes externes
// Assurez-vous que les chemins sont corrects par rapport à index.php
call_user_func(require __DIR__ . '/../src/Routes/route_test.php', $app);
call_user_func(require __DIR__ . '/../src/Routes/export_route.php', $app);

$app->run();