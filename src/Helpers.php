<?php
/**
 * PHP++ Global Helpers
 * English comments for global standards.
 */

if (!function_exists('get')) {
    /** Register a GET route */
    function get(string $path, $callback) {
        \PHPPlusPlus\Router::get($path, $callback);
    }
}

if (!function_exists('view')) {
    /** Render a P++ view template */
    function view(string $name, array $data = []) {
        // Here we call the View class you just verified in src/View.php
        \PHPPlusPlus\View::render($name, $data);
    }
}

if (!function_exists('dispatch')) {
    /** Launch the routing engine */
    function dispatch() {
        \PHPPlusPlus\Router::dispatch();
    }
}
