<?php

namespace Sourcegraph\Tests\PHP\Grapher;

use Sourcegraph\Tests\TestCase;
use Sourcegraph\PHP\Grapher\RefExtractor;

class RefExtractorTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testAnalyzer($filename, Array $expected)
    {
        $nodes = $this->loadNodeFixture($filename);
        $unit = $this->getSourceUnitMock();

        $extractor = new RefExtractor();
        $result = $extractor->extract($unit, $filename, $nodes);
        $this->assertEquals($result, $expected);
    }

    public function provider()
    {
        return [
            ['101.params.php', [
                [
                    'DefPath' => 'Foo/Bar',
                    'File' => '101.params.php',
                    'Start' => 130,
                    'End' => 138,
                ], [
                    'DefPath' => 'BAZ',
                    'File' => '101.params.php',
                    'Start' => 140,
                    'End' => 150,
                ], [
                    'DefPath' => 'BAZ',
                    'File' => '101.params.php',
                    'Start' => 147,
                    'End' => 150,
                ], [
                    'DefPath' => 'Foo/Qux',
                    'File' => '101.params.php',
                    'Start' => 190,
                    'End' => 202,
                ], [
                    'DefPath' => 'Foo/Bar',
                    'File' => '101.params.php',
                    'Start' => 260,
                    'End' => 273,
                ], [
                    'DefPath' => 'Foo/Qux',
                    'File' => '101.params.php',
                    'Start' => 275,
                    'End' => 283,
                ], [
                    'DefPath' => 'Foo/Qux/QUX',
                    'File' => '101.params.php',
                    'Start' => 338,
                    'End' => 353,
                ], [
                    'DefPath' => 'Foo/Qux/QUX',
                    'File' => '101.params.php',
                    'Start' => 345,
                    'End' => 353,
                ]
            ]],
            ['102.uses.php', [
                [
                    'DefPath' => 'Foo/BAR',
                    'File' => '102.uses.php',
                    'Start' => 108,
                    'End' => 126,
                ], [
                    'DefPath' => 'Foo/qux',
                    'File' => '102.uses.php',
                    'Start' => 131,
                    'End' => 152,
                ], [
                    'DefPath' => 'Foo/Baz',
                    'File' => '102.uses.php',
                    'Start' => 157,
                    'End' => 169,
                ]
            ]],
            ['103.inherance.php', [
                [
                    'DefPath' => 'Foo/Qux',
                    'File' => '103.inherance.php',
                    'Start' => 177,
                    'End' => 185,
                ], [
                    'DefPath' => 'Foo/Bar',
                    'File' => '103.inherance.php',
                    'Start' => 187,
                    'End' => 195,
                ], [
                    'DefPath' => 'Foo/Baz',
                    'File' => '103.inherance.php',
                    'Start' => 157,
                    'End' => 165,
                ], [
                    'DefPath' => 'Foo/Foo',
                    'File' => '103.inherance.php',
                    'Start' => 210,
                    'End' => 218,
                ], [
                    'DefPath' => 'Foo/Fuz',
                    'File' => '103.inherance.php',
                    'Start' => 220,
                    'End' => 228,
                ]
            ]],
            ['104.asigns.php' , [
                [
                    'DefPath' => 'Foo/Baz',
                    'File' => '104.asigns.php',
                    'Start' => 118,
                    'End' => 139,
                ], [
                    'DefPath' => 'Foo/Baz',
                    'File' => '104.asigns.php',
                    'Start' => 125,
                    'End' => 139,
                ], [
                    'DefPath' => 'Baz/Baz/BAR',
                    'File' => '104.asigns.php',
                    'Start' => 145,
                    'End' => 160,
                ], [
                    'DefPath' => 'Baz/Baz/BAR',
                    'File' => '104.asigns.php',
                    'Start' => 152,
                    'End' => 160,
                ], [
                    'DefPath' => 'FOO',
                    'File' => '104.asigns.php',
                    'Start' => 166,
                    'End' => 176,
                ], [
                    'DefPath' => 'FOO',
                    'File' => '104.asigns.php',
                    'Start' => 173,
                    'End' => 176,
                ]
            ]],
        ];
    }
}
