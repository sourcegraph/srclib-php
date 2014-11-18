<?php

namespace Sourcegraph\Tests\PHP;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\Grapher;

class GrapherTest extends TestCase
{
    public function testAnalyzer()
    {
        $code = $this->loadFixture('001.namespaces.php');

        $grapher = new Grapher();
        $result = $grapher->run($code);

        print_r($result);
    }
}
