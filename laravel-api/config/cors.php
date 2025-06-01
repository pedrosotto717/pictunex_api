<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This enables selective cross-origin access to your resources.
    | You may define multiple "paths" to be assigned distinct CORS
    | configurations, or simply use this single package configuration
    | for the entire application.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*', 'Authorization-Token', 'Client-Id', 'Authentication'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];