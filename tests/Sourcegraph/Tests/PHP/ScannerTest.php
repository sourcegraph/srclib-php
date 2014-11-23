<?php

namespace Sourcegraph\Tests\PHP;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\Scanner;

class ScannerTest extends TestCase
{
    public function testRun()
    {
        $scanner = new Scanner();
        $result = $scanner->run(BASE_PATH);

        $this->assertCount(1, $result);
        $this->assertSame($result[0]['Name'], 'sourcegraph/srclib-php');
    }
}
