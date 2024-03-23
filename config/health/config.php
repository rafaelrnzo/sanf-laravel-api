<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Health Monitor Title
    |--------------------------------------------------------------------------
    |
    | This is the title of the health check panel, that shows up at the top-left
    | corner of the window. Feel free to edit this value to suit your needs.
    |
    */
    'title' => 'Health Check Panel',

    /*
    |--------------------------------------------------------------------------
    | Health Monitor Resources
    |--------------------------------------------------------------------------
    |
    | Below is the list of resources the health checker will look into.
    | And the path to where the resources yaml files are located.
    |
    */
    'resources' => [

        /*
        |--------------------------------------------------------------------------
        | Health Monitor Resources Path
        |--------------------------------------------------------------------------
        |
        | This value determines the path to where the resources yaml files are
        | located. By default, all resources are in config/health/resources
        |
        */
        'path' => config_path('health/resources'),

        /*
        |--------------------------------------------------------------------------
        | Health Monitor Enabled Resources
        |--------------------------------------------------------------------------
        |
        | Below is the list of resources currently enabled for your laravel application.
        | The default enabled resources are picked for the common use-case. However,
        | you are free to uncomment certain resource or add your own as you wish.
        |
        */
        'enabled' => [
            'ServerUptime',
            'SanfCoreApi',
            'DebugMode',
            'DirectoryPermissions',
            'DiskSpace',
            'Database',
            'ClientAppCredential',
            'AppKey',
            'PostgreSqlConnectable',
            'StoragePermission',
            'Queue',
            'Cache',
            'PackagesUpToDate',
            'MigrationsUpToDate',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Monitor Sort Key
    |--------------------------------------------------------------------------
    |
    | This value determines how the resources cards in your panel is sorted. By
    | default, we sort by slug, but you may use other supported values below
    |
    | Options: 'abbreviation', 'slug', 'name'
    */
    'sort_by' => 'slug',

    /*
    |--------------------------------------------------------------------------
    | Health Monitor Caching
    |--------------------------------------------------------------------------
    |
    | Below is the list of configurations for health monitor caching mechanism
    |
    */
    'cache' => [
        /*
        |--------------------------------------------------------------------------
        | Health Monitor Caching Key
        |--------------------------------------------------------------------------
        |
        | This value determines the key to use for caching the results of health
        | monitor. Please feel free to update this to suit your own convention
        |
        */
        'key' => 'health-resources',

        /*
        |--------------------------------------------------------------------------
        | Health Monitor Caching Duration
        |--------------------------------------------------------------------------
        |
        | This determines how long the results of each check should stay cached in
        | your application. When your application is in "debug" mode caching is
        | automatically disabled, otherwise we default to caching every minute
        |
        | Options:
        |   0 = Cache Forever
        |   false = Disables caching
        |   30 = (integer) Minutes to cache
        */
        'minutes' => config('app.debug') === true ? false : 1,
    ],

    'database' => [
        'enabled' => false,

        'graphs' => [
            'enabled' => true,

            'height' => 90,
        ],

        'max_records' => 30,

        'model' => PragmaRX\Health\Data\Models\HealthCheck::class,
    ],

    'services' => [
        'ping' => [
            'bin' => env('HEALTH_PING_BIN', '/sbin/ping'),
        ],

        'composer' => [
            'bin' => env('HEALTH_COMPOSER_BIN', 'composer'),
        ],
    ],

    'assets' => [
        'css' => base_path(
            'vendor/pragmarx/health/src/resources/dist/css/app.css'
        ),

        'js' => base_path(
            'vendor/pragmarx/health/src/resources/dist/js/app.js'
        ),
    ],

    'cache_files_base_path' => $path = 'app/pragmarx/health',

    'notifications' => [
        'enabled' => false,

        'notify_on' => [
            'panel' => false,
            'check' => true,
            'string' => true,
            'resource' => false,
        ],

        'subject' => 'Health Status',

        'action-title' => 'View App Health',

        'action_message' => "The '%s' service is in trouble and needs attention%s",

        'from' => [
            'name' => 'Laravel Health Checker',

            'address' => 'healthchecker@mydomain.com',

            'icon_emoji' => ':anger:',
        ],

        'scheduler' => [
            'enabled' => false,

            'frequency' => 'everyFiveMinutes', // most methods on -- https://laravel.com/docs/8.x/scheduling#schedule-frequency-options
        ],

        'channels' => ['mail', 'slack'], // mail, slack

        'notifier' => 'PragmaRX\Health\Notifications\HealthStatus',
    ],

    'alert' => [
        'success' => [
            'type' => 'success',
            'message' => 'Everything is fine with this resource',
        ],

        'error' => [
            'type' => 'error',
            'message' => 'We are having trouble with this resource',
        ],
    ],

    'style' => [
        'columnSize' => 4,
        'button_lines' => 'multi', // multi or single
        'multiplier' => 0.4,
        'opacity' => [
            'healthy' => '0.4',
            'failing' => '1',
        ],
    ],

    'views' => [
        'panel' => 'pragmarx/health::default.panel',
        'empty-panel' => 'pragmarx/health::default.empty-panel',
        'partials' => [
            'well' => 'pragmarx/health::default.partials.well',
        ],
    ],

    'routes' => [
        'namespace' => $namespace = 'NbsPhp\Core\Controllers\HealthCheckController',

        'notification' => 'pragmarx.health.panel',

        'list' => [
            [
                'uri' => 'health',
                'name' => 'app.health.status',
                'action' => "{$namespace}@checkSimplified",
//                'action' => "{$namespace}@check",
                'middleware' => [],
            ],
        ],
    ],

    'urls' => [
        'panel' => '/health/panel',
    ],
];
