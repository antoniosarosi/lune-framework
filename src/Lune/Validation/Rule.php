<?php

namespace Lune\Validation;

use Lune\Validation\Exceptions\UnknownValidationRule;
use Lune\Validation\Rules\Email;
use Lune\Validation\Exceptions\RuleParseException;
use Lune\Validation\Rules\LessThan;
use Lune\Validation\Rules\Nullable;
use Lune\Validation\Rules\Number;
use Lune\Validation\Rules\Required;
use Lune\Validation\Rules\RequiredWhen;
use Lune\Validation\Rules\RequiredWith;
use Lune\Validation\Rules\ValidationRule;
use ReflectionClass;

/**
 * Container for all the validation rules defined in the application.
 */
class Rule {
    /**
     * Rule container. Maps rule name to rule class.
     */
    private static array $rules = [];

    /**
     * Lune validation rules.
     */
    private static array $defaultRules = [
        Required::class,
        RequiredWith::class,
        RequiredWhen::class,
        Nullable::class,
        Email::class,
        Number::class,
        LessThan::class,
    ];

    /**
     * Default Lune validation rules.
     */
    public static function loadDefaultRules() {
        self::load(self::$defaultRules);
    }

    /**
     * Initialize rules.
     */
    public static function load($rules) {
        foreach ($rules as $class) {
            $className = array_slice(explode("\\", $class), -1)[0];
            $ruleName = snake_case($className);
            self::$rules[$ruleName] = $class;
        }
    }

    /**
     * Resolve name of the rule.
     * @param ValidationRule
     * @return string
     */
    public static function nameOf(ValidationRule $rule) {
        $class = new ReflectionClass($rule);

        return snake_case($class->getShortName());
    }

    /**
     * Parse rule with no parameters into `\Lune\Validation\Rules\ValidationRule`
     * instance.
     *
     * @param string $ruleName
     * @return ValidationRule
     * @throws RuleParseException
     */
    private static function parseBasicRule(string $ruleName): ValidationRule {
        $class = new ReflectionClass(self::$rules[$ruleName]);

        if (count($class->getConstructor()?->getParameters() ?? []) > 0) {
            throw new RuleParseException("Rule $ruleName requires parameters, but none have been passed");
        }

        return new self::$rules[$ruleName]();
    }

    /**
     * Get `\Lune\Validation\Rules\ValidationRule` associated to `$ruleName`
     * and instantiate it with given `$params`.
     *
     * @param string $ruleName
     * @param string $params
     * @return ValidationRule
     * @throws RuleParseException
     */
    private static function parseRuleWithParameters(string $ruleName, string $params): ValidationRule {
        $class = new ReflectionClass(self::$rules[$ruleName]);
        $constructorParams = $class->getConstructor()?->getParameters() ?? [];
        $givenParams = array_filter(explode(",", $params), fn ($p) => !empty($p));

        if (count($givenParams) !== count($constructorParams)) {
            throw new RuleParseException(sprintf(
                "Rule %s requires %d parameters, but %d where given: %s",
                $ruleName,
                count($constructorParams),
                count($givenParams),
                $params
            ));
        }

        return $class->newInstance(...$givenParams);
    }

    /**
     * Convert string into `\Lune\Validation\Rules\ValidationRule`
     * instance.
     *
     * @return ValidationRule
     * @throws UnknownValidationRule
     */
    private static function parseRule(string $string): ValidationRule {
        if (strlen($string) == 0) {
            throw new RuleParseException("Can't parse empty string to rule");
        }

        $ruleParts = explode(":", $string);

        if (!array_key_exists($ruleParts[0], self::$rules)) {
            throw new UnknownValidationRule($ruleParts[0]);
        }

        if (count($ruleParts) == 1) {
            return self::parseBasicRule($ruleParts[0]);
        }

        [$ruleName, $params] = $ruleParts;

        return self::parseRuleWithParameters($ruleName, $params);
    }

    /**
     * Create a new rule object from string format (example: "requiredWith:name").
     *
     * @return ValidtionRule
     */
    public static function from(string $string): ValidationRule {
        return self::parseRule($string);
    }

    /**
     * Email validaton rule.
     *
     * @return ValidationRule
     */
    public static function email(): ValidationRule {
        return new Email();
    }

    /**
     * Build a new `Lune\Validation\Rules\RequiredWith` instance.
     *
     * @return ValidationRule
     */
    public static function requiredWith(string $otherField): ValidationRule {
        return new RequiredWith($otherField);
    }
}
