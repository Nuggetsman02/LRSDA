<?php

namespace LRSDA\Server\Services;

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

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retourne une liste d'objets Statement
     */
    public function getStatements(array $filters = []): array
    {
        $allRawStatements = [];
        $moreUrl = null;

        // 1. BOUCLE DE PAGINATION (Récupération intégrale)
        do {
            if ($moreUrl) {
                $response = $this->client->get($moreUrl);
            } else {
                $response = $this->client->get('statements', [
                    'query' => $filters
                ]);
            }

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['statements'])) {
                $allRawStatements = array_merge($allRawStatements, $data['statements']);
            }

            $moreUrl = $data['more'] ?? null;

        } while (!empty($moreUrl));

        // 2. TRANSFORMATION EN OBJETS
        $statements = [];

        foreach ($allRawStatements as $raw) {
            
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
            $verbDisplay = $displayMap['fr-FR'] ?? current($displayMap) ?? 'unknown';
            
            $verb = new StatementVerb(
                $raw['verb']['id'] ?? '',
                $verbDisplay
            );

            // --- OBJECT ---
            $objDef = $raw['object']['definition'] ?? [];
            $objNameMap = $objDef['name'] ?? [];
            $objName = $objNameMap['fr-FR'] ?? current($objNameMap) ?? 'unknown';

            $object = new StatementObject(
                $raw['object']['id'] ?? '',
                $raw['object']['objectType'] ?? 'Activity',
                $objDef['type'] ?? '',
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

            // --- STATEMENT ---
            try {
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
            } catch (\Exception $e) {
                // Gestion erreur parsing date si nécessaire
                continue; 
            }
        }

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
            'Verb Display',
            'Timestamp',
            'Object Type',
            'Object ID',
            'Object Definition',
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
                $statement->getVerb()->getDisplay(),
                
                $statement->getTimestamp()->format('c'),

                $statement->getObject()->getObjectType(),
                $statement->getObject()->getId(),
                $statement->getObject()->getDefinition(),

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
