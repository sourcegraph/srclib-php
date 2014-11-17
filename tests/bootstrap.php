<?php
define('TESTS_DIR', __DIR__);
$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('Sourcegraph\Tests', __DIR__);
