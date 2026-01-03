<?php
/**
 * PHP++ (P++) Entry Point - Global Bootstrap
 */

// 1. تحميل الملفات الأساسية
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

// 2. تشغيل المترجم هنا (مرة واحدة فقط عند بداية التحميل)
// هذا السطر يتأكد من وجود المجلدات وملف الـ .so والـ .htaccess
\PHPPlusPlus\Compiler::build();

// 3. تعريف الاختصارات
if (!class_exists('P')) {
    class_alias('\PHPPlusPlus\Router', 'P');
}
