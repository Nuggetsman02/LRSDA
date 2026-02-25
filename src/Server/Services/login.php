<?php

$auth = new \SimpleSAML\Auth\Simple('default-sp');

$auth->requireAuth();
$attributes = $auth->getAttributes();