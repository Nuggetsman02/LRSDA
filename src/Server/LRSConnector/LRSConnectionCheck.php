<?php

namespace LRSDA\Server\LRSConnector;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use LRSDA\Server\LRSConnector\Configuration;

class LRSConnectionCheck
{
    private Client $client;

    public function __construct()
    {
        $conf = Configuration::getInstance();
        $xapi = $conf->xapi();

        $this->client = new Client([
            'base_uri' => $xapi['uri'] . '/', // On s'assure qu'il y a un slash à la fin
            'headers'  => [
                'X-Experience-API-Version' => '1.0.1',
                'Authorization'            => $xapi['auth_key'],
                'Content-Type'             => 'application/json',
            ],
            'timeout'  => 15.0,
            'http_errors' => true,
        ]);
    }

    public function pingLRS(): bool
    {
        try {
            $response = $this->client->request('GET', 'statements', [
                'query' => ['limit' => 1]
            ]);

            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            return false;
        }
    }
}
