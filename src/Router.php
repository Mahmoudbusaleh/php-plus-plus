<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * PHP++ High-Performance Router
 * * An ultra-fast routing engine featuring:
 * 1. AOT (Ahead-of-Time) compilation for routing tables.
 * 2. High-speed string matching using a C++ Shared Library via PHP FFI.
 * 3. Dynamic Regex pattern matching for parameterized routes.
 * * @package PHPPlusPlus
 * @author Mahmoud Busaleh
 */
class Router {
    /** * @var array $routes Stores all registered routes.
     * Structured as: self::$routes['METHOD']['PATTERN'] = $callback;
     */
    private static array $routes = [];

    /**
     * Registers a GET route.
     * * @param string $path The URL path (e.g., '/user/{id}')
     * @param mixed $callback Closure or controller string
     * @return void
     */
    public static function get(string $path, $callback): void {
        self::addRoute('GET', $path, $callback);
    }

    /**
     * Registers a POST route.
     * * @param string $path
     * @param mixed $callback
     * @return void
     */
    public static function post(string $path, $callback): void {
        self::addRoute('POST', $path, $callback);
    }

    /**
     * Helper to perform a quick HTTP redirect.
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
     * Processes the route path and stores it.
     * Converts human-friendly placeholders {param} into Regex named groups.
     * * @param string $method GET|POST|...
     * @param string $path
     * @param mixed $callback
     * @return void
     */
    private static function addRoute(string $method, string $path, $callback): void {
        // Regex conversion: {id} becomes (?P<id>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        
        // Wrap the pattern with delimiters and anchors for exact matching
        $pattern = "#^" . $pattern . "$#D";
        
        self::$routes[$method][$pattern] = $callback;
    }

    /**
     * The Heart of the Engine.
     * Matches the incoming request and executes the corresponding callback.
     * Optimizes performance using C++ if the route is static.
     * * @return void
     */
    public static function dispatch(): void {
        $cacheFile = __DIR__ . '/../cache/routes_compiled.php';
        $sourceFile = __DIR__ . '/../index.php';

        // --- PHASE 1: AOT COMPILATION CHECK ---
        // If the source file (index.php) is newer than the cache, rebuild the environment.
        if (!file_exists($cacheFile) || (file_exists($sourceFile) && filemtime($sourceFile) > filemtime($cacheFile))) {
            if (class_exists(__NAMESPACE__ . '\\Compiler')) {
                $start = microtime(true);
                Compiler::build(); // Re-generates directories and C++ binaries
                $duration = round((microtime(true) - $start) * 1000, 4);
                header("X-PHP-Plus-Plus-Status: Re-compiled in {$duration}ms");
            }
        }

        // --- PHASE 2: C++ ENGINE INTEGRATION (via PHP FFI) ---
        $ffi = null;
        $cppLibrary = __DIR__ . '/../engine/router.so'; 

        // Only attempt FFI if the extension is loaded and the binary exists
        if (extension_loaded('ffi') && file_exists($cppLibrary)) {
            try {
                $ffi = \FFI::cdef(
                    "bool match_route(const char* current_url, const char* target_route);", 
                    $cppLibrary
                );
            } catch (\Exception $e) {
                $ffi = null; // Silently fallback to native PHP on failure
            }
        }

        // Clean the URI and fetch the request method
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Check if any routes exist for the current HTTP method
        if (!isset(self::$routes[$method])) {
            self::sendNotFound();
            return;
        }

        // --- PHASE 3: ROUTE MATCHING LOOP ---
        foreach (self::$routes[$method] as $pattern => $callback) {
            $isMatched = false;
            $params = []; // Holds dynamic URL segments (e.g., 'id' => 5)

            // OPTIMIZATION: Use C++ for static strings (Routes without {parameters})
            if ($ffi !== null && strpos($pattern, '(?P<') === false) {
                $cleanPattern = str_replace(['#^', '$#D'], '', $pattern);
                // Call the C++ Shared Library function for O(1) matching
                if ($ffi->match_route($uri, $cleanPattern)) {
                    $isMatched = true;
                }
            } else {
                // Standard Regex matching for parameterized/dynamic routes
                if (preg_match($pattern, $uri, $matches)) {
                    $isMatched = true;
                    // Extract only the named keys from the regex matches
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                }
            }

            // --- PHASE 4: EXECUTION ---
            if ($isMatched) {
                if (is_callable($callback)) {
                    // Call the function and echo the returned content
                    echo call_user_func_array($callback, $params);
                    return;
                }
                
                // If callback is just a string/HTML, echo it directly
                if (is_string($callback)) {
                    echo $callback;
                    return;
                }
            }
        }

        // If no matches were found after the loop
        self::sendNotFound();
    }

    /**
     * Terminate the request with a 404 response.
     * * @return void
     */
    private static function sendNotFound(): void {
        if (!headers_sent()) {
            header("HTTP/1.1 404 Not Found");
        }
        echo "404 Not Found - PHP++ High Performance Engine";
    }
}
