<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * PHP++ Response Handler
 * Manages outgoing data, headers, and HTTP status codes.
 */
class Response {
    
    /**
     * Send a plain text or HTML response
     */
    public static function send(string $content, int $status = 200): void {
        http_response_code($status);
        echo $content;
    }

    /**
     * Send a JSON response (Perfect for APIs)
     */
    public static function json(array $data, int $status = 200): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
    }

    /**
     * Redirect to a different URL
     */
    public static function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
}
