<?php

namespace Sourcegraph\Tests\PHP;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\Grapher;

class GrapherTest extends TestCase
{
    public function testRun()
    {
        $filename = $this->getFixtureFullPath('500.complex.php');

        $unit = $this->getMock(
            'Sourcegraph\PHP\SourceUnit',
            ['getFiles', 'getPackageName', 'getType', 'getRepository']
        );

        $unit->method('getFiles')->willReturn([$filename]);

        $grapher = new Grapher(BASE_PATH);
        $result = $grapher->run($unit);

        $this->assertCount(15, $result['Defs']);
        $this->assertCount(4, $result['Docs']);
        $this->assertCount(13, $result['Refs']);
    }
}
