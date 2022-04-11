<?php

use Lune\Session\Session;

/**
 * Get session instance.
 */
function session(): Session {
    return app()->session;
}

/**
 * Get flashed error.
 */
function error(string $key) {
    $errors = session()->get('errors', [])[$key] ?? [];
    $keys = array_keys($errors);
    if (count($keys) > 0) {
        return $errors[$keys[0]];
    }

    return null;
}

/**
 * Old submitted data.
 */
function old(string $key) {
    return session()->get('old', [])[$key] ?? null;
}
