<?php

namespace LRSDA\Server\LRSConnector;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use LRSDA\Server\LRSConnector\Configuration;

class LRSConnectionCheck
{
    private Client $client;

    //constructeur qui initialise le client GuzzleHTTP avec les paramètres de configuration
    public function __construct()
    {
        $conf = Configuration::getInstance();
        $xapi = $conf->xapi();

        $this->client = new Client([
            'base_uri' => $xapi['uri'] . '/', //s'assure qu'il y a un slash à la fin
            'headers'  => [
                'X-Experience-API-Version' => '1.0.1',
                'Authorization'            => $xapi['auth_key'],
                'Content-Type'             => 'application/json',
            ],
            'http_errors' => true,
        ]);
    }

    // requete simple pour tester la connexion au LRS
    public function pingLRS(): bool
    {
        try {
            $response = $this->client->request('GET', 'statements', [
                'query' => ['limit' => 1]
            ]);
            $body = $response->getBody()->getContents();
            echo htmlspecialchars($body);
            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            echo "Erreur Guzzle : " . $e->getMessage();
            return false;
        }
    }
}
