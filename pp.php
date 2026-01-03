<?php
/**
 * PHP++ (P++) Entry Point - Global Bootstrap
 * (c) Mahmoud Busaleh
 */

// 1. Loading core files manually (Standard way for the engine)
$coreFiles = [
    'Compiler.php', 'Router.php', 'Request.php', 
    'Response.php', 'View.php', 'Helpers.php'
];

foreach ($coreFiles as $file) {
    $filePath = __DIR__ . '/src/' . $file;
    if (file_exists($filePath)) {
        require_once $filePath;
    }
}

// 2. Initialize the environment (C++ Build & Folders)
\PHPPlusPlus\Compiler::build();

// 3. Set Global Alias for the Engine
if (!class_exists('P')) {
    class_alias('\PHPPlusPlus\Router', 'P');
}
