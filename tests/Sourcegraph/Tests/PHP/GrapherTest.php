<?php

namespace Sourcegraph\Tests\PHP;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\Grapher;

class GrapherTest extends TestCase
{
    public function testRun()
    {
        $filename = $this->getFixtureFullPath('500.complex.php');

        $grapher = new Grapher(BASE_PATH);
        $result = $grapher->run($filename);

        //echo json_encode($result, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }
}
