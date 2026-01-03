<?php
declare(strict_types=1);

namespace PHPPlusPlus;

/**
 * نظام إدارة الاستجابة (Response Handler) - مشروع PHP++
 * * هذا الكلاس هو المسؤول عن إرسال البيانات النهائية للمستخدم.
 * يتحكم في أنواع الملفات (Headers)، حالات الخطأ (HTTP Status Codes)، 
 * وعمليات إعادة التوجيه.
 */
class Response {
    
    /**
     * إرسال رد نصي أو HTML:
     * تستخدم لعرض المحتوى العادي للمتصفح.
     * * @param string $content المحتوى المراد عرضه (نص أو كود HTML)
     * @param int $status رمز الحالة (الافتراضي 200 وهو رمز النجاح)
     */
    public static function send(string $content, int $status = 200): void {
        // تحديد رمز الحالة للطلب (مثل 200 للنجاح أو 404 للخطأ)
        http_response_code($status);
        // طباعة المحتوى للمتصفح
        echo $content;
    }

    /**
     * إرسال رد بصيغة JSON:
     * هذي الدالة "كنز" لمبرمجي التطبيقات (Mobile Apps) والـ APIs.
     * تحول المصفوفات البرمجية إلى نصوص JSON يفهمها أي نظام في العالم.
     * * @param array $data المصفوفة المراد تحويلها
     * @param int $status رمز الحالة
     */
    public static function json(array $data, int $status = 200): void {
        // إخبار المتصفح أو التطبيق أن البيانات القادمة هي JSON وليست صفحة HTML
        header('Content-Type: application/json');
        // تحديد رمز الحالة
        http_response_code($status);
        // تحويل المصفوفة إلى نص JSON وطباعته
        echo json_encode($data);
    }

    /**
     * إعادة التوجيه (Redirect):
     * تستخدم لنقل المستخدم من صفحة إلى صفحة أخرى تلقائياً.
     * * @param string $url الرابط المراد الانتقال إليه
     */
    public static function redirect(string $url): void {
        // إرسال رأس الصفحة الخاص بنقل الموقع
        header("Location: $url");
        // إيقاف تنفيذ أي كود إضافي بعد عملية التحويل لضمان الأمان والسرعة
        exit;
    }
}
