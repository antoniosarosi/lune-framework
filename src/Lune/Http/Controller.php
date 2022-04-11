<?php

namespace Lune\Http;

/**
 * HTTP Controller.
 */
class Controller {
    /**
     * View layout.
     */
    public ?string $layout = null;

    /**
     * Middleware behind this controller.
     *
     * @var \Lune\Http\Middleware[]
     */
    public array $middlewares = [];

    /**
     * Register middlewares.
     *
     * @param string[]
     */
    protected function middlewares(array $middlewares) {
        foreach ($middlewares as $class) {
            $this->middlewares[] = new $class();
        }
    }
}
