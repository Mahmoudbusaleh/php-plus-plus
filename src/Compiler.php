<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * PHP++ Auto-Compiler & Environment Setup
 * Master class responsible for AOT compilation and structural integrity.
 * @package PHPPlusPlus
 * @author Mahmoud Busaleh
 */
class Compiler {
    /**
     * Master Build: Synchronizes the environment.
     * Triggered automatically by the Router to ensure the engine is ready.
     */
    public static function build(): void {
        // Use absolute root path to avoid issues with nested directories
        $root = dirname(__DIR__);

        // 1. Ensure core directory structure exists
        self::createFolders([
            $root . '/cache', 
            $root . '/views', 
            $root . '/public', 
            $root . '/engine'
        ]);

        // 2. Setup Server Configuration
        self::generateHtaccess($root);

        // 3. Handle C++ Engine (Source & Binary)
        self::handleCppEngine($root);

        // 4. Generate Starter Boilerplate
        self::generateBoilerplate($root);
        
        // 5. Initialize Cache file if missing to prevent Router errors
        if (!file_exists($root . '/cache/routes_compiled.php')) {
            file_put_contents($root . '/cache/routes_compiled.php', "<?php\n return [];");
        }
    }

    /**
     * Creates directories with correct permissions
     */
    private static function createFolders(array $folders): void {
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
        }
    }

    /**
     * Manages the C++ Engine lifecycle (Source code and Shared Object)
     */
    private static function handleCppEngine(string $root): void {
        $cppFile = $root . '/engine/router.cpp';
        $soFile = $root . '/engine/router.so';

        // Optimized C++ Source with strict C linkage for FFI
        $cppCode = '#include <cstring>
extern "C" {
    /**
     * High-speed string matching for PHP++ Engine.
     * Uses C-style strings for maximum compatibility with PHP FFI.
     */
    bool match_route(const char* current_url, const char* target_route) noexcept {
        if (!current_url || !target_route) return false;
        return std::strcmp(current_url, target_route) == 0;
    }
}';

        // Write source only if it doesn't exist to allow manual user edits
        if (!file_exists($cppFile)) {
            file_put_contents($cppFile, $cppCode);
        }

        // Re-compile if .so is missing OR if source was updated
        if (!file_exists($soFile) || filemtime($cppFile) > filemtime($soFile)) {
            // Check for g++ compiler availability
            $hasGpp = (bool) shell_exec('which g++');
            if ($hasGpp) {
                // Compile with PIC (Position Independent Code) and O3 (Maximum Optimization)
                $cmd = "g++ -fPIC -shared -O3 -o " . escapeshellarg($soFile) . " " . escapeshellarg($cppFile);
                shell_exec($cmd);
            }
        }
    }

    /**
     * Generates .htaccess for the Front Controller pattern
     */
    private static function generateHtaccess(string $root): void {
        $path = $root . '/.htaccess'; // Usually at the project root
        $content = "RewriteEngine On\n" .
                   "RewriteCond %{REQUEST_FILENAME} !-f\n" .
                   "RewriteCond %{REQUEST_FILENAME} !-d\n" .
                   "RewriteRule ^(.*)$ index.php [QSA,L]";

        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
    }

    /**
     * Generates initial view files
     */
    private static function generateBoilerplate(string $root): void {
        $viewPath = $root . '/views/welcome.php';
        if (!file_exists($viewPath)) {
            $html = "\n" .
                    "<div style='font-family: system-ui, sans-serif; text-align: center; padding: 50px;'>\n" .
                    "  <h1 style='color: #2c3e50;'>Welcome to PHP++ (P++)</h1>\n" .
                    "  <p style='color: #7f8c8d;'>Status: <strong style='color: #27ae60;'>C++ Core Active</strong></p>\n" .
                    "  <hr style='width: 100px; border: 1px solid #eee;'>\n" .
                    "  <p>Edit this file in <code>views/welcome.php</code></p>\n" .
                    "</div>";
            file_put_contents($viewPath, $html);
        }
    }
}
