<?php

namespace Lune\Auth\Authenticators;

use Lune\Auth\Authenticatable;

/**
 * Authentication method.
 */
interface Authenticator {
    /**
     * Get authenticatable from current request.
     *
     * @return ?Authenticatable
     */
    public function resolve(): ?Authenticatable;

    /**
     * Determines if the `$authenticatable` is authenticated.
     *
     * @param Authenticatable $authenticatable
     * @return bool
     */
    public function isAuthenticated(Authenticatable $authenticatable): bool;

    /**
     * Log authenticatable model in.
     *
     * @param Authenticatable $authenticatable
     */
    public function login(Authenticatable $authenticatable);

    /**
     * Log authenticatable model out.
     *
     * @param Authenticatable $authenticatable
     */
    public function logout(Authenticatable $authenticatable);
}
