<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * PHP++ Request Handler
 * Manages global inputs with built-in security filtering.
 */
class Request {

    /**
     * Get a sanitized value from $_GET
     */
    public static function query(string $key, $default = null) {
        return self::sanitize($_GET[$key] ?? $default);
    }

    /**
     * Get a sanitized value from $_POST
     */
    public static function post(string $key, $default = null) {
        return self::sanitize($_POST[$key] ?? $default);
    }

    /**
     * Get all input data (JSON or Form-Data)
     */
    public static function all(): array {
        $data = array_merge($_GET, $_POST);
        
        // Handle JSON Input (Common in APIs)
        $json = json_decode(file_get_contents('php://input'), true);
        if (is_array($json)) {
            $data = array_merge($data, $json);
        }

        return array_map([self::class, 'sanitize'], $data);
    }

    /**
     * Core Sanitization Logic to prevent XSS and malicious scripts
     */
    private static function sanitize($value) {
        if (is_array($value)) {
            return array_map([self::class, 'sanitize'], $value);
        }
        
        if (is_string($value)) {
            // Remove HTML tags and encode special characters
            return htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
        }

        return $value;
    }

    /**
     * Get the current Request Method (GET, POST, etc.)
     */
    public static function method(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get the current URI path
     */
    public static function uri(): string {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }
}
