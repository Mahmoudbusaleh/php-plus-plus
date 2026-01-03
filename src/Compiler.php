<?php
declare(strict_types=1);
namespace PHPPlusPlus;

class Compiler {
    /**
     * The Master Build function: Fully automated environment setup
     */
    public static function build() {
        // 1. Create necessary folders
        self::createFolders(['cache', 'views', 'public', 'engine']);

        // 2. Generate configuration files
        self::generateHtaccess();

        // 3. Handle C++ Engine (Source + Automatic Compilation)
        self::handleCppEngine();

        // 4. Create starter files
        self::generateBoilerplate();
    }

    private static function createFolders($folders) {
        foreach ($folders as $folder) {
            if (!is_dir($folder)) mkdir($folder, 0777, true);
        }
    }

    private static function handleCppEngine() {
        $cppFile = 'engine/router.cpp';
        $soFile = 'engine/router.so';

        $cppCode = '#include <string.h>
extern "C" {
    bool match_route(const char* current_url, const char* target_route) {
        return strcmp(current_url, target_route) == 0;
    }
}';

        // Write C++ source if it doesn't exist
        if (!file_exists($cppFile)) {
            file_put_contents($cppFile, $cppCode);
        }

        // FEATURE: Automatic C++ Compilation
        // If the compiled library doesn't exist, try to compile it using the system's g++
        if (!file_exists($soFile)) {
            // Check if g++ is installed on the server
            $checkGpp = shell_exec('which g++');
            if ($checkGpp) {
                shell_exec("g++ -fPIC -shared -o $soFile $cppFile");
            }
        }
    }

    private static function generateHtaccess() {
        $content = "RewriteEngine On\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule ^(.*)$ index.php [QSA,L]";
        if (!file_exists('public/.htaccess')) {
            file_put_contents('public/.htaccess', $content);
        }
    }

    private static function generateBoilerplate() {
        if (!file_exists('views/welcome.php')) {
            file_put_contents('views/welcome.php', "<h1>Welcome to PHP++</h1><p>Running with Auto-Compiled C++ Core.</p>");
        }
    }
}
