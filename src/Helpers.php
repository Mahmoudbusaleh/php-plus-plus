<?php
/**
 * Global functional API for PHP++
 * English comments for global standard.
 */

if (!function_exists('get')) {
    /** Register a GET route */
    function get(string $path, $callback) {
        \PHPPlusPlus\Router::get($path, $callback);
    }
}

if (!function_exists('view')) {
    /** Render a PHP++ template */
    function view(string $name, array $data = []) {
        \PHPPlusPlus\View::render($name, $data);
    }
}

if (!function_exists('dispatch')) {
    /** Run the P++ Engine */
    function dispatch() {
        \PHPPlusPlus\Router::dispatch();
    }
}
