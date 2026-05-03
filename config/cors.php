<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Angular se ejecuta normalmente en otro origen durante el desarrollo
    | local, por ejemplo http://localhost:4200. Esta configuracion permite
    | que el navegador pueda completar las peticiones E2E contra la API.
    |
    */

    // Solo aplicamos CORS a la API, que es lo que consume Angular.
    'paths' => ['api/*'],

    // Permitimos GET, POST, PUT, DELETE, OPTIONS, etc.
    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_map(
        'trim',
        explode(',', env('FRONTEND_URLS', env('FRONTEND_URL', 'http://localhost:4200')))
    )),

    'allowed_origins_patterns' => [],

    // Permitimos cabeceras como Content-Type o Authorization.
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    // No cacheamos la preflight para que los cambios se vean al momento en local.
    'max_age' => 0,

    // No usamos cookies de sesion entre dominios en esta API.
    'supports_credentials' => false,

];
