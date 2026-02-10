<?php

namespace LRSDA\Server\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use LRSDA\Server\Services\StatementService;
use LRSDA\Server\Services\XapiRegistry; 

class ExportController
{
    private StatementService $statementService;

    public function __construct(StatementService $statementService)
    {
        $this->statementService = $statementService;
    }

    public function export(Request $request, Response $response): Response
    {
        // 1. Récupération des choix utilisateur
        $params = $request->getParsedBody();
        // Décodage des filtres envoyés par le JS
        $Filters = json_decode($params['filters'] ?? '{}', true);

        // 2. Initialisation de la Registry
        $registry = XapiRegistry::getInstance();

        // --- CONSTRUCTION DU FILTRE CONNECTION API (MongoDB) ---
        $mongoFilter = [];

        // A. Filtre par Date (statement.stored)
        if (!empty($Filters['start_date']) && !empty($Filters['end_date'])) {
            $mongoFilter['statement.stored'] = [
                '$gte' => $Filters['start_date'] . 'T00:00:00Z',
                '$lte' => $Filters['end_date'] . 'T23:59:59Z'
            ];
        } elseif (!empty($Filters['start_date'])) {
             $mongoFilter['statement.stored']['$gte'] = $Filters['start_date'] . 'T00:00:00Z';
        }

        // B. Filtre par Verbe (CORRIGÉ POUR CHOIX MULTIPLES)
        if (!empty($Filters['verbs']) && is_array($Filters['verbs'])) {
            $verbIds = [];
            
            // On boucle sur tous les verbes sélectionnés (ex: 'loggedin', 'loggedout')
            foreach ($Filters['verbs'] as $verbName) {
                $verbInfo = $registry->getVerb($verbName, true);
                if ($verbInfo && isset($verbInfo['id'])) {
                    $verbIds[] = $verbInfo['id'];
                }
            }

            // Si on a trouvé des IDs valides
            if (!empty($verbIds)) {
                if (count($verbIds) === 1) {
                    // Un seul verbe : égalité simple
                    $mongoFilter['statement.verb.id'] = $verbIds[0];
                } else {
                    // Plusieurs verbes : on utilise l'opérateur $in
                    $mongoFilter['statement.verb.id'] = ['$in' => $verbIds];
                }
            }
        }

        // C. Filtre par Type d'Activité (CORRIGÉ POUR CHOIX MULTIPLES)
        if (!empty($Filters['objects']) && is_array($Filters['objects'])) {
            $typeUris = [];

            foreach ($Filters['objects'] as $objectName) {
                // On récupère le type (ex: http://adlnet.gov/expapi/activities/assessment)
                $uri = $registry->get($objectName, 'type'); 
                if ($uri) {
                    $typeUris[] = $uri;
                }
            }

            if (!empty($typeUris)) {
                if (count($typeUris) === 1) {
                    $mongoFilter['statement.object.definition.type'] = $typeUris[0];
                } else {
                    // Opérateur $in pour chercher l'un OU l'autre type
                    $mongoFilter['statement.object.definition.type'] = ['$in' => $typeUris];
                }
            }
        }

        // --- APPEL AU SERVICE ---
        $statements = $this->statementService->getStatements($mongoFilter);

        // 4. Export CSV (Inchangé)
        $csvContent = $this->statementService->exportStatementsToCsv($statements);

        $response->getBody()->write($csvContent);
        
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="export_lrs.csv"')
            ->withHeader('Cache-Control', 'no-cache');
    }
}