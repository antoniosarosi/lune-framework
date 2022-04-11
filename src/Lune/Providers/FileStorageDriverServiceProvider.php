<?php

namespace Lune\Providers;

use Lune\App;
use Lune\Storage\Drivers\DiskFileStorage;
use Lune\Storage\Drivers\FileStorageDriver;

class FileStorageDriverServiceProvider {
    public function registerServices() {
        match (config("storage.driver", "disk")) {
            "disk" => singleton(
                FileStorageDriver::class,
                fn () => new DiskFileStorage(
                    App::$ROOT . "/storage",
                    "storage",
                    config("app.url")
                )
            ),
        };
    }
}
