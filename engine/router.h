#ifndef P_PLUS_PLUS_ROUTER_H
#define P_PLUS_PLUS_ROUTER_H

/**
 * ملف الترويسة لمحرّك PHP++ (Header File)
 * * هذا الملف هو "عقد الاتفاق" بين لغة PHP ولغة ++C.
 * وظيفته الأساسية هي تعريف الدوال الموجودة في المحرك قبل استخدامها،
 * لضمان أن المترجم يعرف نوع البيانات الممررة والمستقبلة.
 */

/*
 * حماة التكرار (Include Guards):
 * السطرين #ifndef و #define في البداية، مع #endif في النهاية،
 * تضمن أن هذا الملف لا يتم استدعاؤه أكثر من مرة في نفس عملية الترجمة،
 * مما يمنع حدوث أخطاء "إعادة التعريف" (Redefinition Errors).
 */

extern "C" {
    /**
     * توقيع الدالة (Function Signature):
     * هنا نحن لا نكتب كود الدالة، بل نعلن عن وجودها فقط.
     * * * extern "C": تخبر المترجم أن هذه الدالة يجب أن تتبع قواعد لغة C في التسمية،
     * وهذا هو "المفتاح" الذي يسمح لـ PHP FFI بالتعرف على الدالة داخل ملف الـ .so.
     * * @param current_url: الرابط الحالي من المتصفح.
     * @param target_route: الرابط المستهدف للمقارنة.
     * @return: القيمة المرجعة (صح أو خطأ).
     */
    bool match_route(const char* current_url, const char* target_route) noexcept;
}

#endif // P_PLUS_PLUS_ROUTER_H
