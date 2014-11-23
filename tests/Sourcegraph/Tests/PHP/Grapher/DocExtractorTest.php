<?php

namespace Sourcegraph\Tests\PHP\Grapher;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\Grapher\DocExtractor;

class DocExtractorTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testAnalyzer($filename, Array $expected)
    {
        $nodes = $this->loadNodeFixture($filename);
        $unit = $this->getSourceUnitMock();

        $extractor = new DocExtractor();
        $result = $extractor->extract($unit, $filename, $nodes);

        $this->assertEquals($result, $expected);
    }

    public function provider()
    {
        return [
            ['001.functions.php', [
                [
                    'TreePath' => 'Foo/bar',
                    'Format' => 'text/plain',
                    'Data' => "/**\n * Documentation\n */",
                    'File' => '001.functions.php',
                    'Start' => 0,
                    'End' => 0,
                ]
            ]],
            ['002.classes.php', [
                [
                    'TreePath' => 'Foo/Bar',
                    'Format' => 'text/plain',
                    'Data' => '/* doc */',
                    'File' => '002.classes.php',
                    'Start' => 0,
                    'End' => 0,
                ]
            ]],
            ['003.constants.php', [
                [
                    'TreePath' => 'Foo/QUX',
                    'Format' => 'text/plain',
                    'Data' => '/* doc */',
                    'File' => '003.constants.php',
                    'Start' => 0,
                    'End' => 0,
                ]
            ]],
            ['004.properties.php', [
                [
                    'TreePath' => 'Foo/Bar/protectedProperty',
                    'Format' => 'text/plain',
                    'Data' => '/* doc */',
                    'File' => '004.properties.php',
                    'Start' => 0,
                    'End' => 0,
                ]
            ]],
            ['005.traits.php', [
                [
                    'TreePath' => 'Foo/Qux',
                    'Format' => 'text/plain',
                    'Data' => '/* doc */',
                    'File' => '005.traits.php',
                    'Start' => 0,
                    'End' => 0,
                ]
            ]],
            ['006.interfaces.php', [
                [
                    'TreePath' => 'Foo/Qux',
                    'Format' => 'text/plain',
                    'Data' => '/* doc */',
                    'File' => '006.interfaces.php',
                    'Start' => 0,
                    'End' => 0,
                ],
                [
                    'TreePath' => 'Foo/Qux/publicMethod',
                    'Format' => 'text/plain',
                    'Data' => '/* Document */',
                    'File' => '006.interfaces.php',
                    'Start' => 0,
                    'End' => 0,
                ]
            ]],
        ];
    }
}
