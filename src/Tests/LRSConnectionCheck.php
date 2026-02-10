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
                'query' => ['limit' => 10]
            ]);

            $body = $response->getBody()->getContents();
            echo "<script>console.log(" . json_encode($body) . ");</script>";
            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            echo "Erreur Guzzle : " . $e->getMessage();
            return false;
        }
    }
}
