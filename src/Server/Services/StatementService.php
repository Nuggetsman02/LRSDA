<?php

namespace LRSDA\Server\Services;

use GuzzleHttp\Client;
use LRSDA\Server\LRSConnector\Configuration;
use LRSDA\Server\Models\{
    Statement,
    StatementAccount,
    StatementActor,
    StatementVerb,
    StatementObject,
    StatementAuthority
};

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

            $ActorAccountRaw = $raw['actor']['account'] ?? [];
            $ActorAccount = new StatementAccount(
                $ActorAccountRaw['name'] ?? '',
                $ActorAccountRaw['homePage'] ?? ''
            );

            $actor = new StatementActor(
                $raw['actor']['objectType'] ?? 'Agent',
                $ActorAccount
            );

            $displayMap = $raw['verb']['display'] ?? [];
            $displayText = $displayMap['fr-FR'] ?? current($displayMap) ?? 'unknown';

            $verb = new StatementVerb(
                $raw['verb']['id'] ?? '',
                (string)$displayText
            );

            $object = new StatementObject(
                $raw['object']['objectType'] ?? '',
                $raw['object']['id'] ?? '',
                $raw['object']['definition']['type'] ?? ''
            );

            $authorityAccountRaw = $raw['authority']['account'] ?? [];
            $authorityAccount = new StatementAccount(
                $authorityAccountRaw['name'] ?? '',
                $authorityAccountRaw['homePage'] ?? '',
            );

            $authority = new StatementAuthority(
                $raw['authority']['objectType'] ?? 'Agent',
                $raw['authority']['name'] ?? '',
                $authorityAccount
            );

            $statements[] = new Statement(
                $raw['id'] ?? '',
                $actor,
                $verb,
                new \DateTime($raw['timestamp'] ?? 'now'),
                $object,
                new \DateTime($raw['stored'] ?? 'now'),
                $authority,
                $raw['version'] ?? 'unknown'
            );
        }

        return $statements;
    }
}
