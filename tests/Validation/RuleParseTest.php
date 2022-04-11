<?php

namespace Lune\Tests\Validation;

use Lune\Validation\Exceptions\RuleParseException;
use Lune\Validation\Exceptions\UnknownValidationRule;
use Lune\Validation\Rule;
use Lune\Validation\Rules\Email;
use Lune\Validation\Rules\LessThan;
use Lune\Validation\Rules\Nullable;
use Lune\Validation\Rules\Number;
use Lune\Validation\Rules\Required;
use Lune\Validation\Rules\RequiredWhen;
use Lune\Validation\Rules\RequiredWith;
use PHPUnit\Framework\TestCase;

class RuleParseTest extends TestCase {
    protected function setUp(): void {
        Rule::loadDefaultRules();
    }

    public function basicRules() {
        return [
            [Email::class, "email"],
            [Nullable::class, "nullable"],
            [Required::class, "required"],
            [Number::class, "number"],
        ];
    }

    /**
     * @dataProvider basicRules
     */
    public function testParseBasicRules($class, $name) {
        $this->assertInstanceOf($class, Rule::from($name));
    }

    public function testParsingUnknownRulesThrowsUnkownRuleException() {
        $this->expectException(UnknownValidationRule::class);
        Rule::from("unknown");
    }

    public function rulesWithParameters() {
        return [
            [new LessThan(5), "less_than:5"],
            [new RequiredWith("other"), "required_with:other"],
            [new RequiredWhen("other", "=", "test"), "required_when:other,=,test"],
        ];
    }

    /**
     * @dataProvider rulesWithParameters
     */
    public function testParseRulesWithParameters($expected, $rule) {
        $this->assertEquals($expected, Rule::from($rule));
    }

    public function rulesWithParametersWithError() {
        return [
            ["less_than"],
            ["less_than:"],
            ["required_with:"],
            ["required_when"],
            ["required_when:"],
            ["required_when:other"],
            ["required_when:other,"],
            ["required_when:other,="],
            ["required_when:other,=,"],
        ];
    }

    /**
     * @dataProvider rulesWithParametersWithError
     */
    public function testParsingRuleWithParametersWithoutPassingCorrectParametersThrowsRuleParseException($rule) {
        $this->expectException(RuleParseException::class);
        Rule::from($rule);
    }
}
