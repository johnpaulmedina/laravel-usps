<?php

return [

    /*
    |--------------------------------------------------------------------------
    | USPS API v3 — OAuth2 Client Credentials
    |--------------------------------------------------------------------------
    |
    | Register at https://developer.usps.com to obtain your client ID and
    | secret. These are used to obtain an OAuth2 access token for the
    | USPS Addresses API v3.
    |
    */

    'client_id' => env('USPS_CLIENT_ID', ''),
    'client_secret' => env('USPS_CLIENT_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware applied to all /api/usps/* routes. Add 'auth:api',
    | 'auth:sanctum', or any custom middleware to protect the endpoints.
    |
    */

    'route_middleware' => explode(',', env('USPS_ROUTE_MIDDLEWARE', 'api')),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | USPS API rate limits vary by account tier. Set a throttle to prevent
    | exceeding your limit. Format: "requests,minutes" (e.g., "60,1").
    | Leave empty to disable package-level throttling.
    |
    | See: https://developer.usps.com/api/81
    |
    */

    'throttle' => env('USPS_THROTTLE', ''),

];
