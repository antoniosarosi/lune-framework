<?php

namespace Lune\Storage\Drivers;

/**
 * File storage driver.
 */
interface FileStorageDriver {
    /**
     * Store file.
     *
     * @param string $path
     * @param mixed $content
     * @return string The URL of the stored file.
     */
    public function put(string $path, mixed $content): string;
}
