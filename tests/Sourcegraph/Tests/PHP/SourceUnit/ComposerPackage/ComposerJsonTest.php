<?php

namespace Sourcegraph\Tests\PHP\SourceUnit\ComposerPackage;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\SourceUnit\ComposerPackage\ComposerJson;

class ComposerJsonTest extends TestCase
{
    public function testGetName()
    {
        $package = new ComposerJson(BASE_PATH);
        $this->assertSame($package->getName(), 'sourcegraph/srclib-php');
    }

    /**
     * @expectedException Sourcegraph\PHP\SourceUnit\FileNotFound
     */
    public function testRead()
    {
        $package = new ComposerJson('/tmp/');
    }

    public function testGetDependencies()
    {
        $path = TEST_PATH . '/fixtures/scanner/monolog/';
        $package = new ComposerJson($path);
        $this->assertSame($package->getDependencies(), [
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

    public function testGetRequiredVersion()
    {
        $package = new ComposerJson(BASE_PATH);
        $this->assertSame(
            $package->getRequiredVersion('symfony/console'),
            '2.5.*'
        );
    }

    public function testGetNamespacesPSR0()
    {
        $package = new ComposerJson(BASE_PATH);
        $this->assertSame($package->getNamespaces(), ['Sourcegraph\PHP']);
    }

    public function testGetNamespacesPSR4()
    {
        $path = TEST_PATH . '/fixtures/scanner/monolog/';
        $package = new ComposerJson($path);
        $this->assertSame($package->getNamespaces(), ['Monolog\\']);
    }
}

