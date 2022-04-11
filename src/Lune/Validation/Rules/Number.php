<?php

namespace Lune\Validation\Rules;

class Number implements ValidationRule {
    public function message(): string {
        return "Must be a number";
    }

    public function isValid($field, &$data): bool {
        return isset($data[$field]) && is_numeric($data[$field]);
    }
}
