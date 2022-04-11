<?php

namespace Lune\Validation;

use Lune\Validation\Exceptions\ValidationException;

/**
 * Validates given data or returns with errors if any.
 */
class Validator {
    /**
     * Data to be validated.
     *
     * @var array
     */
    private array $data;

    /**
     * Create a new validator instances.
     *
     * @param array $data Data to be validated.
     */
    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * Get validated data.
     *
     * @param array $validationRules Rules to be applied.
     * @param array $messages Override default messages for specific rules.
     * @return array
     * @throws ValidationException if validation does not pass.
     */
    public function validate(array $validationRules, array $messages = []) {
        $errors = [];
        $validated = [];
        foreach ($validationRules as $field => $rules) {
            if (!is_array($rules)) {
                $rules = [$rules];
            }
            $fieldUnderValidationErrors = [];
            foreach ($rules as $rule) {
                if (is_string($rule)) {
                    $rule = Rule::from($rule);
                }
                if (!$rule->isValid($field, $this->data)) {
                    $message = $messages[$field][Rule::nameOf($rule)] ?? $rule->message();
                    $fieldUnderValidationErrors[Rule::nameOf($rule)] = $message;
                }
            }
            if (count($fieldUnderValidationErrors) > 0) {
                $errors[$field] = $fieldUnderValidationErrors;
            } else {
                $validated[$field] = $this->data[$field];
            }
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        return $validated;
    }
}
