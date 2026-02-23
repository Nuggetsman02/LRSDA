<?php

namespace LRSDA\Server\Services;

use LRSDA\Server\Services\XapiRegistry;
use GuzzleHttp\Client;
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
    private XapiRegistry $registry;

    public function __construct(Client $client, XapiRegistry $registry)
    {
        $this->client = $client;
        $this->registry = $registry;

    }

    /**
     * Retourne une liste d'objets Statement
     */
    public function getStatements(array $filters = []): array
    {
        $statements = [];
        $afterCursor = null;
        $hasNextPage = true;

        // Préparation du filtre JSON
        $jsonFilter = empty($filters) ? '{}' : json_encode($filters);

        do {
            // Paramètres de la requête
            $queryParams = [
                'filter' => $jsonFilter,
                'first'  => 500,
                'sort'   => json_encode(['stored' => -1]) 
            ];

            if ($afterCursor) {
                $queryParams['after'] = $afterCursor;
            }

            // Appel avec le slash au début pour éviter les problèmes d'URL relative
            $response = $this->client->get('/api/connection/statement', [
                'query' => $queryParams
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['edges'])) {
                break;
            }

            foreach ($data['edges'] as $edge) {
                $mongoDoc = $edge['node'] ?? null;

                if (!$mongoDoc || !isset($mongoDoc['statement'])) {
                    continue;
                }

                $raw = $mongoDoc['statement'];

                // --- ACTOR ---
                $ActorAccountRaw = $raw['actor']['account'] ?? [];
                $ActorAccount = new StatementAccount(
                    $ActorAccountRaw['name'] ?? '',
                    $ActorAccountRaw['homePage'] ?? ''
                );

                $actor = new StatementActor(
                    $raw['actor']['objectType'] ?? 'Agent',
                    $ActorAccount
                );

                // --- VERB ---
                $displayMap = $raw['verb']['display'] ?? [];
                $verbDisplay = $displayMap['fr-FR'] ?? $displayMap['en-US'] ?? current($displayMap) ?? 'unknown';

                // Tentative de récupération du nom du verb depuis l'id', sinon 'unknown'
                $verbName = $this->registry->verbReverseLookup($raw['verb']['id'] ?? '');
                
                $verb = new StatementVerb(
                    $raw['verb']['id'] ?? '',
                    $verbDisplay,
                    $verbName
                );

                // --- OBJECT ---
                // Tentative de récupération du nom de l'objet depuis le type de la définition, sinon 'unknown'
                $objName = $this->registry->activityReverseLookup($raw['object']['definition']['type'] ?? '');

                $object = new StatementObject(
                    $raw['object']['objectType'] ?? '',
                    $raw['object']['id'] ?? '',
                    $raw['object']['definition']['type'] ?? '',
                    $objName
                );

                // --- AUTHORITY ---
                $authRaw = $raw['authority'] ?? [];
                $authAccountRaw = $authRaw['account'] ?? [];
                
                $authAccount = new StatementAccount(
                    $authAccountRaw['name'] ?? 'unknown',
                    $authAccountRaw['homePage'] ?? 'unknown'
                );

                $authority = new StatementAuthority(
                    $authRaw['objectType'] ?? 'Group',
                    $authRaw['name'] ?? 'unknown',
                    $authAccount
                );

                // --- DATES ---
                $storedDate = $mongoDoc['stored'] ?? $raw['stored'] ?? 'now';
                $timestampDate = $raw['timestamp'] ?? 'now';

                // --- STATEMENT ---
                try {
                    $statements[] = new Statement(
                        $raw['id'] ?? '',
                        $actor,
                        $verb,
                        new \DateTime($timestampDate),
                        $object,
                        new \DateTime($storedDate),
                        $authority,
                        $raw["version"] ?? 'unknown'
                    );
                } catch (\Exception $e) {
                    continue; 
                }
            }

            // Gestion du curseur
            if (isset($data['pageInfo'])) {
                $hasNextPage = $data['pageInfo']['hasNextPage'] ?? false;
                $afterCursor = $data['pageInfo']['endCursor'] ?? null;
            } else {
                $hasNextPage = false;
            }

        } while ($hasNextPage && !empty($afterCursor));

        return $statements;
    }

    /**
     * Transforme une liste d'objets Statement en fichier csv
     */
    public function exportStatementsToCsv(array $statements): string
    {
        // Ouverture d'un flux mémoire pour écrire le CSV
        $output = fopen('php://temp', 'r+');

        // En-têtes CSV
        fputcsv($output, [
            'Statement ID',
            'Actor Type',
            'Actor Account Name',
            'Actor Account HomePage',
            'Verb ID',
            'Verb Name',
            'Verb Display',
            'Timestamp',
            'Object Type',
            'Object ID',
            'Object Definition',
            'Object Name',
            'Stored',
            'Authority Type',
            'Authority Name',
            'Authority Account Name',
            'Authority Account HomePage',
            'Version'
        ]);

        // Écriture des données des statements
        foreach ($statements as $statement) {
            fputcsv($output, [
                $statement->getId(),

                $statement->getActor()->getObjectType(),
                $statement->getActor()->getAccount()->getName(),
                $statement->getActor()->getAccount()->getHomePage(),

                $statement->getVerb()->getId(),
                $statement->getVerb()->getName(),
                $statement->getVerb()->getDisplay(),
                
                $statement->getTimestamp()->format('c'),

                $statement->getObject()->getObjectType(),
                $statement->getObject()->getId(),
                $statement->getObject()->getDefinition(),
                $statement->getObject()->getName(),

                $statement->getStored()->format('c'),

                $statement->getAuthority()->getObjectType(),
                $statement->getAuthority()->getName(),
                $statement->getAuthority()->getAccount()->getName(),
                $statement->getAuthority()->getAccount()->getHomePage(),

                $statement->getVersion()
            ]);
        }

        // Retour au début du flux pour lire le contenu
        rewind($output);

        // Récupération du contenu CSV
        $csvContent = stream_get_contents($output);

        // Fermeture du flux
        fclose($output);

        return $csvContent;
    }
}
