<?php

namespace Lune\Providers;

use Lune\Database\Drivers\DatabaseDriver;
use Lune\Database\Drivers\PdoDriver;

class DatabaseDriverServiceProvider {
    public function registerServices() {
        match (config("database.driver", "mysql")) {
            "mysql", "pgsql" => singleton(DatabaseDriver::class, PdoDriver::class),
        };
    }
}
