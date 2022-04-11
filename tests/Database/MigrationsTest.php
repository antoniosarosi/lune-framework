<?php

namespace Lune\Tests\Database;

use Lune\Database\DB;
use Lune\Database\Migrations\Migrator;
use PDOException;
use PHPUnit\Framework\TestCase;

/**
 * @requires extension mysqli
 */
class MigrationsTest extends TestCase {
    use RefreshDatabase {
        setUp as protected dbSetUp;
        tearDown as protected dbTearDown;
    }

    protected string $templatesDirectory = __DIR__ . "/templates";
    protected string $migrationsDirectory = __DIR__ . "/migrations";
    protected string $expectedMigrations = __DIR__ . "/expected";
    protected Migrator $migrator;

    protected function setUp(): void {
        if (!file_exists($this->migrationsDirectory)) {
            mkdir($this->migrationsDirectory);
        }

        $this->migrator = new Migrator(
            $this->migrationsDirectory,
            $this->templatesDirectory,
            false
        );

        $this->dbSetUp();
    }

    protected function tearDown(): void {
        shell_exec("rm -r $this->migrationsDirectory");
        $this->dbTearDown();
    }

    public function migrationNames() {
        return [
            [
                "create_products_table",
                "$this->expectedMigrations/products_table.php"
            ],
            [
                "add_category_to_products_table",
                "$this->expectedMigrations/add_category_to_products_table.php"
            ],
            [
                "remove_name_from_products_table",
                "$this->expectedMigrations/remove_name_from_products_table.php"
            ],
        ];
    }

    /**
     * @dataProvider migrationNames
     */
    public function testCreatesMigrationFile($name, $expectedMigrationFile) {
        $expectedName = sprintf("%s_%06d_%s", date("Y_m_d"), 0, $name);
        $actualName = $this->migrator->make($name);

        $this->assertEquals("$expectedName.php", $actualName);
        $this->assertFileExists("$this->migrationsDirectory/$expectedName.php");
        $this->assertFileEquals(
            $expectedMigrationFile,
            "$this->migrationsDirectory/$expectedName.php",
        );
    }

    /**
     * @depends testCreatesMigrationFile
     */
    public function testMigratesFiles() {
        $tables = ["users", "products", "sellers"];
        $migrated = [];

        foreach ($tables as $table) {
            $migrated[] = $this->migrator->make("create_{$table}_table");
        }

        $this->migrator->migrate();

        $rows = DB::statement("SELECT * FROM migrations");

        $this->assertEquals(3, count($rows));
        $this->assertEquals($migrated, array_column($rows, "name"));

        foreach ($tables as $table) {
            try {
                DB::statement("SELECT * FROM $table");
            } catch (PDOException $e) {
                $this->fail("Failed accessing migrated table '$table': {$e->getMessage()}");
            }
        }
    }

    /**
     * @depends testMigratesFiles
     */
    public function testRollsbackFiles() {
        $tables = ["users", "products", "sellers", "providers", "referals"];
        $migrated = [];
        foreach ($tables as $table) {
            $migrated[] = $this->migrator->make("create_{$table}_table");
        }

        $this->migrator->migrate();

        // Rollback last migration
        $this->migrator->rollback(1);
        $rows = DB::statement("SELECT * FROM migrations");
        $this->assertEquals(4, count($rows));
        $this->assertEquals(array_slice($migrated, 0, 4), array_column($rows, "name"));

        try {
            $table = $table[count($tables) - 1];
            DB::statement("SELECT * FROM $table");
            $this->fail("Table re was not deleted after rolling back");
        } catch (PDOException $e) {
            // OK
        }

        // Rollback another 2 migrationss
        $this->migrator->rollback(2);
        $rows = DB::statement("SELECT * FROM migrations");
        $this->assertEquals(2, count($rows));
        $this->assertEquals(array_slice($migrated, 0, 2), array_column($rows, "name"));

        foreach (array_slice($tables, 2, 2) as $table) {
            try {
                DB::statement("SELECT * FROM $table");
                $this->fail("Table '$table' was not deleted after rolling back");
            } catch (PDOException $e) {
                // OK
            }
        }

        // Rollback remaining
        $this->migrator->rollback();
        $rows = DB::statement("SELECT * FROM migrations");
        $this->assertEquals(0, count($rows));
    }
}
