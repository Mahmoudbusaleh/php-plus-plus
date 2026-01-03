<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * نظام التوجيه عالي السرعة (High-Speed Router) - نسخة محسنة
 * * هذا الكلاس هو المسؤول عن إدارة حركة المرور داخل التطبيق.
 * الميزة الثورية هنا هي "النظام الهجين": 
 * ١. إذا كان الرابط ثابتاً، نستخدم محرك ++C المسرّع عبر تقنية FFI.
 * ٢. إذا كان الرابط ديناميكياً (يحتوي متغيرات)، نستخدم التعبيرات القياسية (Regex).
 */
class Router {
    // مصفوفة لتخزين جميع المسارات المسجلة في النظام
    private static array $routes = [];

    /**
     * تسجيل مسار من نوع GET
     * @param string $path الرابط (مثلاً /users)
     * @param mixed $callback الإجراء (دالة مجهولة أو نص)
     */
    public static function get(string $path, $callback): void {
        self::addRoute('GET', $path, $callback);
    }

    /**
     * الدالة الداخلية لإضافة المسارات وتجهيزها
     * تقوم بتحويل الروابط التي تحتوي على أقواس {id} إلى نمط يفهمه الـ Regex
     */
    private static function addRoute(string $method, string $path, $callback): void {
        // تحويل مثل {id} إلى نمط regex مسمى لسهولة استخراج القيم لاحقاً
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        // تخزين المسار تحت مصفوفة نوع الطلب (GET/POST) مع تحديد حدود النمط
        self::$routes[$method]["#^" . $pattern . "$#D"] = $callback;
    }

    /**
     * معالجة الطلب الحالي (Dispatching)
     * هنا يتم اتخاذ القرار: هل نستخدم سرعة ++C أم مرونة PHP؟
     */
    public static function dispatch(): void {
        // استخراج الرابط المطلوب حالياً ونوع الطلب (GET/POST)
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // تحديد مسار المكتبة المشتركة لـ ++C (ملف الباينري)
        $cppLib = __DIR__ . '/../engine/router.so';
        
        /**
         * تفعيل واجهة الوظائف الخارجية (FFI):
         * نتحقق أولاً من وجود إضافة FFI في السيرفر ومن وجود ملف المكتبة.
         * إذا توفرت، نقوم بتعريف الدالة match_route الموجودة داخل ملف الـ ++C.
         */
        $ffi = (extension_loaded('ffi') && file_exists($cppLib)) ? 
               \FFI::cdef("bool match_route(const char* a, const char* b);", $cppLib) : null;

        // الدوران على جميع المسارات المسجلة للبحث عن مطابقة
        foreach (self::$routes[$method] ?? [] as $pattern => $callback) {
            $matched = false;
            $params = [];

            /**
             * المسار الأول: اختبار المسارات الثابتة عبر ++C (السرعة القصوى)
             * نلجأ لهذا المسار إذا كان FFI مفعلاً والرابط لا يحتوي على متغيرات ديناميكية.
             */
            if ($ffi && strpos($pattern, '(?P<') === false) {
                // تنظيف النمط من علامات الـ Regex للحصول على نص نقي
                $clean = str_replace(['#^', '$#D'], '', $pattern);
                
                // استدعاء دالة ++C مباشرة للمقارنة (تتم في الذاكرة بسرعة مذهلة)
                if ($ffi->match_route($uri, $clean)) {
                    $matched = true;
                }
            } 
            /**
             * المسار الثاني: اختبار المسارات الديناميكية عبر Regex
             * نستخدم PHP التقليدية هنا لاستخراج المتغيرات من الرابط (مثل ID المستخدم).
             */
            elseif (preg_match($pattern, $uri, $matches)) {
                $matched = true;
                // فلترة النتائج للحصول على المتغيرات المسماة فقط
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            }

            // إذا تم العثور على مطابقة، قم بتنفيذ الكود المطلوب
            if ($matched) {
                if (is_callable($callback)) {
                    // استدعاء الدالة وتمرير المتغيرات المستخرجة لها
                    echo call_user_func_array($callback, $params);
                } else {
                    // إذا كان الرد مجرد نص، قم بطباعته مباشرة
                    echo $callback;
                }
                return; // إنهاء العملية بعد التنفيذ بنجاح
            }
        }
        
        // في حال عدم وجود أي مطابقة، أرسل خطأ 404
        header("HTTP/1.0 404 Not Found");
        echo "404 - P++ Engine: Page Not Found";
    }
}
