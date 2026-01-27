<?php

namespace LRSDA\Server\Services;

use GuzzleHttp\Client;
use LRSDA\Server\LRSConnector\Configuration;
use LRSDA\Server\Models\Statement;

/**
 * Accès aux statements xAPI depuis le LRS
 */
class StatementService

{
    private Client $client;

    public function __construct()
    {
        $xapi = Configuration::getInstance()->xapi();

        $this->client = new Client([
            'base_uri' => $xapi['uri'] . '/', //s'assure qu'il y a un slash à la fin
            'headers'  => [
                'X-Experience-API-Version' => '1.0.1',
                'Authorization'            => $xapi['auth_key'],
                'Content-Type'             => 'application/json',
            ],
        ]);
    }

    /**
     * Retourne une liste d'objets Statement
     */
    public function getStatements(array $filters = []): array
    {
        $response = $this->client->get('statements', [
            'query' => $filters
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $statements = [];

        foreach ($data['statements'] ?? [] as $raw) {
            $statements[] = new Statement(
                $raw['id'],
                // $raw['actor']['name'] ?? '',
                // $raw['actor']['mbox'] ?? '',
                $raw['verb']['id'] ?? '',
                $raw['object']['id'] ?? ''
            );
        }

        return $statements;
    }
}
