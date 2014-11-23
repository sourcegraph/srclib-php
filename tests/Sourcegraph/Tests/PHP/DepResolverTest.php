<?php

namespace Sourcegraph\Tests\PHP;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\DepResolver;

class DepResolverTest extends TestCase
{
    public function testRun()
    {
        $unit = $this->getSourceUnitMock();

        $resolver = new DepResolver();
        $result = $resolver->run($unit);

        $this->assertCount(3, $result);
        $this->assertSame($result[0]['Raw'], 'nikic/php-parser');
        $this->assertSame($result[2]['Target'], [
            'ToRepoCloneURL' => 'https://github.com/sebastianbergmann/phpunit.git',
            'ToUnit' => 'phpunit/phpunit',
            'ToUnitType' => 'ComposerPackage',
            'ToVersionString' => '4.3.*',
            'ToRevSpec' => '2dab9d593997db4abcf58d0daf798eb4e9cecfe1'
        ]);
    }
}
