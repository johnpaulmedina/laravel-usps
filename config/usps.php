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

];
