<?php
/**
 * PHP++ (P++) Entry Point - Global Bootstrap
 * Recommended version for Production
 */

// 1. Core classes to be loaded
$coreFiles = [
    'Compiler.php', 
    'Router.php', 
    'Request.php',  
    'Response.php', 
    'View.php', 
    'Helpers.php'
];

// 2. Load files safely using absolute path
foreach ($coreFiles as $file) {
    $filePath = __DIR__ . '/src/' . $file;
    if (file_exists($filePath)) {
        require_once $filePath;
    } else {
        // إذا ملف أساسي نقص، لازم نعرف عشان ما نضيع
        error_log("P++ Engine Warning: Core file [{$file}] is missing.");
    }
}

// 3. Trigger the Compiler (C++ Build, .htaccess, Folders)
// This ensures the environment is ready before any route is processed
\PHPPlusPlus\Compiler::build();

// 4. Global Aliases for clean developer experience
if (!class_exists('P')) {
    class_alias('\PHPPlusPlus\Router', 'P');
}

// 5. Optional: Initialize Request globally
// \PHPPlusPlus\Request::init();
