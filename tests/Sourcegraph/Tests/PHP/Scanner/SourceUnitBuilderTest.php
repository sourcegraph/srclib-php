<?php

namespace Sourcegraph\Tests\PHP\Grapher;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\Scanner\SourceUnitBuilder;

class SourceUnitBuilderTest extends TestCase
{
    private $builder;

    protected function setUp()
    {
        $this->builder = new SourceUnitBuilder();
    }

    public function testBuildName()
    {
        $result = $this->builder->build(BASE_PATH);
        $this->assertSame($result['Name'], 'sourcegraph/srclib-php');
    }

    public function testBuildType()
    {
        $result = $this->builder->build(BASE_PATH);
        $this->assertSame($result['Type'], 'ComposerPackage');
    }

    public function testBuildGlob()
    {
        $result = $this->builder->build(BASE_PATH);
        $this->assertSame($result['Globs'], []);
    }

    public function testBuildFiles()
    {
        $path = TEST_PATH . '/fixtures/scanner/monolog/';
        $result = $this->builder->build($path);
        $this->assertSame($result['Files'], ['src/Monolog/Logger.php']);
    }

    public function testBuildDependencies()
    {
        $path = TEST_PATH . '/fixtures/scanner/monolog/';
        $result = $this->builder->build($path);
        $this->assertSame($result['Dependencies'], [
            'psr/log',
            'phpunit/phpunit',
            'graylog2/gelf-php',
            'raven/raven',
            'ruflin/elastica',
            'doctrine/couchdb',
            'aws/aws-sdk-php',
            'videlalvaro/php-amqplib',
            'rollbar/rollbar'
        ]);
    }

    public function testBuildDataPSR0()
    {
        $result = $this->builder->build(BASE_PATH);
        $this->assertSame($result['Data'], [
            'namespaces' => ['Sourcegraph\PHP']
        ]);
    }

    public function testBuildDataPSR4()
    {
        $path = TEST_PATH . '/fixtures/scanner/monolog/';
        $result = $this->builder->build($path);
        $this->assertSame($result['Data'], [
            'namespaces' => ['Monolog\\']
        ]);
    }

    public function testBuildOps()
    {
        $result = $this->builder->build(BASE_PATH);
        $this->assertSame(
            $result['Ops'],
            ['depresolve' => null, 'graph' => null]
        );
    }}
