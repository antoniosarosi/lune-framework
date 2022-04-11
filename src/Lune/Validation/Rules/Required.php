<?php

namespace Lune\Validation\Rules;

class Required implements ValidationRule {
    public function message(): string {
        return "This field is required";
    }

    public function isValid($field, &$data): bool {
        return isset($data[$field]) && $data[$field] != "";
    }
}
