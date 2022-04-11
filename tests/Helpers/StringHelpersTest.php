<?php

namespace Lune\Tests\Helpers;

use PHPUnit\Framework\TestCase;

class StringHelpersTest extends TestCase {
    public function strings() {
        return [
            [
                "camelCaseWord",
                "camel_case_word"
            ],
            [
                "SomeClassName",
                "some_class_name"
            ],
            [
                "String with    spaces",
                "string_with_spaces"
            ],
            [
                "   String with    leading and trailing  spaces   ",
                "string_with_leading_and_trailing_spaces"
            ],
            [
                "string___with---hyphens__and-underscores",
                "string_with_hyphens_and_underscores"
            ],
            [
                "  String   with  spaces ___ and ---snake_case and ___Camel---Case_with_SnakeCase  ",
                "string_with_spaces_and_snake_case_and_camel_case_with_snake_case"
            ]
        ];
    }

    /**
     * @dataProvider strings
     */
    public function testSnakeCase($test, $expected) {
        $this->assertEquals($expected, snake_case($test));
    }
}
