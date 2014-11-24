<?php

namespace Sourcegraph\Tests\PHP;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\Grapher;

class GrapherTest extends TestCase
{
    public function setUp()
    {
        $this->unit = $this->getMock(
            'Sourcegraph\PHP\SourceUnit',
            [
                'getFiles', 'getPackageName', 'getType', 'getRepository',
                'getDependencies', 'getRequiredVersion', 'getCommit'
            ]
        );

    }

    public function testRun()
    {
        $filename = $this->getFixtureFullPath('500.complex.php');
        $this->unit->method('getFiles')->willReturn([$filename]);

        $grapher = new Grapher(BASE_PATH);
        $result = $grapher->run($this->unit);

        $this->assertCount(15, $result['Defs']);
        $this->assertCount(4, $result['Docs']);
        $this->assertCount(13, $result['Refs']);
    }

    public function testRunInvalid()
    {
        $filename = $this->getFixtureFullPath('000.invalid.php');
        $this->unit->method('getFiles')->willReturn([$filename]);

        $grapher = new Grapher(BASE_PATH);
        $result = $grapher->run($this->unit);

        $this->assertCount(0, $result['Defs']);
        $this->assertCount(0, $result['Docs']);
        $this->assertCount(0, $result['Refs']);
    }
}
