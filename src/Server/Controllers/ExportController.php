<?php

namespace LRSDA\Server\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request; 
use Psr\Http\Message\ResponseInterface as Response;
use LRSDA\Server\Services\StatementService;

class ExportController
{
    private StatementService $statementService;

    public function __construct(StatementService $statementService)
    {
        $this->statementService = $statementService;
    }

    public function export(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $filters = [];
        
        // Extraction sécurisée des filtres (verbes, dates) selon le document
        if (!empty($params['verb'])) {
            $filters['verb'] = $params['verb'];
        }

        // On passe les filtres au service qui, lui, utilisera Guzzle Client
        $data = $this->statementService->getStatements($filters);

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
