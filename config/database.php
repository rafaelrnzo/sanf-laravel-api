<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => env('DB_PREFIX', ''),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', true),
            'engine' => env('DB_ENGINE', null),
            'timezone' => env('DB_TIMEZONE', '+00:00'),
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 5432),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => env('DB_PREFIX', ''),
            'schema' => env('DB_SCHEMA', 'public'),
            'sslmode' => env('DB_SSL_MODE', 'prefer'),
        ],

        'pgsql_sodium' => [
            'driver' => 'pgsql',
            'host' => env('DB_ENCRYPTED_HOST', '127.0.0.1'),
            'port' => env('DB_ENCRYPTED_PORT', 5432),
            'database' => env('DB_ENCRYPTED_DATABASE', 'forge'),
            'username' => env('DB_ENCRYPTED_USERNAME', 'forge'),
            'password' => env('DB_ENCRYPTED_PASSWORD', ''),
            'charset' => env('DB_ENCRYPTED_CHARSET', 'utf8'),
            'prefix' => env('DB_ENCRYPTED_PREFIX', ''),
            'schema' => env('DB_ENCRYPTED_SCHEMA', 'public'),
            'sslmode' => env('DB_ENCRYPTED_SSL_MODE', 'prefer'),
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', 1433),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => env('DB_PREFIX', ''),
        ],

        'dashboard_db' => [
            'driver' => 'pgsql',
            'url' => env('DASHBOARD_DATABASE_URL'),
            'host' => env('DASHBOARD_DB_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('DASHBOARD_DB_PORT', env('DB_PORT', '5432')),
            'database' => env('DASHBOARD_DB_DATABASE', 'database'),
            'username' => env('DASHBOARD_DB_USERNAME', env('DB_USERNAME', 'postgres')),
            'password' => env('DASHBOARD_DB_PASSWORD', env('DB_PASSWORD', '')),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => implode(',', array_filter([env('DASHBOARD_DB_SCHEMA'), 'public'])),
            'sslmode' => 'prefer',
            'schema' => env('DASHBOARD_DB_SCHEMA', 'sanf_dashboard_sch'),
        ],

        'dashboard_db_sodium' => [
            'driver' => 'pgsql',
            'url' => env('DASHBOARD_DATABASE_ENCRYPTED_URL'),
            'host' => env('DASHBOARD_DB_ENCRYPTED_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('DASHBOARD_DB_ENCRYPTED_PORT', env('DB_PORT', '5432')),
            'database' => env('DASHBOARD_DB_ENCRYPTED_DATABASE', 'database'),
            'username' => env('DASHBOARD_DB_ENCRYPTED_USERNAME', env('DB_USERNAME', 'postgres')),
            'password' => env('DASHBOARD_DB_ENCRYPTED_PASSWORD', env('DB_PASSWORD', '')),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => implode(',', array_filter([env('DASHBOARD_DB_ENCRYPTED_SCHEMA'), 'public'])),
            'sslmode' => 'prefer',
            'schema' => env('DASHBOARD_DB_ENCRYPTED_SCHEMA', 'sanf_dashboard_sch'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'cluster' => env('REDIS_CLUSTER', false),

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],

    ],

];
