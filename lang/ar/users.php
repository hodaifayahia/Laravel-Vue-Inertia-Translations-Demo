<?php

return [
    // Page titles and headers
    'title' => 'إدارة المستخدمين',
    'description' => 'إدارة جميع المستخدمين في النظام',
    'user_list' => 'قائمة المستخدمين',
    
    // Actions
    'add_user' => 'إضافة مستخدم',
    'add_first_user' => 'إضافة أول مستخدم',
    'create_user' => 'إنشاء مستخدم',
    'edit_user' => 'تعديل المستخدم',
    'delete_user' => 'حذف المستخدم',
    'create' => 'إنشاء',
    'update' => 'تحديث',
    'delete_confirm' => 'نعم، احذف',
    'cancel' => 'إلغاء',
    'edit' => 'تعديل',
    'delete' => 'حذف',
    'actions' => 'الإجراءات',
    'filters' => 'الفلاتر',
    
    // Form fields
    'name' => 'الاسم',
    'email' => 'البريد الإلكتروني',
    'password' => 'كلمة المرور',
    'password_confirmation' => 'تأكيد كلمة المرور',
    'language' => 'اللغة',
    'locale' => 'اللغة',
    'roles' => 'الأدوار',
    'assign_roles' => 'تعيين الأدوار',
    'roles_description' => 'حدد دورًا واحدًا أو أكثر لتعيينه لهذا المستخدم',
    
    // Placeholders
    'name_placeholder' => 'أدخل الاسم الكامل',
    'email_placeholder' => 'example@domain.com',
    'password_placeholder' => 'إنشاء كلمة مرور آمنة',
    'password_confirmation_placeholder' => 'أكد كلمة المرور الخاصة بك',
    'search_placeholder' => 'البحث بالاسم أو البريد الإلكتروني...',
    
    // Status
    'status' => 'الحالة',
    'verified' => 'موثق',
    'unverified' => 'غير موثق',
    'verified_only' => 'الموثقون فقط',
    'unverified_only' => 'غير الموثقين فقط',
    'all_users' => 'جميع المستخدمين',
    
    // Stats
    'total_users' => 'إجمالي المستخدمين',
    'verified_users' => 'المستخدمون الموثقون',
    'unverified_users' => 'المستخدمون غير الموثقين',
    
    // Table columns
    'user' => 'المستخدم',
    'joined' => 'تاريخ الانضمام',
    
    // Messages
    'creating' => 'جاري الإنشاء...',
    'updating' => 'جاري التحديث...',
    'deleting' => 'جاري الحذف...',
    'created_successfully' => 'تم إنشاء المستخدم بنجاح',
    'updated_successfully' => 'تم تحديث المستخدم بنجاح',
    'deleted_successfully' => 'تم حذف المستخدم بنجاح',
    'cannot_delete_yourself' => 'لا يمكنك حذف نفسك',
    'leave_blank_to_keep' => 'اتركه فارغًا للاحتفاظ بكلمة المرور الحالية',
    'roles_updated' => 'تم تحديث أدوار المستخدم بنجاح',
    'permissions_updated' => 'تم تحديث صلاحيات المستخدم بنجاح',
    
    // Descriptions
    'create_user_description' => 'إضافة مستخدم جديد إلى النظام مع تفاصيله',
    'edit_user_description' => 'تحديث معلومات وإعدادات المستخدم',
    'delete_user_warning' => 'هذا الإجراء لا يمكن التراجع عنه',
    'delete_user_confirm' => 'هل أنت متأكد من حذف :name؟',
    
    // Empty state
    'no_users' => 'لم يتم العثور على مستخدمين',
    'no_users_description' => 'لا يوجد مستخدمون يطابقون معايير البحث الخاصة بك.',
    
    // Pagination
    'showing_results' => 'عرض :total مستخدم',
    'pagination_info' => 'عرض من :from إلى :to من أصل :total نتيجة',
    
    // Validation messages
    'validation' => [
        'name_required' => 'الاسم مطلوب',
        'email_required' => 'البريد الإلكتروني مطلوب',
        'email_invalid' => 'يجب أن يكون البريد الإلكتروني عنوانًا صالحًا',
        'email_unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
        'password_required' => 'كلمة المرور مطلوبة',
        'password_confirmed' => 'تأكيد كلمة المرور غير متطابق',
    ],
];
