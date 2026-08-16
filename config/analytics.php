<?php

declare(strict_types=1);
use LaravelAnalytics\LaravelAnalytics\Services\AnalyticsErrorRecorder;
use LaravelAnalytics\LaravelAnalytics\Support\DefaultVisitorIdentifier;

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Master switch for package features. When disabled, tracking middleware
    | and recorders should not persist analytics data.
    |
    */

    'enabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    'dashboard' => [

        'enabled' => false,

        'path' => 'analytics',

        'route_prefix' => 'analytics.',

        'middleware' => ['web', 'auth'],

        /*
        | Gate name, policy class, or callable class registered in the host
        | application. When null, dashboard routes remain disabled by default.
        */
        'authorization' => null,

        'pagination' => [
            'per_page' => 25,
        ],

        'cache_ttl' => 300,

    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking
    |--------------------------------------------------------------------------
    */

    'tracking' => [

        'traffic' => false,

        'errors' => false,

    ],

    /*
    |--------------------------------------------------------------------------
    | IP Banning
    |--------------------------------------------------------------------------
    */

    'ip_banning' => [

        'enabled' => false,

        'blocked_status' => 403,

    ],

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | When null, the package relies on the host application's trusted proxy
    | configuration when resolving client IP addresses.
    |
    */

    'trusted_proxies' => null,

    /*
    |--------------------------------------------------------------------------
    | Privacy
    |--------------------------------------------------------------------------
    */

    'privacy' => [

        'store_raw_ip' => false,

        'hash_ips' => true,

        /*
        | When null, the application key is used as the hashing salt source.
        */
        'hash_salt' => null,

        'track_authenticated_users' => false,

        'collect_referrer' => true,

        'collect_user_agent' => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Visitor Identification
    |--------------------------------------------------------------------------
    |
    | Replace the default visitor identifier by binding a custom class that
    | implements LaravelAnalytics\LaravelAnalytics\Contracts\VisitorIdentifier.
    |
    */

    'visitor_identifier' => DefaultVisitorIdentifier::class,

    /*
    |--------------------------------------------------------------------------
    | Error Recorder
    |--------------------------------------------------------------------------
    |
    | Replace the default error recorder by binding a custom class that
    | implements LaravelAnalytics\LaravelAnalytics\Contracts\ErrorRecorder.
    |
    */

    'error_recorder' => AnalyticsErrorRecorder::class,

    /*
    |--------------------------------------------------------------------------
    | User Association
    |--------------------------------------------------------------------------
    |
    | Optional association with an authenticated user model. The package does
    | not assume a specific User model or foreign key constraint.
    |
    */

    'user' => [

        'model' => null,

        'foreign_key' => 'user_id',

    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored Requests
    |--------------------------------------------------------------------------
    */

    'ignored' => [

        'paths' => [
            'analytics',
            'analytics/*',
        ],

        'route_names' => [
            'analytics.*',
        ],

        'methods' => [
            'OPTIONS',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Retention
    |--------------------------------------------------------------------------
    */

    'retention' => [

        'days' => 90,

        'prune_page_views' => true,

        'prune_visitors' => true,

        'prune_errors' => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Response Status Codes
    |--------------------------------------------------------------------------
    |
    | HTTP status codes that should not be recorded as page views when set.
    |
    */

    'excluded_status_codes' => [],

];
