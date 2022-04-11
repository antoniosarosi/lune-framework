<?php

namespace Lune\Database\Migrations;

/**
 * Database migration.
 */
interface Migration {
    /**
     * Run migration.
     */
    public function up();

    /**
     * Reverse migration.
     */
    public function down();
}
