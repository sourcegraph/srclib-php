<?php

namespace Sourcegraph\Tests\PHP;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\Grapher;

class GrapherTest extends TestCase
{
    public function testRun()
    {
        $filename = $this->getFixtureFullPath('003.constants.php');

        $grapher = new Grapher(BASE_PATH);
        $result = $grapher->run($filename);
    }
}
