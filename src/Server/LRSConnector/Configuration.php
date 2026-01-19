<?php

namespace LRSDA\Server\LRSConnector;

class Configuration
{
    // ---------------------
    // STATIC PUBLIC METHODS
    // ---------------------
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Configuration();
        }
        return self::$instance;
    }
    // --------------------
    // PROPERTIES
    // --------------------
    protected static $instance;
    protected $config = [];
    // --------------------
    // PUBLIC METHODS
    // --------------------
    private function __construct()
    {
        $configFile = "config.json";
        if (!file_exists($configFile)) {
            $configFile = "../" . $configFile;
        }
        if (!file_exists($configFile)) {
            throw new \Exception("Unable to find configuration file.");
        }

        $this->config = json_decode(file_get_contents($configFile), true);
    }
    /**
     * to prevent clone of this Singleton
     * @codeCoverageIgnore
     */
    private function __clone()
    {
        // nothing to do as cloning is prohibited
    }

    // accessors
    public function xapi(): array
    {
        if (!isset($this->config['xapi'])) {
            // @codeCoverageIgnoreStart
            // exception case
            throw new \Exception(__METHOD__ . " : [xapi] config should be set to use LRSConnector. RTFM.");
            // @codeCoverageIgnoreEnd
        }
        return $this->config['xapi'];
    }
}
