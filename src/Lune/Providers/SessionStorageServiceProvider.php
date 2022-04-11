<?php

namespace Lune\Providers;

use Lune\Session\Storage\NativeStorage;
use Lune\Session\Storage\SessionStorage;

class SessionStorageServiceProvider {
    public function registerServices() {
        match (config("session.storage", "native")) {
            "native" => singleton(SessionStorage::class, NativeStorage::class),
        };
    }
}
