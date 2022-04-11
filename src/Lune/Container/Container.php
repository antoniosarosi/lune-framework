<?php

namespace Lune\Container;

/**
 * Service container.
 */
class Container {
    /**
     * Unique instances of each registered class.
     *
     * @var array
     */
    private static array $instances = [];

    /**
     * Register a class to be stored as a singleton.
     *
     * @param string $class
     * @param callable|string|null $build
     */
    public static function singleton(string $class, callable|string|null $build = null) {
        if (!array_key_exists($class, self::$instances)) {
            match (true) {
                is_null($build) => self::$instances[$class] = new $class(),
                is_string($build) => self::$instances[$class] = new $build(),
                is_callable($build) => self::$instances[$class] = $build(),
            };
        }

        return self::$instances[$class];
    }

    /**
     * Get the singleton instance of the given class.
     *
     * @param string $class
     */
    public static function resolve(string $class) {
        return self::$instances[$class] ?? null;
    }
}
