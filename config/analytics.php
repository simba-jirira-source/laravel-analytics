<?php

declare(strict_types=1);
use LaravelAnalytics\LaravelAnalytics\Services\AnalyticsErrorRecorder;
use LaravelAnalytics\LaravelAnalytics\Services\PageViewRecorder;
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

        /*
        | Paths and route names that remain reachable for banned IPs. Leave
        | empty to enforce bans across the entire application.
        */
        'bypass_paths' => [],

        'bypass_route_names' => [],

    ],

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
    | Analytics Recorder
    |--------------------------------------------------------------------------
    |
    | Replace the default traffic recorder by binding a custom class that
    | implements LaravelAnalytics\LaravelAnalytics\Contracts\AnalyticsRecorder.
    |
    */

    'analytics_recorder' => PageViewRecorder::class,

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

        'prune_ip_bans' => true,

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
