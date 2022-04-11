<?php

namespace Lune\Providers;

use Lune\Crypto\Bcrypt;
use Lune\Crypto\Hasher;

class HasherServiceProvider {
    public function registerServices() {
        match (config("hashing.hasher", "bcrypt")) {
            "bcrypt" => singleton(Hasher::class, Bcrypt::class),
        };
    }
}
