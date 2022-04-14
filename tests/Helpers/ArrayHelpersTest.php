<?php

namespace Lune\Tests\Helpers;

use PHPUnit\Framework\TestCase;

class ArrayHelpersTest extends TestCase {
    public function provideIsAssociativeData() {
        return [
            [
                ["first", "second", "third"],
                false
            ],
            [
                [
                    "first" => "I'm the first",
                    "second" => "I'm the second"
                ],
                true
            ],
            [
                [],
                false
            ],
            [
                [10, "key_one" => "A element", true, "key_second" => "Another element"],
                true
            ],
            [
                ["0" => "A element"],
                false
            ],
            [
                [0 => "A element", 1 => "Another element"],
                false
            ],
            [
                [0 => "A element", 1 => "Another element", 4 => "Third element"],
                true
            ],
        ];
    }

    public function provideArrayFirstData() {
        return [
            [
                ["first", "second", "third"],
                "first"
            ],
            [
                [
                    "first" => "I'm the first",
                    "second" => "I'm the second"
                ],
                "I'm the first"
            ],
            [
                [],
                null
            ],
            [
                [10, "key_one" => "A element", true, "key_second" => "Another element"],
                10
            ],
            [
                ["key_one" => "A element", true, "key_second" => "Another element", 10],
                "A element"
            ],
            [
                [[]],
                []
            ],
        ];
    }

    public function provideArrayLastData() {
        return [
            [
                ["first", "second", "third"],
                "third"
            ],
            [
                [
                    "first" => "I'm the first",
                    "second" => "I'm the second"
                ],
                "I'm the second"
            ],
            [
                [],
                null
            ],
            [
                [10, "key_one" => "A element", true, "key_second" => "Another element"],
                "Another element"
            ],
            [
                ["key_one" => "A element", true, "key_second" => "Another element", 10],
                10
            ],
            [
                [[]],
                []
            ],
        ];
    }

    /**
     * @dataProvider provideIsAssociativeData
     */
    public function testIsAssociative($test, $expected) {
        $this->assertEquals($expected, isAssociative($test));
    }

    /**
     * @dataProvider provideArrayFirstData
     */
    public function testFirst($test, $expected) {
        $this->assertEquals($expected, first($test));
    }

    /**
     * @dataProvider provideArrayLastData
     */
    public function testLast($test, $expected) {
        $this->assertEquals($expected, last($test));
    }
}
