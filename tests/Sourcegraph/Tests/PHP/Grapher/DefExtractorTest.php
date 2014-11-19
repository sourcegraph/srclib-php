<?php

namespace Sourcegraph\Tests\PHP\Grapher;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\Grapher\DefExtractor;

class DefExtractorTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testAnalyzer($filename, Array $expected)
    {
        $nodes = $this->loadNodeFixture($filename);

        $extractor = new DefExtractor();
        $result = $extractor->extract($filename, $nodes);

        $this->assertEquals($result, $expected);
    }

    public function provider()
    {
        return [
            ['001.functions.php', [
                [
                    'Kind' => 'function',
                    'Name' => 'bar',
                    'TreePath' => 'Foo/bar',
                    'Exported' => true,
                    'File' => '001.functions.php',
                    'Test' => false,
                    'DefStart' => 27,
                    'DefEnd' => 44,
                ]
            ]],
            ['002.classes.php', [
                [
                    'Kind' => 'class',
                    'Name' => 'Bar',
                    'TreePath' => 'Foo/Bar',
                    'Exported' => true,
                    'File' => '002.classes.php',
                    'Test' => false,
                    'DefStart' => 27,
                    'DefEnd' => 193,
                ], [
                    'Kind' => 'method',
                    'Name' => '__construct',
                    'TreePath' => 'Foo/Bar/__construct',
                    'Exported' => true,
                    'File' => '002.classes.php',
                    'Test' => false,
                    'DefStart' => 63,
                    'DefEnd' => 95,
                ], [
                    'Kind' => 'method',
                    'Name' => 'protectedMethod',
                    'TreePath' => 'Foo/Bar/protectedMethod',
                    'Exported' => false,
                    'File' => '002.classes.php',
                    'Test' => false,
                    'DefStart' => 104,
                    'DefEnd' => 143,
                ], [
                    'Kind' => 'method',
                    'Name' => 'privateMethod',
                    'TreePath' => 'Foo/Bar/privateMethod',
                    'Exported' => false,
                    'File' => '002.classes.php',
                    'Test' => false,
                    'DefStart' => 152,
                    'DefEnd' => 187,
                ], [
                    'Kind' => 'class',
                    'Name' => 'Foo',
                    'TreePath' => 'Foo/Foo',
                    'Exported' => true,
                    'File' => '002.classes.php',
                    'Test' => false,
                    'DefStart' => 199,
                    'DefEnd' => 264,
                ], [
                    'Kind' => 'method',
                    'Name' => 'extendedMethod',
                    'TreePath' => 'Foo/Foo/extendedMethod',
                    'Exported' => true,
                    'File' => '002.classes.php',
                    'Test' => false,
                    'DefStart' => 223,
                    'DefEnd' => 258,
                ]
            ]],
            ['003.constants.php', [
                [
                    'Kind' => 'constant',
                    'Name' => 'QUX',
                    'TreePath' => 'Foo/QUX',
                    'File' => '003.constants.php',
                    'Test' => false,
                    'DefStart' => 27,
                    'DefEnd' => 46,
                    'Exported' => true,
                ], [
                    'Kind' => 'constant',
                    'Name' => 'BAR',
                    //'TreePath' => 'Foo/QUX', TODO: extract TreePath from define
                    'File' => '003.constants.php',
                    'Test' => false,
                    'DefStart' => 51,
                    'DefEnd' => 71,
                    'Exported' => true,
                ]
            ]],
            ['004.properties.php', [
                [
                    'Kind' => 'class',
                    'Name' => 'Bar',
                    'TreePath' => 'Foo/Bar',
                    'File' => '004.properties.php',
                    'Test' => false,
                    'DefStart' => 27,
                    'DefEnd' => 152,
                    'Exported' => true,
                ], [
                    'Kind' => 'property',
                    'Name' => 'publicProperty',
                    'TreePath' => 'Foo/Bar/publicProperty',
                    'Exported' => true,
                    'File' => '004.properties.php',
                    'Test' => false,
                    'DefStart' => 51,
                    'DefEnd' => 74,
                ], [
                    'Kind' => 'property',
                    'Name' => 'privateProperty',
                    'TreePath' => 'Foo/Bar/privateProperty',
                    'Exported' => false,
                    'File' => '004.properties.php',
                    'Test' => false,
                    'DefStart' => 83,
                    'DefEnd' => 108,
                ], [
                    'Kind' => 'property',
                    'Name' => 'protectedProperty',
                    'TreePath' => 'Foo/Bar/protectedProperty',
                    'Exported' => false,
                    'File' => '004.properties.php',
                    'Test' => false,
                    'DefStart' => 117,
                    'DefEnd' => 146,
                ]
            ]],
            ['005.traits.php', [
                [
                    'Kind' => 'trait',
                    'Name' => 'Qux',
                    'TreePath' => 'Foo/Qux',
                    'File' => '005.traits.php',
                    'Test' => false,
                    'DefStart' => 27,
                    'DefEnd' => 77,
                    'Exported' => true,
                ], [
                    'Kind' => 'method',
                    'Name' => 'Bar',
                    'TreePath' => 'Foo/Qux/Bar',
                    'Exported' => true,
                    'File' => '005.traits.php',
                    'Test' => false,
                    'DefStart' => 47,
                    'DefEnd' => 71,
                ]
            ]],
            ['006.interfaces.php', [
                [
                    'Kind' => 'interface',
                    'Name' => 'Qux',
                    'TreePath' => 'Foo/Qux',
                    'File' => '006.interfaces.php',
                    'Test' => false,
                    'DefStart' => 27,
                    'DefEnd' => 92,
                    'Exported' => true,
                ], [
                    'Kind' => 'method',
                    'Name' => 'publicMethod',
                    'TreePath' => 'Foo/Qux/publicMethod',
                    'Exported' => true,
                    'File' => '006.interfaces.php',
                    'Test' => false,
                    'DefStart' => 55,
                    'DefEnd' => 86,
                ]
            ]],
        ];
    }
}
