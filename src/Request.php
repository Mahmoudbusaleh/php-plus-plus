<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * نظام معالجة الطلبات الآمن (Secure Request Handler) - مشروع PHP++
 * * هذا الكلاس هو خط الدفاع الأول ضد هجمات الـ XSS (حقن النصوص البرمجية).
 * يقوم بفلترة وتصفية جميع البيانات القادمة من المستخدم (Inputs) قبل أن تصل 
 * لقاعدة البيانات أو تعرض في المتصفح.
 */
class Request {
    /**
     * جلب البيانات من الرابط (Query Parameters / $_GET):
     * تستخدم لجلب المتغيرات الموجودة في الرابط بعد علامة الاستفهام.
     * * @param string $key اسم المتغير المطلوب
     * @param mixed $default القيمة الافتراضية في حال عدم وجود المتغير
     */
    public static function query(string $key, $default = null) {
        // جلب القيمة وتمريرها فوراً لدالة التنظيف
        return self::sanitize($_GET[$key] ?? $default);
    }

    /**
     * جلب البيانات من النماذج (Form Data / $_POST):
     * تستخدم لجلب البيانات المرسلة عبر الفورم (مثل تسجيل الدخول أو إرسال تعليق).
     * * @param string $key اسم الحقل المطلوب
     * @param mixed $default القيمة الافتراضية
     */
    public static function post(string $key, $default = null) {
        // جلب القيمة وتمريرها فوراً لدالة التنظيف
        return self::sanitize($_POST[$key] ?? $default);
    }

    /**
     * دالة التنظيف الذكية (Sanitization):
     * هي القلب الأمني للكلاس، وتعمل بشكل "تكراري" (Recursive) لتنظيف النصوص والمصفوفات.
     */
    private static function sanitize($value) {
        // ١. إذا كانت القيمة مصفوفة، نقوم بتنظيف كل عنصر بداخلها فرداً فرداً
        if (is_array($value)) {
            return array_map([self::class, 'sanitize'], $value);
        }

        // ٢. إذا كانت القيمة نصاً (String):
        // - strip_tags: لحذف أي وسوم HTML (مثل <script>).
        // - htmlspecialchars: لتحويل الرموز الخاصة إلى نصوص غير قابلة للتنفيذ (مثل تحويل < إلى &lt;).
        // - ENT_QUOTES: لضمان تحويل العلامات الفردية والزوجية أيضاً.
        return is_string($value) 
            ? htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8') 
            : $value;
    }
}
