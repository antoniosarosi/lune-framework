<?php

namespace Lune\Container;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Lune\Database\Model;
use Lune\Http\Exceptions\HttpNotFoundException;

/**
 * Dependency injector for controller methods or normal functions.
 */
class DependencyInjection {
    /**
     * Resolve parameter values.
     *
     * @param array|callable $callback
     * @return array Resolved parameters
     * @throws \RuntimeException if parameters can't be resolved.
     */
    public static function resolveParameters(array|\Closure $callback, $routeParams = []): array {
        $methodOrFunction = is_array($callback)
            ? new ReflectionMethod($callback[0], $callback[1])
            : new ReflectionFunction($callback);

        $params = [];

        foreach ($methodOrFunction->getParameters() as $param) {
            $resolved = null;

            if (is_subclass_of($param->getType()->getName(), Model::class)) {
                $modelClass = (new ReflectionClass($param->getType()->getName()));
                $routeParamName = snake_case($modelClass->getShortName());
                $resolved = $param->getType()->getName()::find($routeParams[$routeParamName] ?? 0);
                if (!$resolved) {
                    throw new HttpNotFoundException();
                }
            } elseif ($param->getType()->isBuiltin()) {
                $resolved = $routeParams[$param->getName()] ?? null;
            } else {
                $resolved = app($param->getType()->getName());
            }

            if (is_null($resolved)) {
                $message = is_array($callback)
                    ? "Failed resolving parameter {$param->getName()} for method {$callback[1]} of class {$callback[0]}"
                    : "Failed resolving parameter {$param->getName()} for function {$methodOrFunction->getName()}";

                throw new \RuntimeException($message);
            }

            $params[] = $resolved;
        }

        return $params;
    }
}
