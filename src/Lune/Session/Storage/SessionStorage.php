<?php

namespace Lune\Session\Storage;

/**
 * Session storage controller interface.
 */
interface SessionStorage {
    /**
     * Load session data.
     */
    public function start();

    /**
     * Get the ID of the current session.
     *
     * @return string
     */
    public function id(): string;

    /**
     * Check if a specific key exists in the sesion.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Get a specific key from session storage.
     *
     * @param string $key
     * @param string $default Value to be returned if key is not present.
     * @return mixed
     */
    public function get(string $key, $default = null): mixed;

    /**
     * Set new key - value pair.
     *
     * @param string $key
     * @param mixed value
     */
    public function set(string $key, mixed $value);

    /**
     * Remove specific key exists in the sesion.
     *
     * @param string $key
     */
    public function remove(string $key);

    /**
     * Write the session data to make it persistent.
     */
    public function save();

    /**
     * Destroy session.
     */
    public function destroy();
}
