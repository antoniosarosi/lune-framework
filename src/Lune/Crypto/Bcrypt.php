<?php

namespace Lune\Crypto;

/**
 * Native Bcrypt hash.
 */
class Bcrypt implements Hasher {
    /**
     * {@inheritdoc}
     */
    public function hash(string $input): string {
        return password_hash($input, PASSWORD_BCRYPT);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(string $input, string $hash): bool {
        return password_verify($input, $hash);
    }
}
