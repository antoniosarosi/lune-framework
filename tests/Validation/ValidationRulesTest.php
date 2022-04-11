<?php

namespace Lune\Tests\Validation;

use Lune\Validation\Exceptions\RuleParseException;
use Lune\Validation\Rules\Email;
use Lune\Validation\Rules\LessThan;
use Lune\Validation\Rules\Nullable;
use Lune\Validation\Rules\Number;
use Lune\Validation\Rules\Required;
use Lune\Validation\Rules\RequiredWhen;
use Lune\Validation\Rules\RequiredWith;
use PHPUnit\Framework\TestCase;

class ValidationRulesTest extends TestCase {
    public function emails() {
        return [
            ["test@test.com", true],
            ["antonio@mastermind.ac", true],
            ["test@testcom", false],
            ["test@test.", false],
            ["antonio@", false],
            ["antonio@.", false],
            ["antonio", false],
            ["@", false],
            ["", false],
            [null, false],
            [4, false],
        ];
    }

    /**
     * @dataProvider emails
     */
    public function testEmail($email, $expected) {
        $rule = new Email();
        $data = ["test" => $email];
        $this->assertEquals($expected, $rule->isValid("test", $data));
    }


    public function lessThanData() {
        return [
            [5, 5, false],
            [5, 6, false],
            [5, 3, true],
            [5, null, false],
            [5, "", false],
            [5, "test", false],
        ];
    }

    /**
     * @dataProvider lessThanData
     */
    public function testLessThan($value, $check, $expected) {
        $rule = new LessThan($value);
        $data = ["test" => $check];
        $this->assertEquals($expected, $rule->isValid("test", $data));
    }

    public function testNullable() {
        $rule = new Nullable();
        foreach (["test", "", null] as $check) {
            $data = ["test" => $check];
            $this->assertTrue($rule->isValid("test", $data));
        }
    }

    public function numbers() {
        return [
            [0, true],
            [1, true],
            [1.5, true],
            [-1, true],
            [-1.5, true],
            ["0", true],
            ["1", true],
            ["1.5", true],
            ["-1", true],
            ["-1.5", true],
            ["test", false],
            ["1test", false],
            ["-5test", false],
            ["", false],
            [null, false],
        ];
    }

    /**
     * @dataProvider numbers
     */
    public function testNumber($n, $expected) {
        $rule = new Number();
        $data = ["test" => $n];
        $this->assertEquals($expected, $rule->isValid("test", $data));
    }

    public function requiredData() {
        return [
            ["", false],
            [null, false],
            [5, true],
            ["test", true],
        ];
    }

    /**
     * @dataProvider requiredData
     */
    public function testRequired($value, $expected) {
        $rule = new Required();
        $data = ["test" => $value];
        $this->assertEquals($expected, $rule->isValid("test", $data));
    }

    public function testRequiredWith() {
        $rule = new RequiredWith("other");
        $data = ["test" => 5, "other" => 10];
        $this->assertTrue($rule->isValid("test", $data));
        $data = ["other" => 10];
        $this->assertFalse($rule->isValid("test", $data));
    }

    public function requiredWhenData() {
        return [
            ["other", "=", "value", ["other" => "value"], "test", false],
            ["other", "=", "value", ["other" => "value", "test" => 1], "test", true],
            ["other", "=", "value", ["other" => "not value"], "test", true],
            ["other", ">", 5, ["other" => 1], "test", true],
            ["other", ">", 5, ["other" => 6], "test", false],
            ["other", ">", 5, ["other" => 6, "test" => 1], "test", true],
        ];
    }

    /**
     * @dataProvider requiredWhenData
     */
    public function testRequiredWhen($other, $operator, $compareWith, $data, $field, $expected) {
        $rule = new RequiredWhen($other, $operator, $compareWith);
        $this->assertEquals($expected, $rule->isValid($field, $data));
    }

    public function testRequiredWhenThrowsParseRuleExceptionWhenOperatorIsInvalid() {
        $rule = new RequiredWhen("other", "|||", "test");
        $data = ["other" => 5, "test" => 1];
        $this->expectException(RuleParseException::class);
        $rule->isValid("test", $data);
    }
}
