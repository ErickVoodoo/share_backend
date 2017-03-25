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
    'supportsCredentials' => false,
    'allowedOrigins' => ['All'],
    'allowedHeaders' => ['All'],
    'allowedMethods' => ['All'],
    'exposedHeaders' => [],
    'maxAge' => 0,
    'hosts' => [],
];
