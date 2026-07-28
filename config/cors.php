<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    | Karena frontend adalah HTML statis di origin terpisah (misal dibuka
    | dari Live Server / file hosting lain), CORS wajib diatur eksplisit.
    | supports_credentials = false karena kita pakai Bearer token
    | (bukan cookie), jadi tidak perlu kirim credentials lintas origin.
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // GANTI dengan domain/port asli tempat frontend statis kamu di-serve.
    // Selama development biasanya pakai Live Server (VS Code) di port 5500,
    // atau `php -S localhost:8080` di folder frontend/.
    'allowed_origins' => [
        'http://localhost:5500',
        'https://gleeful-cheesecake-ef19c1.netlify.app',
        'http://127.0.0.1:5500',
        'http://localhost:8080',
        'http://127.0.0.1:8080'
        // tambahkan domain production frontend di sini saat deploy, contoh:
        // 'https://tomoto-frontend.example.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // WAJIB false: kita pakai Authorization: Bearer {token}, bukan cookie session.
    'supports_credentials' => true,

];
