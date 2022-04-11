<?php

namespace Lune\Session;

use Lune\Session\Storage\NativeStorage;
use Lune\Session\Storage\SessionStorage;
use Lune\Singleton;

/**
 * HTTP Session.
 */
class Session {
    /**
     * Storage controller.
     */
    protected SessionStorage $storage;

    /**
     * Session flash key.
     */
    public const FLASH_KEY = '_flash';

    /**
     * Initialize session.
     */
    public function __construct(SessionStorage $storage) {
        $this->storage = $storage;
        $this->storage->start();

        if (!$this->storage->has(self::FLASH_KEY)) {
            $this->storage->set(self::FLASH_KEY, ['old' => [], 'new' => []]);
        }
    }

    /**
     * Handle flash data before destroying session.
     */
    public function __destruct() {
        foreach ($this->storage->get(self::FLASH_KEY)['old'] as $key) {
            $this->remove($key);
        }
        $this->ageFlashData();
        $this->storage->save();
    }

    /**
     * Prepare session data to be removed for the next request.
     */
    public function ageFlashData() {
        $flash = $this->storage->get(self::FLASH_KEY);
        $flash['old'] = $flash['new'];
        $flash['new'] = [];
        $this->storage->set(self::FLASH_KEY, $flash);
    }

    /**
     * Flash key - value to current session.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function flash(string $key, $value): self {
        $this->storage->set($key, $value);
        $flash = $this->storage->get(self::FLASH_KEY);
        $flash['new'][] = $key;
        $this->storage->set(self::FLASH_KEY, $flash);

        return $this;
    }

    /**
     * Session ID.
     */
    public function id(): string {
        return $this->storage->id();
    }

    /**
     * Check if session has `$key`.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool {
        return $this->storage->has($key);
    }

    /**
     * Get value for `$key` or default.
     *
     * @param string $key
     * @param mixed $default
     */
    public function get(string $key, $default = null): mixed {
        return $this->storage->get($key, $default);
    }

    /**
     * Set key - value.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value) {
        $this->storage->set($key, $value);
    }

    /**
     * Remov key from session.
     *
     * @param string $key
     */
    public function remove(string $key) {
        $this->storage->remove($key);
    }

    /**
     * Destroy session.
     */
    public function destroy() {
        $this->storage->destroy();
    }
}
