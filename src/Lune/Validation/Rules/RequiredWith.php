<?php

namespace Lune\Validation\Rules;

class RequiredWith implements ValidationRule {
    /**
     * Field that must be present when field under validation is present.
     */
    private string $withField;

    /**
     * Instantiate required with rule.
     *
     * @param string $withField field to check when validating actual field.
     */
    public function __construct(string $withField) {
        $this->withField = $withField;
    }

    public function message(): string {
        return "This field is required when $this->withField is present";
    }

    public function isValid($field, &$data): bool {
        if (isset($data[$this->withField]) && $data[$this->withField] != "") {
            return isset($data[$field]) && $data[$field] != "";
        }

        return true;
    }
}
