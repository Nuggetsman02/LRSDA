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
        $Filters = json_decode($params['filters'] ?? '{}', true);

        // 2. Initialisation de la Registry
        $registry = XapiRegistry::getInstance();

        $xApiFilters = [];

        // --- A. FILTRES POUR LE LRS (Dates & Verbes) ---

        // Dates
        if (!empty($Filters['start_date'])) {
            $xApiFilters['since'] = $Filters['start_date'] . 'T00:00:00Z';
        }
        if (!empty($Filters['end_date'])) {
            $xApiFilters['until'] = $Filters['end_date'] . 'T23:59:59Z';
        }

        // Verbes (C'est ici que ton problème "tous les verbes" est corrigé)
        if (!empty($Filters['verbs'])) {
            $verbInfo = $registry->getVerb($Filters['verbs'][0], true);
            
            if ($verbInfo && isset($verbInfo['id'])) {
                $xApiFilters['verb'] = $verbInfo['id'];
            }
        }

        // --- B. APPEL AU LRS ---
        $statements = $this->statementService->getStatements($xApiFilters);


        // --- C. FILTRE COMPLÉMENTAIRE PHP (Pour le Type d'Activité) ---

        if (!empty($Filters['objects'])) {
            $targetType = $registry->get($Filters['objects'][0], 'type');

            if ($targetType) {
                $statements = array_filter($statements, function($stmt) use ($targetType) {
                    return $stmt->getObject()->getDefinition() === $targetType;
                });
            }
        }

        // 4. Export CSV
        $csvContent = $this->statementService->exportStatementsToCsv($statements);

        $response->getBody()->write($csvContent);
        
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="export_lrs.csv"')
            ->withHeader('Cache-Control', 'no-cache');
    }
}