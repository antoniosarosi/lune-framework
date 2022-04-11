<?php

namespace Lune\Server;

/**
 * Similar to PHP `$_SERVER` but having an interface allows us to mock these
 * global variables, useful for testing.
 */
interface ServerData {
    /**
     * Get a specific key.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * Request query parameters.
     *
     * @return array
     */
    public function queryParams(): array;

    /**
     * Request post data.
     *
     * @return array
     */
    public function postData(): array;

    /**
     * Files sent as `enctype="multipart/form-data"`
     *
     * @return array
     */
    public function files(): array;
}
