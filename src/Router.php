<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * PHP++ High-Speed Router with C++ Integration
 */
class Router {
    private static array $routes = [];

    public static function get(string $path, $callback): void {
        self::addRoute('GET', $path, $callback);
    }

    private static function addRoute(string $method, string $path, $callback): void {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        self::$routes[$method]["#^" . $pattern . "$#D"] = $callback;
    }

    public static function dispatch(): void {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // Full path to the compiled C++ library
        $cppLib = __DIR__ . '/../engine/router.so';
        $ffi = (extension_loaded('ffi') && file_exists($cppLib)) ? 
               \FFI::cdef("bool match_route(const char* a, const char* b);", $cppLib) : null;

        foreach (self::$routes[$method] ?? [] as $pattern => $callback) {
            $matched = false;
            $params = [];

            // Performance: Check static routes via C++ first
            if ($ffi && strpos($pattern, '(?P<') === false) {
                $clean = str_replace(['#^', '$#D'], '', $pattern);
                if ($ffi->match_route($uri, $clean)) $matched = true;
            } elseif (preg_match($pattern, $uri, $matches)) {
                $matched = true;
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            }

            if ($matched) {
                echo is_callable($callback) ? call_user_func_array($callback, $params) : $callback;
                return;
            }
        }
        
        header("HTTP/1.0 404 Not Found");
        echo "404 - P++ Engine: Page Not Found";
    }
}
