<?php

namespace LRSDA\Server\LRSConnector;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use LRSDA\Server\LRSConnector\Configuration;
use Tincan\RemoteLRS;

class LRSConnectionCheck
{

    private Client $client;

    // --------------------
    // PROPERTIES
    // --------------------
    protected $lrs = null;
    protected $toRemote = true;
    protected $batchMode = false;
    protected $statements = [];
    // for debug purpose, i.e. deleting statements later on test execution
    protected $statementsId = [];
    public function __construct()
    {
        $conf = Configuration::getInstance();
        $this->lrs = new RemoteLRS(
            $conf->xapi()['uri'],
            '1.0.1',
            $conf->xapi()['auth_key']
        );

        if (isset($GLOBALS['toRemote'])) {
            $this->toRemote = $GLOBALS['toRemote'];
        }
        $this->batchMode = false;
    }

    public function pingLRS(): bool
    {
        try {
            $response = $this->client->get('statements', [
                'query' => ['limit' => 1],
            ]);
        } catch (GuzzleException $e) {
            return false;
        }
        return $response->getStatusCode() === 200;
    }
}
