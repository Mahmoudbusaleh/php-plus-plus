<?php

namespace PHPPlusPlus;

class Compiler {
    private static $entryFile = 'index.php';

    /**
     * Core build function: Handles environment setup and route optimization.
     */
    public static function build() {
        self::initializeEnvironment();

        if (!file_exists(self::$entryFile)) return;

        $content = file_get_contents(self::$entryFile);
        
        // Extracting routes for static mapping
        preg_match_all('/Router::(get|post)\(\'([^\']+)\'/', $content, $matches);

        $compiled = ['GET' => [], 'POST' => []];
        foreach ($matches[1] as $index => $method) {
            $compiled[strtoupper($method)][$matches[2][$index]] = true;
        }

        file_put_contents('cache/routes_compiled.php', "<?php\nreturn " . var_export($compiled, true) . ";");
    }

    /**
     * Auto-creates folders and a sample view if they don't exist.
     */
    private static function initializeEnvironment() {
        // Create cache folder for performance
        if (!is_dir('cache')) mkdir('cache', 0777, true);

        // Create views folder and a starter file for the developer
        if (!is_dir('views')) {
            mkdir('views', 0777, true);
            $welcomeBody = "<h1>Welcome to PHP++</h1>\n<p>Edit this file in <code>views/welcome.php</code></p>";
            file_put_contents('views/welcome.php', $welcomeBody);
        }
    }
}
