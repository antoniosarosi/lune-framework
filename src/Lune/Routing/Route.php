<?php

namespace Lune\Routing;

use Closure;

/**
 * HTTP route.
 */
class Route {
    /**
     * Route URI pattern.
     *
     * @var string
     */
    protected string $uri;

    /**
     * Route action (callback or controller method)
     *
     * @var \Closure|array
     */
    protected \Closure|array $action;

    /**
     * Regular expression to match against.
     *
     * @var string
     */
    protected string $regex;

    /**
     * Route parameters.
     *
     * @var string[]
     */
    protected array $parameters;

    /**
     * Create route instance.
     *
     * @param string $uri
     * @param \Closure|array $callback
     */
    public function __construct(string $uri, \Closure|array $action) {
        $this->uri = $uri;
        $this->action = $action;
        $this->regex = preg_replace(
            "/\{([a-zA-Z]+)\}/",
            "([a-zA-Z0-9]+)",
            str_replace("/", "\/", $uri)
        );
        preg_match_all("/\{([a-zA-Z]+)\}/", $uri, $parameters);
        $this->parameters = $parameters[1];
    }

    /**
     * Get route URI pattern.
     *
     * @return string
     */
    public function uri(): string {
        return $this->uri;
    }

    /**
     * Route action
     *
     * @return \Closure|array
     */
    public function action(): \Closure|array {
        return $this->action;
    }

    /**
     * Returns true if the given path matches this route.
     *
     * @param string $path
     * @return boolean
     */
    public function matches(string $path): bool {
        return preg_match("/^$this->regex$/", $path);
    }

    /**
     * Check if this route has parameters.
     *
     * @return boolean
     */
    public function hasParameters(): bool {
        return count($this->parameters) > 0;
    }

    /**
     * Parse parameters from path.
     *
     * @param string $path
     * @return array<string, mixed>
     */
    public function parseParameters(string $path): array {
        preg_match("/^$this->regex$/", $path, $arguments);

        return array_combine($this->parameters, array_slice($arguments, 1));
    }

    /**
     * Execute route files.
     */
    public static function load(string $routesDir) {
        foreach (glob("$routesDir/*.php") as $routes) {
            require_once $routes;
        }
    }

    /**
     * Register action for HTTP GET method.
     *
     * @param string $path
     * @param Closure|array $callback
     */
    public static function get(string $path, Closure|array $callback) {
        app()->router->get($path, $callback);
    }

    /**
     * Register action for HTTP POST method.
     *
     * @param string $path
     * @param Closure|array $callback
     */
    public static function post(string $path, Closure|array $callback) {
        app()->router->post($path, $callback);
    }
}
