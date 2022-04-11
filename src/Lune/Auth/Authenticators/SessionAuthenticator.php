<?php

namespace Lune\Auth\Authenticators;

use Lune\Auth\Authenticatable;

/**
 * Authentication method.
 */
class SessionAuthenticator implements Authenticator {
    /**
     * {@inheritdoc}
     */
    public function resolve(): ?Authenticatable {
        return session()->get("auth");
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated(Authenticatable $authenticatable): bool {
        return session()->get("auth")?->id() == $authenticatable->id();
    }

    /**
     * {@inheritdoc}
     */
    public function login(Authenticatable $authenticatable) {
        session()->set("auth", $authenticatable);
    }

    /**
     * {@inheritdoc}
     */
    public function logout(Authenticatable $authenticatable) {
        session()->destroy();
    }
}
