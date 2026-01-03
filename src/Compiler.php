<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * PHP++ Auto-Compiler & Environment Setup
 * Automatically manages directories, C++ compilation, and boilerplate generation.
 */
class Compiler {
    /**
     * Master Build: Fully automated environment setup
     * This is triggered by the Router when changes are detected.
     */
    public static function build(): void {
        // 1. Create necessary directory structure
        self::createFolders(['cache', 'views', 'public', 'engine']);

        // 2. Generate server configuration (.htaccess)
        self::generateHtaccess();

        // 3. Handle C++ Engine (Source generation and auto-compilation)
        self::handleCppEngine();

        // 4. Generate initial boilerplate for the developer
        self::generateBoilerplate();
    }

    /**
     * Ensures all required framework folders exist
     */
    private static function createFolders(array $folders): void {
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
        }
    }

    /**
     * Manages the C++ Shared Library life cycle
     */
    private static function handleCppEngine(): void {
        $cppFile = 'engine/router.cpp';
        $soFile = 'engine/router.so';

        // Optimized C++ Code for FFI
        $cppCode = '#include <string.h>
extern "C" {
    /**
     * High-speed string matching for static routes.
     * Returns true (1) if current_url matches target_route exactly.
     */
    bool match_route(const char* current_url, const char* target_route) {
        if (!current_url || !target_route) return false;
        return strcmp(current_url, target_route) == 0;
    }
}';

        // Write or update C++ source if missing
        if (!file_exists($cppFile)) {
            file_put_contents($cppFile, $cppCode);
        }

        // AUTO-COMPILATION LOGIC:
        // Compile if .so is missing OR if .cpp source was recently modified
        if (!file_exists($soFile) || filemtime($cppFile) > filemtime($soFile)) {
            // Check if g++ exists on the host system
            $hasGpp = (bool) shell_exec('which g++');
            if ($hasGpp) {
                // Compile with -fPIC for shared library and -O3 for maximum optimization
                shell_exec("g++ -fPIC -shared -O3 -o $soFile $cppFile");
            }
        }
    }

    /**
     * Generates .htaccess for clean URLs (Front Controller Pattern)
     */
    private static function generateHtaccess(): void {
        $path = 'public/.htaccess';
        $content = "RewriteEngine On\n" .
                   "RewriteCond %{REQUEST_FILENAME} !-f\n" .
                   "RewriteCond %{REQUEST_FILENAME} !-d\n" .
                   "RewriteRule ^(.*)$ index.php [QSA,L]";

        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
    }

    /**
     * Generates starter files to help the developer begin immediately
     */
    private static function generateBoilerplate(): void {
        $viewPath = 'views/welcome.php';
        if (!file_exists($viewPath)) {
            $html = "\n" .
                    "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>\n" .
                    "  <h1>Welcome to PHP++</h1>\n" .
                    "  <p>Status: <span style='color: green;'>Running with Auto-Compiled C++ Core</span></p>\n" .
                    "</div>";
            file_put_contents($viewPath, $html);
        }
    }
}
