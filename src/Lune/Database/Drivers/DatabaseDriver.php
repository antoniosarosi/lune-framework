<?php

namespace Lune\Database\Drivers;

/**
 * Database driver.
 */
interface DatabaseDriver {
    /**
     * Create a connection to the database.
     *
     * @param string $protocol
     * @param string $host
     * @param int $port
     * @param string $database
     * @param string $username
     * @param string $password
     *
     */
    public function connect(
        string $protocol,
        string $host,
        int $port,
        ?string $database,
        string $username,
        string $password
    ): self;

    /**
     * Close connection.
     */
    public function close();

    /**
     * Execute a statement and return the response.
     *
     * @param string $statement.
     * @param array $bind Values to be replaced in the statement.
     * @return mixed statement result.
     */
    public function statement(string $statement, array $bind = []): mixed;
}
