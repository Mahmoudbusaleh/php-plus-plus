<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * PHP++ Secure Request Handler
 * Filters all global inputs to prevent XSS attacks.
 */
class Request {
    /**
     * Get sanitized value from $_GET
     */
    public static function query(string $key, $default = null) {
        return self::sanitize($_GET[$key] ?? $default);
    }

    /**
     * Get sanitized value from $_POST
     */
    public static function post(string $key, $default = null) {
        return self::sanitize($_POST[$key] ?? $default);
    }

    /**
     * Recursive sanitization using htmlspecialchars
     */
    private static function sanitize($value) {
        if (is_array($value)) {
            return array_map([self::class, 'sanitize'], $value);
        }
        return is_string($value) ? htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8') : $value;
    }
}
