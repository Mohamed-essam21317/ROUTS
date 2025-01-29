<?php


return [
    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your settings for handling Cross-Origin Resource
    | Sharing (CORS). The following options control which origins, headers,
    | and methods are allowed when making requests to your application.
    |
    */

    'paths' => ['api/*'], // حدد الـ API routes التي ستدعم CORS
    'allowed_methods' => ['*'], // السماح بكل الـ HTTP methods (GET, POST, PUT, DELETE, ...)
    'allowed_origins' => ['*'], // السماح بكل الـ origins (المصادر). لو حابب تحدد URL معين ممكن تعمله هنا
    'allowed_headers' => ['*'], // السماح بكل الهيدرز
    'exposed_headers' => [], // الهيدرز التي سيتم إرسالها مع الاستجابة (ممكن تسيبها فارغة لو مش محتاجها)
    'max_age' => 0, // المدة الزمنية التي يتم تخزين الـ CORS في المتصفح
    'supports_credentials' => false, // تحديد إذا كنت ترغب في إرسال الـ cookies مع الطلبات
];
