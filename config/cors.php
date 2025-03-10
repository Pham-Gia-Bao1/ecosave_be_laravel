<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'api/csrf-token', 'storage/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Allow any domain
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Set to true if sending cookies or auth tokens
];
