<?php

/**
 * رسائل التحقق بالعربية.
 * بدون هذا الملف كانت العربية ترجع إلى الفرنسية (fallback). الآن يرى المستخدم
 * العربي رسائل مفهومة بالعربية بدل المفاتيح الخام أو الفرنسية.
 * أسماء الحقول في 'attributes' وبعض الرسائل الخاصة في 'custom'.
 */
return [
    'accepted'             => 'يجب قبول :attribute.',
    'active_url'           => ':attribute ليس رابطًا صحيحًا.',
    'after'                => 'يجب أن يكون :attribute تاريخًا بعد :date.',
    'alpha'                => 'يجب أن يحتوي :attribute على حروف فقط.',
    'alpha_dash'           => 'يجب أن يحتوي :attribute على حروف وأرقام وشرطات فقط.',
    'alpha_num'            => 'يجب أن يحتوي :attribute على حروف وأرقام فقط.',
    'array'                => 'يجب أن يكون :attribute مصفوفة.',
    'before'               => 'يجب أن يكون :attribute تاريخًا قبل :date.',
    'between'              => [
        'numeric' => 'يجب أن يكون :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'string'  => 'يجب أن يحتوي :attribute بين :min و :max حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute بين :min و :max عنصرًا.',
    ],
    'boolean'              => 'يجب أن تكون قيمة :attribute صحيحة أو خاطئة.',
    'confirmed'            => 'تأكيد :attribute غير مطابق.',
    'date'                 => ':attribute ليس تاريخًا صحيحًا.',
    'date_format'          => ':attribute لا يطابق الصيغة :format.',
    'different'            => 'يجب أن يكون :attribute و :other مختلفين.',
    'digits'               => 'يجب أن يحتوي :attribute على :digits أرقام.',
    'digits_between'       => 'يجب أن يحتوي :attribute بين :min و :max رقمًا.',
    'dimensions'           => 'أبعاد الصورة :attribute غير صحيحة.',
    'email'                => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيحًا.',
    'exists'               => 'القيمة المحددة لـ :attribute غير صحيحة.',
    'file'                 => 'يجب أن يكون :attribute ملفًا.',
    'filled'               => 'يجب تعبئة حقل :attribute.',
    'image'                => 'يجب أن يكون :attribute صورة.',
    'in'                   => 'القيمة المحددة لـ :attribute غير صحيحة.',
    'integer'              => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'ip'                   => 'يجب أن يكون :attribute عنوان IP صحيحًا.',
    'json'                 => 'يجب أن يكون :attribute نص JSON صحيحًا.',
    'max'                  => [
        'numeric' => 'يجب ألا يكون :attribute أكبر من :max.',
        'file'    => 'يجب ألا يتجاوز حجم الملف :attribute قيمة :max كيلوبايت.',
        'string'  => 'يجب ألا يتجاوز :attribute :max حرفًا.',
        'array'   => 'يجب ألا يحتوي :attribute على أكثر من :max عنصرًا.',
    ],
    'mimes'                => 'يجب أن يكون :attribute ملفًا من نوع: :values.',
    'mimetypes'            => 'يجب أن يكون :attribute ملفًا من نوع: :values.',
    'min'                  => [
        'numeric' => 'يجب أن يكون :attribute على الأقل :min.',
        'file'    => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت.',
        'string'  => 'يجب أن يحتوي :attribute على الأقل :min حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute على الأقل :min عنصرًا.',
    ],
    'not_in'               => 'القيمة المحددة لـ :attribute غير صحيحة.',
    'numeric'              => 'يجب أن يكون :attribute رقمًا.',
    'present'              => 'يجب أن يكون حقل :attribute موجودًا.',
    'regex'                => 'صيغة :attribute غير صحيحة.',
    'required'             => 'حقل :attribute مطلوب.',
    'required_if'          => 'حقل :attribute مطلوب عندما تكون قيمة :other هي :value.',
    'required_with'        => 'حقل :attribute مطلوب عندما يكون :values موجودًا.',
    'required_without'     => 'حقل :attribute مطلوب عندما لا يكون :values موجودًا.',
    'same'                 => 'يجب أن يتطابق :attribute و :other.',
    'size'                 => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية لـ :size.',
        'file'    => 'يجب أن يكون حجم الملف :attribute مساويًا لـ :size كيلوبايت.',
        'string'  => 'يجب أن يحتوي :attribute على :size حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute على :size عنصرًا.',
    ],
    'string'               => 'يجب أن يكون :attribute نصًا.',
    'timezone'             => 'يجب أن يكون :attribute منطقة زمنية صحيحة.',
    'unique'               => 'قيمة :attribute مستخدمة من قبل.',
    'uploaded'             => 'فشل رفع :attribute.',
    'url'                  => 'صيغة الرابط :attribute غير صحيحة.',

    /*
     | رسائل خاصة بالحقل.القاعدة (أولوية على الرسائل العامة).
     */
    'custom' => [
        'email' => [
            'unique' => 'هذا البريد الإلكتروني مستخدم من قبل. سجّل الدخول أو استخدم بريدًا آخر.',
            'email'  => 'البريد الإلكتروني الذي أدخلته غير صحيح.',
        ],
        'phone' => [
            'unique' => 'رقم الهاتف هذا مستخدم من قبل. سجّل الدخول أو استخدم رقمًا آخر.',
        ],
        'password' => [
            'confirmed' => 'تأكيد كلمة المرور غير مطابق.',
            'min'       => 'يجب أن تحتوي كلمة المرور على الأقل :min حرفًا.',
        ],
    ],

    /*
     | أسماء الحقول بالعربية (تحل محل :attribute).
     */
    'attributes' => [
        'name'                  => 'الاسم الكامل',
        'nom'                   => 'الاسم',
        'email'                 => 'البريد الإلكتروني',
        'password'              => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'phone'                 => 'الهاتف',
        'telephone'             => 'الهاتف',
        'company'               => 'اسم الشركة',
        'type'                  => 'نوع الحساب',
        'ae_role'               => 'الدور',
        'city'                  => 'المدينة',
        'ville'                 => 'المدينة',
        'mark'                  => 'الماركة',
        'modele'                => 'الموديل',
        'year'                  => 'السنة',
        'prix'                  => 'السعر',
        'km'                    => 'عدد الكيلومترات',
        'carburant'             => 'الوقود',
        'boite'                 => 'علبة السرعة',
        'box'                   => 'علبة السرعة',
        'etat'                  => 'الحالة',
        'origine'               => 'الأصل',
        'titre'                 => 'العنوان',
        'description'           => 'الوصف',
        'message'               => 'الرسالة',
        'ice'                   => 'ICE',
        'images'                => 'الصور',
        'image'                 => 'الصورة',
        'video'                 => 'الفيديو',
    ],
];
