<?php

namespace Lune\Auth;

use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\RegisterController;
use Lune\Auth\Authenticatable;
use Lune\Auth\Authenticators\Authenticator;
use Lune\Routing\Route;

/**
 * Authentication facade.
 */
class Auth {
    /**
     * Authentication routes.
     */
    public static function routes() {
        Route::get('/login', [LoginController::class, 'show']);
        Route::post('/login', [LoginController::class, 'create']);
        Route::get('/logout', [LoginController::class, 'destroy']);
        Route::get('/register', [RegisterController::class, 'show']);
        Route::post('/register', [RegisterController::class, 'create']);
    }

    /**
     * Current logged in user.
     */
    public static function user(): ?Authenticatable {
        return app(Authenticator::class)->resolve();
    }

    /**
     * Check if current request is performed by guest.
     *
     * @return bool
     */
    public static function isGuest(): bool {
        return is_null(self::user());
    }
}
