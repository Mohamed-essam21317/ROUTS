<?php

use Laravel\Sanctum\Sanctum;

return [
    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | هذه المجالات يجب أن يتم التعامل معها كـ stateful لتمكين المصادقة باستخدام 
    | الكوكيز والـ CSRF لحماية الأمان.
    |
    */

   'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1')),




    'guard' => ['web', 'api'],



    'expiration' => env('SANCTUM_TOKEN_EXPIRATION', null),

    

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'sanctum_'),






    

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],
];
