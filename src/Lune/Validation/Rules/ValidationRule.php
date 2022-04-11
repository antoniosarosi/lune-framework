<?php

namespace Lune\Validation\Rules;

interface ValidationRule {
    /**
     * Check if given data passes validation.
     *
     * @param string $field Field under validation.
     * @param array &$data Reference to data under validation.
     * @return bool
     */
    public function isValid($field, &$data): bool;

    /**
     * Default message to display when validation fails.
     *
     * @return string
     */
    public function message(): string;
}
