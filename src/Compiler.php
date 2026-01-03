<?php

namespace PHPPlusPlus;

class Compiler {
    /**
     * The Master Build function: Creates folders, configs, and C++ source
     */
    public static function build() {
        // 1. Initialize Folder Structure
        self::createFolders(['cache', 'views', 'public', 'engine']);

        // 2. Generate .htaccess for Clean URLs
        self::generateHtaccess();

        // 3. Generate C++ Engine Source Code
        self::generateCppEngine();

        // 4. Create Initial Index and Welcome View
        self::generateBoilerplate();
    }

    private static function createFolders($folders) {
        foreach ($folders as $folder) {
            if (!is_dir($folder)) mkdir($folder, 0777, true);
        }
    }

    private static function generateHtaccess() {
        $content = "RewriteEngine On\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule ^(.*)$ index.php [QSA,L]";
        file_put_contents('public/.htaccess', $content);
    }

    private static function generateCppEngine() {
        $cppCode = '#include <string.h>
extern "C" {
    bool match_route(const char* current_url, const char* target_route) {
        return strcmp(current_url, target_route) == 0;
    }
}';
        if (!file_exists('engine/router.cpp')) {
            file_put_contents('engine/router.cpp', $cppCode);
        }
    }

    private static function generateBoilerplate() {
        if (!file_exists('views/welcome.php')) {
            file_put_contents('views/welcome.php', "<h1>Welcome to PHP++</h1><p>Running with C++ Turbo Core.</p>");
        }
    }
}
