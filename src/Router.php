<?php

namespace PHPPlusPlus;

/**
 * PHP++ High-Performance Router
 * Integrated with C++ Engine via FFI (Foreign Function Interface)
 */
class Router {
    // Array to hold the routes registered by the developer
    private static $routes = [];

    /**
     * Registers a GET HTTP route
     */
    public static function get($path, $callback) {
        self::addRoute('GET', $path, $callback);
    }

    /**
     * Registers a POST HTTP route
     */
    public static function post($path, $callback) {
        self::addRoute('POST', $path, $callback);
    }

    /**
     * Helper for quick URL redirection
     */
    public static function redirect($from, $to) {
        self::get($from, function() use ($to) {
            header("Location: $to");
            exit;
        });
    }

    /**
     * Internal method to process and store routes
     */
    private static function addRoute($method, $path, $callback) {
        // Regex conversion for dynamic parameters like {id}
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = "#^" . $pattern . "$#D";
        
        self::$routes[$method][$pattern] = $callback;
    }

    /**
     * The heart of PHP++: Matches the request using C++ Engine if available
     */
    public static function dispatch() {
        $cacheFile = 'cache/routes_compiled.php';
        $sourceFile = 'index.php';

        // --- PHASE 1: AOT COMPILATION CHECK ---
        // Automatically re-builds the project if changes are detected in index.php
        if (!file_exists($cacheFile) || (file_exists($sourceFile) && filemtime($sourceFile) > filemtime($cacheFile))) {
            if (class_exists(__NAMESPACE__ . '\\Compiler')) {
                $start = microtime(true);
                Compiler::build();
                $end = microtime(true);
                
                $duration = round(($end - $start) * 1000, 4);
                header("X-PHP-Plus-Plus-Status: Re-compiled in {$duration}ms");
            }
        }

        // --- PHASE 2: C++ ENGINE INTEGRATION (EXPERIMENTAL) ---
        // Attempt to use the C++ Shared Library for lightning-fast matching
        $ffi = null;
        $cppLibrary = 'engine/router.so'; // Path to the compiled C++ binary

        if (file_exists($cppLibrary) && extension_loaded('ffi')) {
            try {
                // Bridge between PHP and C++: Define the C function signature
                $ffi = \FFI::cdef(
                    "bool match_route(const char* current_url, const char* target_route);", 
                    $cppLibrary
                );
            } catch (\Exception $e) {
                // Fallback to native PHP if FFI fails
                $ffi = null;
            }
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset(self::$routes[$method])) {
            self::sendNotFound();
            return;
        }

        // --- PHASE 3: ROUTE MATCHING ---
        foreach (self::$routes[$method] as $pattern => $callback) {
            $isMatched = false;

            // If C++ Engine is loaded, use it for O(1) string comparison
            if ($ffi !== null && strpos($pattern, '(?P<') === false) {
                // Strip regex characters for pure string matching in C++
                $cleanPattern = trim($pattern, '#^$D');
                if ($ffi->match_route($uri, $cleanPattern)) {
                    $isMatched = true;
                }
            } else {
                // Fallback to standard Regex for dynamic routes {id}
                if (preg_match($pattern, $uri, $matches)) {
                    $isMatched = true;
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                }
            }

            if ($isMatched) {
                if (is_callable($callback)) {
                    echo call_user_func_array($callback, $params ?? []);
                    return;
                }
            }
        }

        self::sendNotFound();
    }

    /**
     * Renders the final 404 Not Found response
     */
    private static function sendNotFound() {
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found - PHP++ C++ Powered Engine";
    }
}
