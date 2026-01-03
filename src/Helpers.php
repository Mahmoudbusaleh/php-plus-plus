<?php
/**
 * ملف الدوال المساعدة العالمية (Global Helpers) - مشروع PHP++
 * * الهدف من هذا الملف:
 * توفير "اختصارات ذكية" تجعل كتابة الكود داخل المشروع سهلة وسلسة جداً، 
 * بحيث لا يحتاج المبرمج لاستدعاء الكلاسات بمساراتها الطويلة كل مرة.
 * نحن هنا نتبع مبدأ (Make it simple, keep it powerful).
 */

// ١. تسجيل مسارات الـ GET
if (!function_exists('get')) {
    /**
     * دالة get:
     * تستخدم لتحديد رابط (Route) يستجيب لطلب من نوع GET.
     * * @param string $path الرابط المطلوب (مثلاً: /profile)
     * @param mixed $callback الإجراء المطلوب تنفيذه (دالة مجهولة أو اسم كنترولر)
     */
    function get(string $path, $callback) {
        // نقوم بتمرير البيانات مباشرة لكلاس الـ Router الأساسي داخل الـ Namespace الخاص بنا
        \PHPPlusPlus\Router::get($path, $callback);
    }
}

// ٢. عرض الواجهات (Views)
if (!function_exists('view')) {
    /**
     * دالة view:
     * المحرك المسؤول عن جلب ملفات الـ HTML وعرضها للمستخدم مع حقن البيانات داخلها.
     * * @param string $name اسم ملف العرض الموجود داخل مجلد views
     * @param array $data مصفوفة البيانات التي نريد تمريرها للواجهة (اختياري)
     */
    function view(string $name, array $data = []) {
        /**
         * هنا نستدعي كلاس الـ View الذي تم التحقق منه مسبقاً.
         * هذا الكلاس سيتولى عملية استخراج البيانات (Extract) وتضمين الملف المطلوب.
         */
        \PHPPlusPlus\View::render($name, $data);
    }
}

// ٣. إطلاق نظام التوجيه (Dispatch)
if (!function_exists('dispatch')) {
    /**
     * دالة dispatch:
     * هذه هي "صافرة البداية". بعد تعريف كل المسارات (Routes)، 
     * يتم استدعاء هذه الدالة لتبدأ عملية مطابقة الرابط الحالي مع الروابط المسجلة،
     * وهنا بالتحديد يبدأ محرك الـ ++C بالعمل للمقارنة السريعة.
     */
    function dispatch() {
        // تشغيل المحرك الأساسي للراوتر لمعالجة الطلب الحالي
        \PHPPlusPlus\Router::dispatch();
    }
}
