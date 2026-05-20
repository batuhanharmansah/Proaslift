<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional location to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Maps API Configuration
    |--------------------------------------------------------------------------
    | COST-OPTIMIZED SETTINGS:
    | - Only Geocoding API is enabled (no unnecessary APIs)
    | - API key should be restricted to Geocoding API only in Google Cloud Console
    | - Usage is minimized: only on SAVE/UPDATE, cached for 30 days
    |
    | Security Best Practices:
    | 1. Restrict API key to Geocoding API only in Google Cloud Console
    | 2. Add HTTP referrer restrictions (your domain only)
    | 3. Set up billing alerts ($50 recommended limit)
    | 4. Monitor usage in Google Cloud Console
    |
    */
    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
        'geocoding_enabled' => env('GOOGLE_GEOCODING_ENABLED', false),
        // Cost optimization: Use Google only if enabled, fallback to free OSM
        'use_free_osm_first' => env('GOOGLE_USE_OSM_FIRST', false), // Set to true to prefer OSM (FREE)
    ],

];
