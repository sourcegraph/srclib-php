<?php

namespace Sourcegraph\Tests;

use Sourcegraph\PHP\Grapher;
use ReflectionMethod;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function getFixtureFullPath($filename)
    {
        return TEST_PATH . '/fixtures/graph/' . $filename;
    }

    public function loadCodeFixture($filename)
    {
        return file_get_contents($this->getFixtureFullPath($filename));
    }

    public function loadNodeFixture($filename)
    {
        $filename = $this->getFixtureFullPath($filename);
        $grapher = new Grapher(BASE_PATH);

        $method = new ReflectionMethod('Sourcegraph\PHP\Grapher', 'getNodes');
        $method->setAccessible(true);

        return $method->invokeArgs($grapher, [$filename, []]);
    }

    public function loadUnitFixture()
    {
        $filename = $this->getFixtureFullPath('../unit.json');

        return json_decode(file_get_contents($filename));
    }
}
