<?php

namespace Sourcegraph\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function loadFixture($filename)
    {
        return file_get_contents(TESTS_DIR . '/fixtures/' . $filename);
    }
}
