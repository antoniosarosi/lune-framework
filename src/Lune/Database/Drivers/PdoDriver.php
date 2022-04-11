<?php

namespace Lune\Database\Drivers;

use PDO;

/**
 * PHP PDO wrapper.
 */
class PdoDriver implements DatabaseDriver {
    /**
     * PDO Instance
     */
    protected ?PDO $pdo = null;

    /**
     * {@inheritdoc}
     */
    public function connect(
        string $protocol,
        string $host,
        int $port,
        ?string $database,
        string $username,
        string $password
    ): self {
        $dsn = is_null($database)
            ? "$protocol:host=$host;port=$port"
            : "$protocol:host=$host;port=$port;dbname=$database";
        $this->pdo = new PDO($dsn, $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function close() {
        $this->pdo = null;
    }

    /**
     * {@inheritdoc}
     */
    public function statement(string $statement, array $bind = []): mixed {
        if (substr_count($statement, "?") != count($bind)) {
            throw new \BadMethodCallException("The number of '?' marks in string '$statement' must match the number of bind values for ".json_encode($bind));
        }

        $statement = $this->pdo->prepare($statement);
        $statement->execute($bind);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
