<?php

namespace LRSDA\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class LRSConnectionCheck
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // requete simple pour tester la connexion au LRS
    public function pingLRS(): bool
    {
        try {
            $response = $this->client->request('GET', 'statements', [
                'query' => ['limit' => 1]
            ]);

            // Exemple de requête pour tester la connexion et récupérer une déclaration spécifique à l'aide de filtres
            // $feedbackPartType = 'http://smart.uliege.be/ulla/feedback/part';
            // $filter = [
            //     'statement.object.definition.type' => $feedbackPartType
            // ];
            // $response = $this->client->request('GET', '/api/connection/statement', [
            //     'query' => [
            //         'first'  => 1,
            //         'filter' => json_encode($filter)
            //     ]
            // ]);

            $body = $response->getBody()->getContents();
            echo "<script>console.log(" . json_encode($body) . ");</script>";
            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            echo "Erreur Guzzle : " . $e->getMessage();
            return false;
        }
    }
}
