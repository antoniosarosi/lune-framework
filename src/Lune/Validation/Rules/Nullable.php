<?php

namespace Lune\Validation\Rules;

class Nullable implements ValidationRule {
    public function message(): string {
        return "This field must be present";
    }

    public function isValid($field, &$data): bool {
        return array_key_exists($field, $data);
    }
}
