<?php

namespace Lune\Validation\Rules;

class LessThan implements ValidationRule {
    public function __construct(private float $lessThan) {
        $this->lessThan = $lessThan;
    }

    public function message(): string {
        return "Must be a numeric value less than 5";
    }

    public function isValid($field, &$data): bool {
        return isset($data[$field])
            && is_numeric($data[$field])
            && $data[$field] < $this->lessThan;
    }
}
