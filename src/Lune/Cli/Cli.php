<?php

namespace Lune\Cli;

use Dotenv\Dotenv;
use Lune\App;
use Lune\Cli\Commands\MakeController;
use Lune\Cli\Commands\MakeMigration;
use Lune\Cli\Commands\MakeModel;
use Lune\Cli\Commands\Migrate;
use Lune\Cli\Commands\MigrateRollback;
use Lune\Cli\Commands\Serve;
use Lune\Config\Config;
use Lune\Database\DB;
use Lune\Database\Migrations\Migrator;
use Lune\Providers\DatabaseDriverServiceProvider;
use Symfony\Component\Console\Application;

/**
 * Lune development command line interface.
 */
class Cli {
    /**
     * Bootstrap CLI app.
     *
     * @param string $root
     * @return self
     */
    public static function bootstrap(string $root): self {
        App::$ROOT = $root;
        Dotenv::createImmutable(App::$ROOT)->load();
        Config::load("$root/config");

        (new DatabaseDriverServiceProvider())->registerServices();
        DB::connect(config("database"));

        singleton(
            Migrator::class,
            fn () => new Migrator(
                "$root/database/migrations",
                "$root/resources/templates"
            )
        );

        return new self();
    }

    /**
     * Run CLI app.
     *
     * @return void
     */
    public function run() {
        $cli = new Application("Lune");

        $cli->addCommands([
            new MakeController(),
            new MakeMigration(),
            new MakeModel(),
            new Migrate(),
            new MigrateRollback(),
            new Serve(),
        ]);

        $cli->run();
    }
}
