<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration determines which cross-origin operations may execute
    | in web browsers. You may adjust these settings as needed.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Here you can specify which origins are allowed to access your API.
    | You can list multiple origins for local dev (3000, 3001) and also
    | add a fallback to your production domain via .env.
    |
    */

    // Prefer patterns for local dev so ports can vary
    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:3001',
    ],
    'allowed_origins_patterns' => [
        '/^http:\/\/localhost(?::\d+)?$/',
        '/^http:\/\/127\.0\.0\.1(?::\d+)?$/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | These are the HTTP methods that can be used when making requests.
    |
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | These are the request headers that are allowed.
    |
    */

    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | These headers are exposed to the browser.
    |
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | The maximum time (in seconds) that the results of a preflight request
    | can be cached.
    |
    */

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Indicates whether or not the response to the request can be exposed
    | when the credentials flag is true.
    |
    */

    'supports_credentials' => true,

];
