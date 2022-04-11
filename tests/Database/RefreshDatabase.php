<?php

namespace Lune\Tests\Database;

use Dotenv\Dotenv;
use Lune\Container\Container;
use Lune\Database\DB;
use Lune\Database\Drivers\DatabaseDriver;
use Lune\Database\Drivers\PdoDriver;
use PDOException;

trait RefreshDatabase {
    protected function setUp(): void {
        Container::singleton(DatabaseDriver::class, PdoDriver::class);
        $root = __DIR__ . "/../..";
        if (file_exists("$root/.env.testing")) {
            Dotenv::createImmutable($root, ".env.testing")->load();
        }
        try {
            $config = [
                'connection' => env("DB_CONNECTION", "mysql"),
                'host' => env("DB_HOST", "127.0.0.1"),
                'port' => env("DB_PORT", "3306"),
                'database' => env("DB_DATABASE", "lune_tests"),
                'username' => env("DB_USERNAME", "root"),
                'password' => env("DB_PASSWORD", "root"),
            ];

            DB::connect($config);
        } catch (PDOException $e) {
            $this->markTestSkipped("Cannot connect to test database: {$e->getMessage()}");
        }
    }

    protected function tearDown(): void {
        DB::statement("DROP DATABASE IF EXISTS lune_tests");
        DB::statement("CREATE DATABASE lune_tests");
    }
}
