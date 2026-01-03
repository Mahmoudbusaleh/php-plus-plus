<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * PHP++ High-Speed Router with C++ Integration
 * Enhanced with Dynamic Methods and Path Sanitization.
 */
class Router {
    private static array $routes = [];

    // دعم كل الـ Methods الأساسية
    public static function get(string $path, $callback): void { self::addRoute('GET', $path, $callback); }
    public static function post(string $path, $callback): void { self::addRoute('POST', $path, $callback); }
    public static function put(string $path, $callback): void { self::addRoute('PUT', $path, $callback); }
    public static function delete(string $path, $callback): void { self::addRoute('DELETE', $path, $callback); }

    private static function addRoute(string $method, string $path, $callback): void {
        // تحويل البرامترات مثل {id} إلى Regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        self::$routes[$method]["#^" . $pattern . "$#D"] = $callback;
    }

    public static function dispatch(): void {
        // 1. تنظيف المسار ومعالجة المجلدات الفرعية
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/') {
            $uri = str_replace($scriptName, '', $uri);
        }
        $uri = ($uri === '') ? '/' : $uri;
        
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // 2. تجهيز الـ C++ Bridge (FFI)
        $cppLib = __DIR__ . '/../engine/router.so';
        $ffi = (extension_loaded('ffi') && file_exists($cppLib)) ? 
               \FFI::cdef("bool match_route(const char* a, const char* b);", $cppLib) : null;

        foreach (self::$routes[$method] ?? [] as $pattern => $callback) {
            $matched = false;
            $params = [];

            // 3. اختبار المسار عبر C++ للمسارات الثابتة (أداء صاروخي)
            if ($ffi && strpos($pattern, '(?P<') === false) {
                $cleanPattern = str_replace(['#^', '$#D'], '', $pattern);
                if ($ffi->match_route($uri, $cleanPattern)) {
                    $matched = true;
                }
            } 
            // 4. اختبار المسار عبر Regex للمسارات المتغيرة
            elseif (preg_match($pattern, $uri, $matches)) {
                $matched = true;
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            }

            if ($matched) {
                // تنفيذ الـ Callback وإرسال النتيجة
                if (is_callable($callback)) {
                    $response = call_user_func_array($callback, $params);
                } else {
                    $response = $callback;
                }
                
                // إذا كانت النتيجة مصفوفة، نحولها لـ JSON تلقائياً
                if (is_array($response) || is_object($response)) {
                    header('Content-Type: application/json');
                    echo json_encode($response);
                } else {
                    echo $response;
                }
                return;
            }
        }
        
        // 5. في حال عدم وجود المسار
        header("HTTP/1.0 404 Not Found");
        if (file_exists(__DIR__ . '/../views/404.php')) {
            require_once __DIR__ . '/../views/404.php';
        } else {
            echo "404 - P++ Engine: Path [{$uri}] not found.";
        }
    }
}
