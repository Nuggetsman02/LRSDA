<?php

namespace LRSDA\Server\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use LRSDA\Server\Services\StatementService;
use LRSDA\Server\Services\QueryService;

class ExportController
{
    private StatementService $statementService;
    private QueryService $queryService;

    // On injecte les deux services via le constructeur
    public function __construct(StatementService $statementService, QueryService $queryService)
    {
        $this->statementService = $statementService;
        $this->queryService = $queryService;
    }

    public function export(Request $request, Response $response): Response
    {
        // 1. Récupération des choix utilisateur bruts
        $params = $request->getParsedBody();
        $rawFilters = json_decode($params['filters'] ?? '{}', true);

        // 2. Délégation : Le QueryService fabrique la requête base de données
        $mongoFilter = $this->queryService->buildMongoFilter($rawFilters);

        // 3. Délégation : Le StatementService récupère et formate les données
        $statements = $this->statementService->getStatements($mongoFilter);
        $csvContent = $this->statementService->exportStatementsToCsv($statements);

        // 4. Réponse HTTP
        $response->getBody()->write($csvContent);
        
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="export_lrs.csv"')
            ->withHeader('Cache-Control', 'no-cache');
    }
}