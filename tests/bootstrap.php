<?php
define('TEST_PATH', __DIR__);
define('BASE_PATH', TEST_PATH . '/../');

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('Sourcegraph\Tests', __DIR__);
