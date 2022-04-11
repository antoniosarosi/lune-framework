<?php

namespace Lune\Validation\Exceptions;

use Lune\Exceptions\LuneException;

class ValidationException extends LuneException {
    public function __construct(private array $errors) {
        parent::__construct();
        $this->errors = $errors;
    }

    public function errors() {
        return $this->errors;
    }
}
