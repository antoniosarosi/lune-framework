<?php

namespace Lune\Crypto;

/**
 * Cryptographic hash.
 */
interface Hasher {
    /**
     * Hash `$input`.
     *
     * @param string $input
     * @return string hashed
     */
    public function hash(string $input): string;

    /**
     * Check if `hash($input) == $hash`.
     *
     * @param string $a
     * @param string $b
     */
    public function verify(string $input, string $hash): bool;
}
