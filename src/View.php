<?php
declare(strict_types=1);
namespace PHPPlusPlus;

class View {
    /**
     * Renders a PHP view file from the views directory.
     */
    public static function render($view, $data = []) {
        $path = "views/{$view}.php";

        if (!file_exists($path)) {
            header("HTTP/1.0 500 Internal Server Error");
            die("PHP++ Error: View [{$view}] not found in /views folder.");
        }

        // Makes array keys available as variables inside the view
        extract($data);

        // Capture output to return it as a string
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
