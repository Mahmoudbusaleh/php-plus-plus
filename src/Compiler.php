<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * فئة المترجم الذكي (Compiler) - المحرك الأساسي لـ PHP++
 * * هذا الكلاس هو "القلب النابض" للمشروع، وهو المسؤول عن تهيئة بيئة العمل بالكامل.
 * فكرته الأساسية تقوم على أتمتة كل شيء؛ بمجرد تشغيل المشروع، يقوم المترجم بإنشاء 
 * المجلدات، إعداد الملفات، بل وحتى بناء محرك الـ ++C وربطه بالـ PHP تلقائياً.
 * * المطور: محمود بوسالح (Mahmoud Busaleh)
 */
class Compiler {
    /**
     * عملية البناء الرئيسية (Master Build):
     * هذه الدالة هي نقطة الانطلاق، يتم استدعاؤها بواسطة "الراوتر" لضمان أن النظام 
     * جاهز للعمل. تقوم بمزامنة الملفات والتأكد من عدم وجود نقص في هيكلة المشروع.
     */
    public static function build(): void {
        // تحديد المسار الرئيسي للمشروع بشكل مطلق لضمان عدم حدوث أخطاء عند التنقل بين المجلدات
        $root = dirname(__DIR__);

        // ١. التحقق من هيكلة المجلدات الأساسية:
        // نقوم بإنشاء المجلدات الضرورية إذا لم تكن موجودة (التخزين المؤقت، الواجهات، الملفات العامة، والمحرك).
        self::createFolders([
            $root . '/cache',  // لتخزين الروابط المترجمة وتحسين الأداء
            $root . '/views',  // لتخزين ملفات العرض (HTML/PHP)
            $root . '/public', // للملفات التي يمكن للجمهور الوصول لها (صور، CSS)
            $root . '/engine'  // المجلد الذي يحتوي على كود الـ ++C السحري
        ]);

        // ٢. إعداد ملف التوجيه (Server Configuration):
        // توليد ملف .htaccess لضمان تحويل جميع الطلبات إلى index.php (نظام Front Controller).
        self::generateHtaccess($root);

        // ٣. إدارة محرك الـ ++C (الجوهرة التقنية):
        // هنا يتم التعامل مع كود الـ ++C، سواءً بكتابة السورس كود أو بتحويله لملف باينري (.so).
        self::handleCppEngine($root);

        // ٤. إنشاء ملفات البداية (Boilerplate):
        // توليد واجهة ترحيبية للمطور الجديد ليعرف أن النظام يعمل بنجاح.
        self::generateBoilerplate($root);
        
        // ٥. تهيئة ملف الكاش:
        // لتجنب أخطاء نظام التوجيه (Router)، نتأكد من وجود ملف الروابط المترجمة حتى لو كان فارغاً.
        if (!file_exists($root . '/cache/routes_compiled.php')) {
            file_put_contents($root . '/cache/routes_compiled.php', "<?php\n return [];");
        }
    }

    /**
     * دالة إنشاء المجلدات:
     * تأخذ مصفوفة من المسارات وتنشئ المجلدات مع إعطائها صلاحيات كاملة (0777).
     */
    private static function createFolders(array $folders): void {
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                // إنشاء المجلد مع تفعيل خاصية الـ recursive لإنشاء المجلدات المتداخلة
                mkdir($folder, 0777, true);
            }
        }
    }

    /**
     * دالة إدارة محرك الـ ++C:
     * هذه هي المنطقة الأكثر إثارة؛ حيث يتم دمج لغة ++C مع PHP لتحقيق سرعة خارقة.
     * الوظيفة: مقارنة الروابط (Route Matching) تتم داخل ++C بدلاً من PHP التقليدية.
     */
    private static function handleCppEngine(string $root): void {
        $cppFile = $root . '/engine/router.cpp';
        $soFile = $root . '/engine/router.so';

        // كود الـ ++C المدمج:
        // يستخدم مكتبة cstring للمقارنة السريعة بين النصوص.
        // تم استخدام extern "C" لضمان توافق الأسماء عند استدعائها عبر PHP FFI.
        $cppCode = '#include <cstring>
extern "C" {
    /**
     * دالة مطابقة الروابط عالية السرعة.
     * تقارن رابط المستخدم الحالي بالروابط المسجلة في النظام.
     */
    bool match_route(const char* current_url, const char* target_route) noexcept {
        if (!current_url || !target_route) return false;
        return std::strcmp(current_url, target_route) == 0;
    }
}';

        // كتابة سورس كود الـ ++C إذا لم يكن موجوداً، مما يسمح للمطورين بتعديله يدوياً لاحقاً.
        if (!file_exists($cppFile)) {
            file_put_contents($cppFile, $cppCode);
        }

        // الذكاء في التحديث:
        // نقوم بإعادة بناء (Compile) الملف الثنائي (.so) في حالتين فقط:
        // ١. الملف غير موجود.
        // ٢. تم تعديل ملف السورس (.cpp) بحيث أصبح أحدث من ملف الباينري.
        if (!file_exists($soFile) || filemtime($cppFile) > filemtime($soFile)) {
            // التحقق من وجود مترجم g++ في نظام التشغيل (Linux/Server)
            $hasGpp = (bool) shell_exec('which g++');
            if ($hasGpp) {
                // عملية الترجمة:
                // -fPIC: لإنتاج كود مستقل عن الموقع (ضروري للمكتبات المشتركة).
                // -shared: لإنشاء ملف .so قابل للربط.
                // -O3: تفعيل أعلى مستويات التحسين لسرعة التنفيذ.
                $cmd = "g++ -fPIC -shared -O3 -o " . escapeshellarg($soFile) . " " . escapeshellarg($cppFile);
                shell_exec($cmd);
            }
        }
    }

    /**
     * دالة إنشاء ملف الـ .htaccess:
     * وظيفتها تقنية بحتة، وهي إخبار خادم Apache بأن كل الروابط يجب أن تذهب لملف index.php،
     * وهذا ما يسمح لنا بعمل روابط نظيفة (Clean URLs) مثل /profile بدلاً من index.php?p=profile.
     */
    private static function generateHtaccess(string $root): void {
        $path = $root . '/.htaccess'; 
        $content = "RewriteEngine On\n" .
                   "RewriteCond %{REQUEST_FILENAME} !-f\n" .
                   "RewriteCond %{REQUEST_FILENAME} !-d\n" .
                   "RewriteRule ^(.*)$ index.php [QSA,L]";

        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
    }

    /**
     * دالة توليد صفحة الترحيب:
     * إذا كان المشروع جديداً، تقوم هذه الدالة بإنشاء ملف HTML/PHP بسيط يعطي المطور
     * انطباعاً أولياً بأن محرك PHP++ والـ ++C Core يعملان بنجاح.
     */
    private static function generateBoilerplate(string $root): void {
        $viewPath = $root . '/views/welcome.php';
        if (!file_exists($viewPath)) {
            $html = "\n" .
                    "<div style='font-family: system-ui, sans-serif; text-align: center; padding: 50px;'>\n" .
                    "  <h1 style='color: #2c3e50;'>أهلاً بك في PHP++ (P++)</h1>\n" .
                    "  <p style='color: #7f8c8d;'>الحالة: <strong style='color: #27ae60;'>محرك الـ ++C يعمل بنشاط</strong></p>\n" .
                    "  <hr style='width: 100px; border: 1px solid #eee;'>\n" .
                    "  <p>يمكنك البدء بتعديل هذا الملف في <code>views/welcome.php</code></p>\n" .
                    "</div>";
            file_put_contents($viewPath, $html);
        }
    }
}
