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

    public function provideArrayFilterData() {
        return [
            [
                ["first", "second", "third", "fourth"],
                fn ($value, $key) => $key % 2 === 0,
                ["first", "third"]
            ],
            [
                ["first", "second", "third", "fourth"],
                fn ($value) => $value === "fifth",
                []
            ],
            [
                [0, "1", 2, "3", 4],
                fn ($value) => is_string($value),
                ["1", "3"]
            ],
            [
                ["first" => 100, "second" => 200, "third" => 300, "fourth" => 400],
                fn ($value) => ($value / 100) % 2 === 0,
                ["second" => 200, "fourth" => 400]
            ],
            [
                [],
                fn () => true,
                []
            ],
            [
                ["first", "second", "third", "fourth"],
                fn () => false,
                []
            ],
            [
                ["first", "second", "third", "fourth"],
                fn () => true,
                ["first", "second", "third", "fourth"]
            ],
            [
                ["first", "second" => 2, "third", "fourth" => 4, true, [6, 7, 8]],
                fn ($value, $key) => is_string($key) && is_integer($value),
                ["second" => 2, "fourth" => 4]
            ],
            [
                [null],
                fn ($value) => $value !== null,
                []
            ],
            [
                [[1, 2, 3], 10, [4, 5, 6], "a string", [7, 8, 9]],
                fn ($value) => is_array($value),
                [[1, 2, 3], [4, 5, 6], [7, 8, 9]]
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

    /**
     * @dataProvider provideArrayFilterData
     */
    public function testFilter($test, $callback, $expected) {
        $this->assertEquals($expected, filter($test, $callback));
    }
}
