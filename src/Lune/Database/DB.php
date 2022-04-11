<?php

namespace Lune\Database;

use Lune\Database\Drivers\DatabaseDriver;
use Lune\Database\Drivers\PdoDriver;

/**
 * Database connection.
 */
class DB {
    /**
     * Initialize database connection.
     */
    public static function connect(array $config) {
        [
            'connection' => $protocol,
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ] = $config;

        app(DatabaseDriver::class)->connect(
            $protocol,
            $host,
            $port,
            $database,
            $username,
            $password
        );
    }

    /**
     * Run statement and get response.
     * @param string $statement
     * @param array $bind Statement parameters.
     * @return mixed Database response.
     */
    public static function statement(string $statement, array $bind = []) {
        return app(DatabaseDriver::class)->statement($statement, $bind);
    }

    /**
     * Close database connection.
     */
    public static function close() {
        app(DatabaseDriver::class)->close();
    }
}
