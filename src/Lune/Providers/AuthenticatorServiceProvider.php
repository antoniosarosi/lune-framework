<?php

namespace Lune\Providers;

use Lune\Auth\Authenticators\Authenticator;
use Lune\Auth\Authenticators\SessionAuthenticator;

class AuthenticatorServiceProvider {
    public function registerServices() {
        match (config("auth.method", "session")) {
            "session" => singleton(Authenticator::class, SessionAuthenticator::class),
        };
    }
}
