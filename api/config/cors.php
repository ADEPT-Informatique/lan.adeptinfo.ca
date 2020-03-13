<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */
    'supports_credentials' => false,
    'allowed_origins' => ['*'],
    'allowed_headers' => ['Content-Type', 'Authorization'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
    'exposed_headers' => [],
    'max_age' => 0,
];
