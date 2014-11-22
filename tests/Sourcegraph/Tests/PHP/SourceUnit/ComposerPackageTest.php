<?php

namespace Sourcegraph\Tests\PHP\SourceUnit;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\SourceUnit\ComposerPackage;

class ComposerPackageTest extends TestCase
{
    public function testGetName()
    {
        $package = new ComposerPackage(BASE_PATH);
        $this->assertSame($package->getName(), 'sourcegraph/srclib-php');
    }

    public function testGetFiles()
    {
        $path = TEST_PATH . '/fixtures/scanner/monolog/';
        $package = new ComposerPackage($path);
        $this->assertSame($package->getFiles(), ['src/Monolog/Logger.php']);
    }


    public function testGetDependencies()
    {
        $path = TEST_PATH . '/fixtures/scanner/monolog/';
        $package = new ComposerPackage($path);
        $this->assertCount(9, $package->getDependencies());
    }

    public function testBuildDataPSR0()
    {
        $package = new ComposerPackage(BASE_PATH);
        $this->assertCount(1, $package->getNamespaces());
    }

    public function testToArrayOps()
    {
        $package = new ComposerPackage(BASE_PATH);
        $result = $package->toArray();

        $this->assertSame(
            $result['Ops'],
            ['depresolve' => null, 'graph' => null]
        );
    }

    public function testToArrayType()
    {
        $package = new ComposerPackage(BASE_PATH);
        $result = $package->toArray();
        $this->assertSame($result['Type'], 'ComposerPackage');
    }

    public function testToArrayGlob()
    {
        $package = new ComposerPackage(BASE_PATH);
        $result = $package->toArray();
        $this->assertSame($result['Globs'], []);
    }

    public function testToArrayFiles()
    {
        $path = TEST_PATH . '/fixtures/scanner/monolog/';
        $package = new ComposerPackage($path);
        $result = $package->toArray();
        $this->assertCount(1, $result['Files']);
    }

    public function testToArrayDependencies()
    {
        $package = new ComposerPackage(BASE_PATH);
        $result = $package->toArray();
        $this->assertCount(3, $result['Dependencies']);
    }

    public function testToArrayNamespaces()
    {
        $package = new ComposerPackage(BASE_PATH);
        $result = $package->toArray();
        $this->assertCount(1, $result['Data']['namespaces']);
    }
}

