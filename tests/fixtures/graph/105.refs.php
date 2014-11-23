<?php

use Sourcegraph\PHP\Grapher;
use Symfony\Component\Console\Input\InputOption;

function foo(Grapher $internal) {
    $external = new InputOption();
}
