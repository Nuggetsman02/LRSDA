<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LRSDA\Server\LRSConnector\LRSConnectionCheck;
use LRSDA\Server\Configs\Configuration;
use LRSDA\Server\Services\StatementService;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use GuzzleHttp\Client;

require(__DIR__ . '/../_config/config.php');
require(__DIR__ . '/../vendor/autoload.php');

$containerBuilder = new ContainerBuilder();

// Définir les dépendances dans le conteneur
$containerBuilder->addDefinitions([
    // On définit le Client Guzzle une seule fois
    Client::class => function () {
        $xapi = Configuration::getInstance()->xapi();
        return new Client([
            'base_uri' => $xapi['uri'] . '/',
            'headers'  => [
                'X-Experience-API-Version' => '1.0.1',
                'Authorization'            => $xapi['auth_key'],
                'Content-Type'             => 'application/json',
            ],
        ]);
    },

    // Le Service utilise le client défini ci-dessus
    StatementService::class => function ($c) {
        return new StatementService($c->get(Client::class));
    },

    LRSConnectionCheck::class => function ($c) {
        return new LRSConnectionCheck($c->get(Client::class));
    }
]);

// 3. Créer le conteneur
$container = $containerBuilder->build();

// Donne le conteneur à Slim AVANT de créer l'App
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// --- TEST DE CONNEXION ---
//
// $lrsChecker = $container->get(LRSConnectionCheck::class);
// echo "Pinging LRS... ";
// echo $lrsChecker->pingLRS() ? 'LRS is reachable.' : 'Failed to reach LRS.';
// die();

// --- TEST D'EXPORT CSV ---
// $Statements = $container->get(StatementService::class)->getStatements(['limit' => 1]);
// $csvStatments = $container->get(StatementService::class)->exportStatementsToCsv($Statements);
// $fileName = 'test_lrs_export.csv';
// if (ob_get_level()) ob_end_clean();
// header('Content-Type: text/csv; charset=utf-8');
// header('Content-Disposition: attachment; filename="' . $fileName . '"');
// header('Pragma: no-cache');
// header('Expires: 0');
// echo $csvStatments;
// exit;

// Route par défaut : redirection vers la page d'accueil
$app->get('/', function (Request $request, Response $response) {
    return $response->withHeader('Location', 'Views/home.html')->withStatus(302);
});

// Chargement des routes
$routes = require_once(__DIR__ . '/../src/Routes/route_test.php');
$routes($app);

$exportRoutes = require_once(__DIR__ . '/../src/Routes/export_route.php');
$exportRoutes($app);

$app->run();
