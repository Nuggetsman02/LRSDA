<?php

namespace LRSDA\Server\Controllers;

use LRSDA\Client\Services\StatementService;

/**
 * Controller responsable des Statements
 */
class StatementController
{
    private StatementService $statementModel;

    /**
     * Injection du model
     */
    public function __construct()
    {
        $this->statementModel = new StatementService();
    }

    /**
     * Récupère des statements selon des filtres
     *
     * @param array $filters
     * @return array
     */
    public function fetch(array $filters = []): array
    {
        return $this->statementModel->getStatements($filters);
    }
}
