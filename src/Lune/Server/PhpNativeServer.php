<?php

namespace Lune\Server;

/**
 * Similar to PHP `$_SERVER` but having an interface allows us to mock these
 * global variables, useful for testing.
 */
class PhpNativeServer implements ServerData {
    /**
     * {@inheritdoc}
     */
    public function get(string $key): mixed {
        return $_SERVER[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function queryParams(): array {
        return $_GET;
    }

    /**
     * {@inheritdoc}
     */
    public function postData(): array {
        return $_POST;
    }

    /**s
     * {@inheritdoc}
     */
    public function files(): array {
        return $_FILES;
    }
}
