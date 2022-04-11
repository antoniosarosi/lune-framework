<?php

namespace Lune\Routing;

use Closure;
use Lune\Container\DependencyInjection;
use Lune\Http\Exceptions\HttpNotFoundException;
use Lune\Http\HttpMethod;
use Lune\Http\Request;
use Lune\Http\Response;

/**
 * HTTP Routing.
 */
class Router {
    /**
     * Registered routes.
     *
     * @var array<string, Route[]>
     */
    protected array $routes;

    /**
     * Create new Router instance.
     */
    public function __construct() {
        $this->routes = [];
        foreach (HttpMethod::cases() as $method) {
            $this->routes[$method->value] = [];
        }
    }

    /**
     * Register action for HTTP GET method.
     *
     * @param string $path
     * @param Closure|array $action
     */
    public function get(string $path, Closure|array $action) {
        $this->routes[HttpMethod::GET->value][] = new Route($path, $action);
    }

    /**
     * Register action for HTTP POST method.
     *
     * @param string $path
     * @param Closure|array $action
     */
    public function post(string $path, Closure|array $action) {
        $this->routes[HttpMethod::POST->value][] = new Route($path, $action);
    }

    /**
     * Execute registered action for request route.
     *
     * @return Response
     */
    public function resolve(Request $request): Response {
        $route = $this->resolveRoute($request) ?? throw new HttpNotFoundException();
        $action = $route->action();

        $params = DependencyInjection::resolveParameters($action, $route->parseParameters($request->path()));

        if (!is_array($action)) {
            return call_user_func($action, ...$params);
        }

        $controller = new $action[0]();
        app()->controller = $controller;
        $action[0] = $controller;

        return $this->runMiddlewares(
            $controller->middlewares,
            fn () => call_user_func($action, ...$params)
        );
    }

    /**
     * Resolves the requested route.
     *
     * @param Request $request
     * @return ?Route
     */
    public function resolveRoute(Request $request): ?Route {
        foreach ($this->routes[$request->method()->value] as $route) {
            if ($route->matches($request->path())) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Run middleware stack and return final response.
     *
     * @param \Lune\Http\Middleware[] $middlewares
     * @param callable $target
     * @return Response
     */
    private function runMiddlewares(array $middlewares, callable $target): Response {
        if (count($middlewares) == 0) {
            return $target();
        }

        return $middlewares[0]->handle(
            request(),
            fn () => $this->runMiddlewares(
                array_slice($middlewares, 1),
                $target
            )
        );
    }
}
