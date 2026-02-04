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
        $filters = $request->getQueryParams();

        $statements = $this->statementService->getStatements($filters);

        $csvContent = $this->statementService->exportStatementsToCsv($statements);

        $response->getBody()->write($csvContent);

        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Disposition', 'attachment; filename="lrs_export_' . date('Y-m-d_H-i') . '.csv"')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate')
            ->withHeader('Pragma', 'public');
    }
}
