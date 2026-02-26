<?php

$httpUtils = new \SimpleSAML\Utils\HTTP();

$config = [
    'baseurlpath' => 'http://localhost:8100/saml/',
    'application' => [],

    'cachedir'    => '/workspace/simplesamlphp/cache',
    'loggingdir'  => '/workspace/simplesamlphp/log',
    'datadir'     => '/workspace/simplesamlphp/data',
    'metadatadir' => '/workspace/simplesamlphp/metadata',

    'technicalcontact_name' => 'Damien Depluvrez',
    'technicalcontact_email' => 'damien.depluvrez@uliege.be',

    'timezone' => 'Europe/Brussels',


    /**********************************
     | SECURITY CONFIGURATION OPTIONS |
     **********************************/

    'secretsalt' => 'random_salt_value_for_lrsda',

    'auth.adminpassword' => 'password_secret',

    'admin.protectmetadata' => true,
    'admin.checkforupdates' => true,

    // 'trusted.url.domains' => ['XXX.smart.uliege.be'],
    'trusted.url.regex' => false,
    'enable.http_post' => false,
    'assertion.allowed_clock_skew' => 180,


    /************************
     | ERRORS AND DEBUGGING |
     ************************/
    'debug' => [
        'saml' => false,
        'backtraces' => true,
        'validatexml' => false,
    ],
    'showerrors' => true, //false en prod
    'errorreporting' => true,


    /**************************
     | LOGGING AND STATISTICS |
     **************************/
    /*
     * Define the minimum log level to log. Available levels:
     * - SimpleSAML\Logger::ERR     No statistics, only errors
     * - SimpleSAML\Logger::WARNING No statistics, only warnings/errors
     * - SimpleSAML\Logger::NOTICE  Statistics and errors
     * - SimpleSAML\Logger::INFO    Verbose logs
     * - SimpleSAML\Logger::DEBUG   Full debug logs - not recommended for production
     *
     */
    'logging.level' => SimpleSAML\Logger::WARNING,
    'logging.handler' => 'syslog',
    'logging.facility' => defined('LOG_LOCAL5') ? constant('LOG_LOCAL5') : LOG_USER,
    'logging.processname' => 'simplesamlphp',
    'logging.logfile' => 'simplesamlphp.log',
    'statistics.out' => [ // Log statistics to the normal log.
    ],


    /***********************
     | PROXY CONFIGURATION |
     ***********************/
    'proxy' => null,


    /**************************
     | DATABASE CONFIGURATION |
     **************************/
    // 'database.dsn' => 'mysql:host=localhost;dbname=saml',
    // 'database.username' => 'simplesamlphp',
    // 'database.password' => 'secret',
    // 'database.options' => [],
    // 'database.prefix' => '',
    // 'database.driver_options' => [],
    // 'database.persistent' => false,
    // 'database.secondaries' => [],


    /*************
     | PROTOCOLS |
     *************/
    'enable.saml20-idp' => false,
    'enable.adfs-idp' => false,

    /***********
     | MODULES |
     ***********/
    'module.enable' => [
        'exampleauth' => false,
        'core' => true,
        'admin' => true,
        'saml' => true
    ],


    /*************************
     | SESSION CONFIGURATION |
     *************************/
    'session.duration' => 8 * (60 * 60), // 8 hours.
    'session.datastore.timeout' => (4 * 60 * 60), // 4 hours
    'session.state.timeout' => (60 * 60), // 1 hour
    'session.cookie.name' => 'SMARTSAMLSessionID',
    'session.cookie.lifetime' => 0,
    'session.cookie.path' => '/',
    'session.cookie.domain' => '',
    'session.cookie.secure' => false, // Should be true in production when using HTTPS.
    'session.cookie.samesite' => $httpUtils->canSetSameSiteNone() ? 'None' : null,

    'session.phpsession.cookiename' => 'SMARTSAMLTestServer',
    'session.phpsession.savepath' => null,
    'session.phpsession.httponly' => true,

    'session.authtoken.cookiename' => 'SMARTSAMLAuthToken',
    'session.rememberme.enable' => false,
    'session.rememberme.checked' => false,
    'session.rememberme.lifetime' => (14 * 86400),


    /**************************
     | MEMCACHE CONFIGURATION |
     **************************/
    'memcache_store.servers' => [
        [
            ['hostname' => 'localhost'],
        ],
    ],
    'memcache_store.prefix' => '',
    'memcache_store.expires' => 36 * (60 * 60), // 36 hours.
];
