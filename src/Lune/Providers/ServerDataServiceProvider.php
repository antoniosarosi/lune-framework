<?php

namespace Lune\Providers;

use Lune\Server\PhpNativeServer;
use Lune\Server\ServerData;

class ServerDataServiceProvider {
    public function registerServices() {
        singleton(ServerData::class, PhpNativeServer::class);
    }
}
