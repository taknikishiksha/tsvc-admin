<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    | 18 अलग-अलग Roles के लिए अलग-अलग guards define किए गए हैं।
    | सभी के लिए provider 'users' है, यानी एक ही users टेबल का उपयोग होगा।
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'superadmin' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'hr' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'finance' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'training' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'exam' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'usermgmt' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'service' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'client' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'teacher' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'student' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'partner' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'consultant' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'volunteer' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'intern' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'donor' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'corporate' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'affiliate' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    | सभी रोल्स के लिए एक ही user मॉडल और टेबल का उपयोग हो रहा है।
    */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Settings
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
