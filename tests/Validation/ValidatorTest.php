<?php

namespace Lune\Tests\Validation;

use Lune\Validation\Exceptions\ValidationException;
use Lune\Validation\Rule;
use Lune\Validation\Rules\Email;
use Lune\Validation\Rules\Number;
use Lune\Validation\Rules\Required;
use Lune\Validation\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase {
    protected function setUp(): void {
        Rule::loadDefaultRules();
    }

    public function testBasicValidatationPassesWithObjects() {
        $data = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
            "foo" => 5,
            "bar" => 4
        ];

        $rules = [
            "email" => new Email(),
            "other" => new Required(),
            "num" => new Number(),
        ];

        $expected = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
        ];

        $v = new Validator($data);

        $this->assertEquals($expected, $v->validate($rules));
    }

    public function testBasicValidatationPassesWithStrings() {
        $data = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
            "foo" => 5,
            "bar" => 4
        ];

        $rules = [
            "email" => "email",
            "other" => "required",
            "num" => "number",
        ];

        $expected = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
        ];

        $v = new Validator($data);

        $this->assertEquals($expected, $v->validate($rules));
    }

    public function testThrowsValidationExceptionOnInvalidData() {
        $this->expectException(ValidationException::class);
        $v = new Validator(["test" => "test"]);
        $v->validate(["test" => "number"]);
    }

    /**
     * @depends testBasicValidatationPassesWithStrings
     */
    public function testMultipleRulesValidation() {
        $data = ["other" => 2, "num" => 3, "foo" => 5];

        $rules = [
            "other" => "nullable",
            "num" => ["required_with:other", "number"],
        ];

        $expected = ["other" => 2, "num" => 3];

        $v = new Validator($data);

        $this->assertEquals($expected, $v->validate($rules));
    }

    /**
     * @depends testThrowsValidationExceptionOnInvalidData
     */
    public function testReturnsMessagesForEachRuleThatDoesntPass() {
        $email = new Email();
        $required = new Required();
        $number = new Number();

        $data = ["email" => "test@", "num1" => "not a number"];

        $rules = [
            "email" => $email,
            "num1" => $number,
            "num2" => [$required, $number],
        ];

        $expected = [
            "email" => ["email" => $email->message()],
            "num1" => ["number" => $number->message()],
            "num2" => [
                "required" => $required->message(),
                "number" => $number->message()
            ],
        ];

        $v = new Validator($data);

        try {
            $v->validate($rules);
            $this->fail("Did not throw Validation Exception");
        } catch (ValidationException $e) {
            $this->assertEquals($expected, $e->errors());
        }
    }

    public function testOverridesErrorMessagesCorrectly() {
        $data = ["email" => "test@", "num1" => "not a number"];

        $rules = [
            "email" => "email",
            "num1" => "number",
            "num2" => ["required", "number"],
        ];

        $messages = [
            "email" => ["email" => "test email message"],
            "num1" => ["number" => "test number message"],
            "num2" => [
                "required" => "test required message",
                "number" => "test number message again"
            ],
        ];

        $v = new Validator($data);

        try {
            $v->validate($rules, $messages);
            $this->fail("Did not throw Validation Exception");
        } catch (ValidationException $e) {
            $this->assertEquals($messages, $e->errors());
        }
    }
}
