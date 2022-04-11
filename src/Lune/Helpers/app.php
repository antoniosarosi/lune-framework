<?php

use Lune\App;
use Lune\Config\Config;
use Lune\Container\Container;

/**
 * Easy access to singletons.
 */
function app(string $class = App::class) {
    return Container::resolve($class);
}

/**
 * Create singleton.
 *
 * @param string $class
 * @param callable|string|callable $build
 */
function singleton(string $class, callable|string|null $build = null) {
    return Container::singleton($class, $build);
}

/**
 * Get environment variable value.
 *
 * @param string $key
 * @param string $default Value to return if env variable does not exits.
 */
function env(string $variable, $default = null) {
    return $_ENV[$variable] ?? $default;
}

/**
 * Get configuration value.
 * @param string $configuration Path to final key.
 * @param $default Value to be returned if key does not exist.
 */
function config(string $configuration, $default = null) {
    return Config::get($configuration, $default);
}

/**
 * Dump variables and exit.
 */
function debug(...$args) {
    app()->abort(view("lune/debug", compact('args'), "error"));
}

/**
 * Resources directory containing views, css, and other static files.
 */
function resourcesDirectory() {
    return App::$ROOT . "/resources";
}

/**
 * Get template as string from /resources/templates.
 */
function template(string $name, ?string $directory = null): string {
    $directory ??= resourcesDirectory() . "/templates";

    $file = "$directory/$name.php";

    if (!file_exists($file)) {
        return null;
    }

    return file_get_contents($file);
}
