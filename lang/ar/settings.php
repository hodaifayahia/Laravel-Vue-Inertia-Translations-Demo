<?php

return [
    // Main settings
    'title' => 'الإعدادات',
    'description' => 'إدارة ملفك الشخصي وإعدادات الحساب',
    
    // Navigation
    'nav' => [
        'profile' => 'الملف الشخصي',
        'password' => 'كلمة المرور',
        'two_factor' => 'المصادقة الثنائية',
        'appearance' => 'المظهر',
        'customization' => 'التخصيص',
    ],
    
    // Profile settings
    'profile' => [
        'title' => 'إعدادات الملف الشخصي',
        'heading' => 'معلومات الملف الشخصي',
        'description' => 'تحديث اسمك وعنوان بريدك الإلكتروني',
        'name' => 'الاسم',
        'email' => 'عنوان البريد الإلكتروني',
        'name_placeholder' => 'الاسم الكامل',
        'email_placeholder' => 'عنوان البريد الإلكتروني',
        'save' => 'حفظ',
        'saved' => 'تم الحفظ.',
        'email_unverified' => 'عنوان بريدك الإلكتروني غير موثق.',
        'resend_verification' => 'انقر هنا لإعادة إرسال بريد التحقق الإلكتروني.',
        'verification_sent' => 'تم إرسال رابط تحقق جديد إلى عنوان بريدك الإلكتروني.',
    ],
    
    // Password settings
    'password' => [
        'title' => 'إعدادات كلمة المرور',
        'heading' => 'تحديث كلمة المرور',
        'description' => 'تأكد من أن حسابك يستخدم كلمة مرور طويلة وعشوائية للبقاء آمنًا',
        'current_password' => 'كلمة المرور الحالية',
        'new_password' => 'كلمة المرور الجديدة',
        'confirm_password' => 'تأكيد كلمة المرور',
        'current_password_placeholder' => 'كلمة المرور الحالية',
        'new_password_placeholder' => 'كلمة المرور الجديدة',
        'confirm_password_placeholder' => 'تأكيد كلمة المرور',
        'save' => 'حفظ كلمة المرور',
        'saved' => 'تم الحفظ.',
    ],
    
    // Appearance settings
    'appearance' => [
        'title' => 'إعدادات المظهر',
        'heading' => 'إعدادات المظهر',
        'description' => 'تحديث إعدادات مظهر حسابك',
        'language' => 'اللغة',
        'language_description' => 'اختر لغتك المفضلة',
        'save' => 'حفظ التفضيلات',
        'saved' => 'تم الحفظ.',
    ],
    
    // Two-Factor Authentication
    'two_factor' => [
        'title' => 'المصادقة الثنائية',
        'heading' => 'المصادقة الثنائية',
        'description' => 'إدارة إعدادات المصادقة الثنائية الخاصة بك',
        'enabled' => 'مفعلة',
        'disabled' => 'معطلة',
        'enable' => 'تفعيل المصادقة الثنائية',
        'disable' => 'تعطيل المصادقة الثنائية',
        'continue_setup' => 'متابعة الإعداد',
        'enabled_description' => 'مع تمكين المصادقة الثنائية، ستُطالب بإدخال رمز آمن وعشوائي أثناء تسجيل الدخول، والذي يمكنك الحصول عليه من تطبيق TOTP على هاتفك.',
        'disabled_description' => 'عند تمكين المصادقة الثنائية، ستُطالب بإدخال رمز آمن أثناء تسجيل الدخول. يمكن الحصول على هذا الرمز من تطبيق TOTP على هاتفك.',
        'setup_title' => 'تفعيل المصادقة الثنائية',
        'setup_description' => 'تضيف المصادقة الثنائية طبقة إضافية من الأمان إلى حسابك من خلال طلب أكثر من مجرد كلمة مرور لتسجيل الدخول.',
        'scan_qr' => 'امسح رمز الاستجابة السريعة أدناه باستخدام تطبيق المصادقة الخاص بك',
        'manual_entry' => 'أو أدخل هذا الرمز يدويًا:',
        'enter_code' => 'أدخل الرمز المكون من 6 أرقام من تطبيق المصادقة الخاص بك',
        'code_placeholder' => '000000',
        'verify' => 'التحقق والتفعيل',
        'recovery_codes' => 'رموز الاسترداد',
        'recovery_codes_description' => 'احفظ رموز الاسترداد هذه في مدير كلمات مرور آمن. يمكن استخدامها لاستعادة الوصول إلى حسابك في حالة فقدان جهاز المصادقة الثنائية.',
        'regenerate_codes' => 'إعادة إنشاء رموز الاسترداد',
        'show_codes' => 'إظهار رموز الاسترداد',
        'hide_codes' => 'إخفاء رموز الاسترداد',
        'download_codes' => 'تحميل',
        'copy_codes' => 'نسخ',
        'codes_copied' => 'تم نسخ رموز الاسترداد إلى الحافظة',
    ],
    
    // Delete account
    'delete' => [
        'heading' => 'حذف الحساب',
        'description' => 'حذف حسابك وجميع موارده',
        'warning_title' => 'تحذير',
        'warning_message' => 'يرجى المتابعة بحذر، هذا الإجراء لا يمكن التراجع عنه.',
        'button' => 'حذف الحساب',
        'confirm_title' => 'هل أنت متأكد من رغبتك في حذف حسابك؟',
        'confirm_description' => 'بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته بشكل دائم أيضًا. يرجى إدخال كلمة المرور الخاصة بك لتأكيد رغبتك في حذف حسابك نهائيًا.',
        'password' => 'كلمة المرور',
        'password_placeholder' => 'كلمة المرور',
        'cancel' => 'إلغاء',
        'confirm' => 'حذف الحساب',
    ],
    
    // Customization
    'customization' => [
        'title' => 'إعدادات التخصيص',
        'heading' => 'تخصيص تجربتك',
        'description' => 'خصص مظهر وشعور تطبيقك',
        'save' => 'حفظ التغييرات',
        'saving' => 'جاري الحفظ...',
        'saved' => 'تم الحفظ بنجاح!',
        
        'tabs' => [
            'welcome' => 'صفحة الترحيب',
            'theme' => 'ألوان المظهر',
            'branding' => 'العلامة التجارية',
        ],
        
        'welcome' => [
            'title' => 'إعدادات صفحة الترحيب',
            'description' => 'تخصيص محتوى ومظهر صفحة الترحيب',
            'show_page' => 'عرض صفحة الترحيب',
            'show_page_description' => 'عرض صفحة الترحيب للزوار',
            'page_title' => 'عنوان الصفحة',
            'page_title_placeholder' => 'أدخل عنوان الصفحة',
            'page_subtitle' => 'العنوان الفرعي للصفحة',
            'page_subtitle_placeholder' => 'أدخل العنوان الفرعي للصفحة',
            'page_description' => 'وصف الصفحة',
            'page_description_placeholder' => 'أدخل وصف الصفحة',
        ],
        
        'theme' => [
            'title' => 'نظام الألوان',
            'description' => 'تخصيص نظام ألوان تطبيقك',
            'primary_color' => 'اللون الأساسي',
            'primary_color_description' => 'لون العلامة التجارية الرئيسي المستخدم للأزرار والروابط',
            'secondary_color' => 'اللون الثانوي',
            'secondary_color_description' => 'لون الدعم للتمييزات والإبرازات',
            'accent_color' => 'لون التمييز',
            'accent_color_description' => 'لون للعناصر الخاصة والدعوات إلى الإجراء',
            'color_placeholder' => '#000000',
            'preview' => 'معاينة الألوان',
        ],
        
        'branding' => [
            'title' => 'هوية العلامة التجارية',
            'description' => 'قم بتكوين أصول علامتك التجارية وهويتها',
            'logo_text' => 'نص الشعار',
            'logo_text_placeholder' => 'أدخل اسم علامتك التجارية',
            'logo_text_description' => 'النص المعروض في شعار التطبيق',
            'favicon' => 'رابط الأيقونة المفضلة',
            'favicon_placeholder' => 'https://example.com/favicon.ico',
            'favicon_description' => 'رابط الأيقونة المفضلة لموقعك',
            'preview' => 'معاينة الشعار',
        ],
        
        'reset' => [
            'title' => 'إعادة تعيين التخصيص',
            'description' => 'إعادة تعيين جميع إعدادات التخصيص إلى قيمها الافتراضية',
            'button' => 'إعادة تعيين إلى الافتراضي',
        ],
    ],
];
