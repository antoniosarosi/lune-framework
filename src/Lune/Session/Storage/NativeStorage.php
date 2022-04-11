<?php

namespace Lune\Session\Storage;

/**
 * PHP native session implementation.
 */
class NativeStorage implements SessionStorage {
    /**
     * {@inheritdoc}
     */
    public function start() {
        if (!session_start()) {
            throw new \RuntimeException("Failed starting session");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function id(): string {
        return session_id();
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, mixed $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key) {
        unset($_SESSION[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function save() {
        session_write_close();
    }

    /**
     * {@inheritdoc}
     */
    public function destroy() {
        session_destroy();
    }
}
