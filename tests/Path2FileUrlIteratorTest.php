<?php

namespace alcamo\filesystem;

use alcamo\exception\AbsolutePathNeeded;
use PHPUnit\Framework\TestCase;

class Path2FileUrlIteratorTest extends TestCase
{
    /**
     * @dataProvider basicsProvider
     */
    public function testBasics($data, $osFamily, $expectedResult)
    {
        $iterator =
            new Path2FileUrlIterator(new \ArrayIterator($data), $osFamily);

        $result = [];

        foreach ($iterator as $value) {
            $result[] = $value;
        }

        $this->assertSame($expectedResult, $result);
    }

    public function basicsProvider()
    {
        return [
            [
                [ '/home/bob jr', '/var/lib/foo?' ],
                'Linux',
                [ 'file:///home/bob%20jr', 'file:///var/lib/foo%3F' ]
            ],
            [
                [ 'c:\\program files', 'd:\\data' ],
                'Windows',
                [ 'file:///c:/program%20files', 'file:///d:/data' ],
            ]
        ];
    }

    public function testExceptionLinux()
    {
        $this->expectException(AbsolutePathNeeded::class);

        $this->expectExceptionMessage(
            'Relative path "qux/baz" given where absolute path is needed'
        );

        foreach (
            new Path2FileUrlIterator(
                new \ArrayIterator([ 'qux/baz' ]),
                'Linux'
            ) as $url
        ) {
        }
    }

    public function testExceptionWindows()
    {
        $this->expectException(AbsolutePathNeeded::class);

        $this->expectExceptionMessage(
            'Relative path "\\users\\alice" given where absolute path is needed'
        );

        foreach (
            new Path2FileUrlIterator(
                new \ArrayIterator([ '\\users\\alice' ]),
                'Windows'
            ) as $url
        ) {
        }
    }
}
