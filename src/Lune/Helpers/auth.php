<?php

use Lune\Auth\Auth;
use Lune\Auth\Authenticatable;

/**
 * Resolve authenticatable instance.
 *
 * @return Authenticatable
 */
function auth(): ?Authenticatable {
    return Auth::user();
}

/**
 * Check if the request was performed by unauthenticated user.
 *
 * @return bool
 */
function isGuest(): bool {
    return Auth::isGuest();
}
