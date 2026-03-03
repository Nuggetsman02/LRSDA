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

    public function __construct(StatementService $statementService, QueryService $queryService)
    {
        $this->statementService = $statementService;
        $this->queryService = $queryService;
    }

    // --------
    // METHODES PUBLIQUES 
    // --------

    public function export(Request $request, Response $response): Response
    {
        $params = $request->getParsedBody();
        $rawFilters = json_decode($params['filters'] ?? '{}', true);

        $mongoFilter = $this->queryService->buildMongoFilter($rawFilters);

        $statements = $this->statementService->getStatements($mongoFilter);
        $csvContent = $this->statementService->exportStatementsToCsv($statements);

        $response->getBody()->write($csvContent);
        
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="export_lrs.csv"')
            ->withHeader('Cache-Control', 'no-cache');
    }
}