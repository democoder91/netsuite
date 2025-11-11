<?php

return [

    /*
    |--------------------------------------------------------------------------
    | NetSuite Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for NetSuite API integration.
    | All values are loaded from your .env file.
    |
    */

    'endpoint' => env('NETSUITE_ENDPOINT', '2025_1'),

    'host' => env('NETSUITE_HOST', 'https://webservices.netsuite.com'),

    'account' => env('NETSUITE_ACCOUNT'),

    /*
    |--------------------------------------------------------------------------
    | Token Based Authentication
    |--------------------------------------------------------------------------
    |
    | NetSuite uses OAuth 1.0 for token-based authentication.
    | You need to create an integration record in NetSuite and generate
    | consumer key/secret and token key/secret.
    |
    */

    'hash_type' => env('NETSUITE_HASH_TYPE', 'sha256'),

    'consumer_key' => env('NETSUITE_CONSUMER_KEY'),

    'consumer_secret' => env('NETSUITE_CONSUMER_SECRET'),

    'token_key' => env('NETSUITE_TOKEN_KEY'),

    'token_secret' => env('NETSUITE_TOKEN_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | REST API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for NetSuite REST API endpoints including SuiteQL.
    |
    */

    'rest' => [
        'suiteql_endpoint' => env('NETSUITE_HOST', 'https://webservices.netsuite.com') . '/services/rest/query/v1/suiteql',
    ],

    /*
    |--------------------------------------------------------------------------
    | Credential Based Authentication (Deprecated)
    |--------------------------------------------------------------------------
    |
    | NetSuite has deprecated password-based authentication in favor of
    | token-based authentication. These are kept for legacy support only.
    |
    */

    'email' => env('NETSUITE_EMAIL'),

    'password' => env('NETSUITE_PASSWORD'),

    'app_id' => env('NETSUITE_APP_ID'),

    /*
    |--------------------------------------------------------------------------
    | OAuth 2.0 Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OAuth 2.0 flows if needed.
    |
    */

    'redirect_uri' => env('NETSUITE_REDIRECT_URI'),

    'client_id' => env('NETSUITE_CLIENT_ID'),

    'client_secret' => env('NETSUITE_CLIENT_SECRET'),

    'scope' => env('NETSUITE_SCOPE', 'rest_webservices'),

];

