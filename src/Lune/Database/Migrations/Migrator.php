<?php

namespace Lune\Database\Migrations;

use Lune\Database\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Run and reverse migrations.
 */
class Migrator {
    private ConsoleOutput $output;

    /**
     * Build migrator.
     * @param string $migrationsDirectory
     * @param string $templatesDirectory
     * @param bool $logProgress
     * @return self
     */
    public function __construct(
        private string $migrationsDirectory,
        private string $templatesDirectory,
        private bool $logProgress = true,
    ) {
        $this->migrationsDirectory = $migrationsDirectory;
        $this->templatesDirectory = $templatesDirectory;
        $this->logProgress = $logProgress;
        $this->output = new ConsoleOutput();
    }

    private function log(string $message) {
        if ($this->logProgress) {
            $this->output->write("<info>$message</info>");
        }
    }

    private function createMigrationsTableIfNotExists() {
        DB::statement("CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(256))");
    }

    /**
     * Run migrations.
     */
    public function migrate() {
        $this->createMigrationsTableIfNotExists();
        $migrated = DB::statement("SELECT * FROM migrations");
        $migrations = glob("$this->migrationsDirectory/*.php");

        if (count($migrated) >= count($migrations)) {
            $this->log("Nothing to migrate" . PHP_EOL);
            return;
        }

        foreach (array_slice($migrations, count($migrated)) as $file) {
            $migration = require $file;
            $migration->up();
            $name = basename($file);
            DB::statement("INSERT INTO migrations (name) VALUES (?)", [$name]);
            $this->log("Migrated => " . $name . PHP_EOL);
        }
    }

    /**
     * Reverse migrations.
     * @param int $steps Number of migrations to reverse, all by default.
     */
    public function rollback(?int $steps = null) {
        $this->createMigrationsTableIfNotExists();
        $migrated = DB::statement("SELECT * FROM migrations");

        $pending = count($migrated);

        if ($pending == 0) {
            $this->log("Nothing to rollback");
            return;
        }

        if (is_null($steps) || $steps > $pending) {
            $steps = $pending;
        }

        $migrations = array_slice(array_reverse(glob("$this->migrationsDirectory/*.php")), -$pending);

        foreach ($migrations as $file) {
            $migration = require $file;
            $migration->down();
            $name = basename($file);
            DB::statement("DELETE FROM migrations WHERE name = ?", [$name]);
            $this->log("Rollback => " . substr($name, 18) . PHP_EOL);
            if (--$steps == 0) {
                break;
            }
        }
    }

    /**
     * Create new migration.
     * @param string $migrationName
     * @return string file name of the migration
     */
    public function make(string $migrationName): string {
        $migrationName = snake_case($migrationName);
        $date = date("Y_m_d");
        $id = 0;

        foreach (glob("$this->migrationsDirectory/*.php") as $file) {
            if (str_starts_with(basename($file), $date)) {
                $id++;
            }
        }

        $template = template("migration", $this->templatesDirectory);

        if (preg_match('/create_.*_table/', $migrationName)) {
            $table = preg_replace_callback("/create_(.*)_table/", fn ($match) => $match[1], $migrationName);
            $template = str_replace('$UP', "CREATE TABLE $table (id INT AUTO_INCREMENT PRIMARY KEY)", $template);
            $template = str_replace('$DOWN', "DROP TABLE $table", $template);
        } elseif (preg_match('/.*(from|to)_(.*)_table/', $migrationName)) {
            $table = preg_replace_callback('/.*(from|to)_(.*)_table/', fn ($match) => $match[2], $migrationName);
            $template = preg_replace('/\$UP|\$DOWN/', "ALTER TABLE $table", $template);
        } else {
            $template = preg_replace_callback('/DB::statement.*/', fn ($match) => "// {$match[0]}", $template);
        }

        $migrationName = sprintf("%s_%06d_%s", $date, $id, $migrationName);
        file_put_contents("$this->migrationsDirectory/$migrationName.php", $template);

        $this->log("Migration created => $migrationName.php");

        return "$migrationName.php";
    }
}
