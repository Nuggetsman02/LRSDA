<?php 
$config = [

    /* This is the name of this authentication source, and will be used to access it later. */
    'default-sp' => [
        'saml:SP',
        'entityID' => 'LRS_data_analyst',
        'idp' =>'http://localhost:8080/simplesaml/saml2/idp/metadata.php',
        ],
];