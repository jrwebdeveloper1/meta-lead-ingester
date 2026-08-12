<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Meta App Secret
    |--------------------------------------------------------------------------
    |
    | Used for verifying the X-Hub-Signature-256 HMAC signature on incoming
    | webhook requests from Meta. Find this in your Meta App Dashboard.
    |
    */
    'app_secret' => env('META_APP_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Meta Graph API Version
    |--------------------------------------------------------------------------
    |
    | The version of the Meta Graph API used to fetch lead details.
    |
    */
    'graph_api_version' => env('META_GRAPH_API_VERSION', 'v20.0'),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix for the webhook routes registered by this package.
    |
    */
    'route_prefix' => env('META_LEAD_INGESTER_ROUTE_PREFIX', 'api/meta-lead-ingester'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connection
    |--------------------------------------------------------------------------
    |
    | The queue connection to use for processing incoming meta leads asynchronously.
    |
    */
    'queue' => env('META_LEAD_INGESTER_QUEUE', 'default'),
];
