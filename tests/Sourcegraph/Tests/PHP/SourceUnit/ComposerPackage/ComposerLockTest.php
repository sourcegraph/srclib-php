<?php

namespace Sourcegraph\Tests\PHP\SourceUnit\ComposerPackage;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\SourceUnit\ComposerPackage\ComposerLock;

class ComposerLockTest extends TestCase
{
    public function testGetPackageName()
    {
        $lock = new ComposerLock(BASE_PATH);
        $package = $lock->getPackageName('Symfony\Component\Console\Input');
        $this->assertSame('symfony/console', $package);
    }

    public function testGetPackageNameNotFound()
    {
        $lock = new ComposerLock(BASE_PATH);
        $package = $lock->getPackageName('Foo\Bar');
        $this->assertNull($package);
    }

    public function testGetRepository()
    {
        $path = TEST_PATH . '/fixtures/scanner/monolog/';
        $lock = new ComposerLock($path);
        $this->assertSame(
            'https://github.com/php-fig/log.git',
            $lock->getRepository('psr/log')
        );
    }

    public function testGetRepositoryNotFound()
    {
        $lock = new ComposerLock(BASE_PATH);
        $this->assertNull($lock->getRepository('foo'));
    }
}

