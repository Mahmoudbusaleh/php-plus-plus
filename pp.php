<?php
/**
 * PHP++ Engine (P++) - Entry Point
 * Fast, Compiled, and Minimalist.
 */

// 1. Efficient Loading (Check for Composer first, fallback to manual)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    // Manual loading if vendor doesn't exist
    $files = [
        'Compiler.php', 'Router.php', 'Request.php', 
        'Response.php', 'View.php', 'Helpers.php'
    ];
    foreach ($files as $file) {
        $path = __DIR__ . "/src/$file";
        if (file_exists($path)) require_once $path;
    }
}

// 2. Launch the Automatic C++ Compiler & Environment Setup
// This ensures folders and .so files are ready before any route is hit
\PHPPlusPlus\Compiler::build();

// 3. Global Aliases (Identity: P)
if (!class_exists('P')) {
    class_alias('\PHPPlusPlus\Router', 'P');
}

/**
 * Super Shortcuts for PHP++ (The Functional API)
 */
if (!function_exists('get')) {
    function get(string $path, $callback) { 
        \PHPPlusPlus\Router::get($path, $callback); 
    }
}

if (!function_exists('post')) {
    function post(string $path, $callback) { 
        \PHPPlusPlus\Router::post($path, $callback); 
    }
}

if (!function_exists('dispatch')) {
    function dispatch() { 
        \PHPPlusPlus\Router::dispatch(); 
    }
}

// Added view helper to complete the set
if (!function_exists('view')) {
    function view(string $name, array $data = []) {
        return \PHPPlusPlus\View::render($name, $data);
    }
}
