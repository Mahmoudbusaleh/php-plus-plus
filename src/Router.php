<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * PHP++ High-Performance Router
 * * A lightweight, ultra-fast routing engine that supports AOT compilation 
 * and optional C++ integration via FFI for maximum efficiency.
 * * @package PHPPlusPlus
 * @author Mahmoud Busaleh
 */
class Router {
    /** @var array Holds all registered routes organized by HTTP method */
    private static array $routes = [];

    /**
     * Register a GET route
     * * @param string $path
     * @param mixed $callback
     * @return void
     */
    public static function get(string $path, $callback): void {
        self::addRoute('GET', $path, $callback);
    }

    /**
     * Register a POST route
     * * @param string $path
     * @param mixed $callback
     * @return void
     */
    public static function post(string $path, $callback): void {
        self::addRoute('POST', $path, $callback);
    }

    /**
     * Quick URL redirection helper
     * * @param string $from
     * @param string $to
     * @return void
     */
    public static function redirect(string $from, string $to): void {
        self::get($from, function() use ($to) {
            header("Location: $to");
            exit;
        });
    }

    /**
     * Internal method to process and store routes using Regex patterns
     * * @param string $method
     * @param string $path
     * @param mixed $callback
     * @return void
     */
    private static function addRoute(string $method, string $path, $callback): void {
        // Convert dynamic parameters like {id} into named capture groups for Regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = "#^" . $pattern . "$#D";
        
        self::$routes[$method][$pattern] = $callback;
    }

    /**
     * Dispatches the incoming request to the matching route.
     * Includes AOT check and C++ Engine matching optimization.
     * * @return void
     */
    public static function dispatch(): void {
        $cacheFile = 'cache/routes_compiled.php';
        $sourceFile = 'index.php';

        // --- PHASE 1: AOT (Ahead-Of-Time) COMPILATION CHECK ---
        // Automatically re-build the routing cache if index.php has been modified
        if (!file_exists($cacheFile) || (file_exists($sourceFile) && filemtime($sourceFile) > filemtime($cacheFile))) {
            if (class_exists(__NAMESPACE__ . '\\Compiler')) {
                $start = microtime(true);
                // @phpstan-ignore-next-line
                Compiler::build();
                $duration = round((microtime(true) - $start) * 1000, 4);
                header("X-PHP-Plus-Plus-Status: Re-compiled in {$duration}ms");
            }
        }

        // --- PHASE 2: C++ ENGINE INTEGRATION (via FFI) ---
        $ffi = null;
        $cppLibrary = __DIR__ . '/engine/router.so'; 

        if (extension_loaded('ffi') && file_exists($cppLibrary)) {
            try {
                // Initialize the C++ bridge with the match_route function signature
                $ffi = \FFI::cdef(
                    "bool match_route(const char* current_url, const char* target_route);", 
                    $cppLibrary
                );
            } catch (\Exception $e) {
                // Fallback to native PHP routing if FFI fails
                $ffi = null;
            }
        }

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (!isset(self::$routes[$method])) {
            self::sendNotFound();
            return;
        }

        // --- PHASE 3: ROUTE MATCHING ---
        foreach (self::$routes[$method] as $pattern => $callback) {
            $isMatched =
