<?php

return [
    'date_format'         => 'd/m/Y',
    'time_format'         => 'H:i:s',
    'primary_language'    => 'ar',
    'available_languages' => [
        'ar' => 'Arabic',
        'en' => 'English',
    ],
    'registration_default_role' => '2',

    'cache_time_long'     => 86400,
    'cache_time_medium'   => 3600,
    'cache_time_short'    => 600,
    
    'max_characters_long' => 3000,
    'max_characters_medium' => 1000,
    'max_characters_short' => 255,

    'password_min_length' => 8,
    'password_max_length' => 20,

    'phone_validation' => 'regex:/^05[0-9]{8}$/',
    'identity_validation' => 'regex:/^[12]\d{9}$/',

    'validation_price'    => 'lt:9999999999999.99',
    'validation_integer' => 'lt:99999999999',

    'image_max_size' => 5120,
    'image_mimes' => 'jpeg,png,jpg,gif,webp',
    'image_validation' => 'image|mimes:' . config('panel.image_mimes') . '|max:' . config('panel.image_max_size'),

    'file_mimes' => 'pdf,doc,docx,xls,xlsx,ppt,pptx',
    'file_max_size' => 10240, 
];
