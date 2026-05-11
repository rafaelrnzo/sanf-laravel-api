<?php

namespace NbsPhp\Core\Providers;

use Fideloper\Proxy\TrustedProxyServiceProvider;
use Godruoyi\Snowflake\LaravelSequenceResolver;
use Godruoyi\Snowflake\Snowflake;
use Hidehalo\Nanoid\Client;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Notifications\NotificationServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use NbsPhp\Core\Commands\KeyGenerateCommand;
use NbsPhp\Core\Commands\ReloadUserPermissionCommand;
use NbsPhp\Core\Commands\VendorPublishCommand;
use NbsPhp\Core\Response\JsonResponseMapper;
use NbsPhp\Core\Response\ResponseMapperInterface;
use Spatie\Fractal\FractalServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @param Filesystem $filesystem
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(Filesystem $filesystem)
    {
        if ($this->app->runningInConsole()) {
            $this->bootPublishing($filesystem);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerProviders();
        $this->registerSingletons();
        $this->registerBindings();
        $this->registerMiddleware();
        $this->registerCommands();
        $this->registerViews();
        $this->registerTranslations();
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                KeyGenerateCommand::class,
                VendorPublishCommand::class,
                ReloadUserPermissionCommand::class,
            ]);
        }
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__ . '../../resources/views', 'core');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = $this->app->resourcePath('lang/vendor/core');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'core');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'core');
        }
    }

    protected function registerMiddleware()
    {
        $this->app->middleware([
            \NbsPhp\Core\Middleware\RequestIdMiddleware::class,
            \NbsPhp\Core\Middleware\TrustProxies::class,
            \NbsPhp\Core\Middleware\ForceUpdateAppMiddleware::class,
//            \NbsPhp\Core\Middleware\HttpLoggerMiddleware::class //uncomment if track all endpoint
        ]);
        $this->app->routeMiddleware([
            'auth' => \NbsPhp\Core\Middleware\AuthenticateMiddleware::class,
            'user-auth' => \NbsPhp\Core\Middleware\AuthenticateMiddleware::class,
            'basic-auth-config' => \NbsPhp\Core\Middleware\BasicAuthConfigMiddleware::class,
            'can' => \NbsPhp\Core\Middleware\AuthorizationMiddleware::class,
            'horizonBasicAuth' => \NbsPhp\Core\Middleware\HorizonBasicAuthMiddleware::class,
            'http-logger' => \NbsPhp\Core\Middleware\HttpLoggerMiddleware::class,
            'callback' => \NbsPhp\Core\Middleware\CallbackMiddleware::class,
            'throttle' => \NbsPhp\Core\Middleware\ThrottleRequestsMiddleware::class,
        ]);
    }

    protected function registerBindings()
    {
        $this->app->bind(ResponseMapperInterface::class, JsonResponseMapper::class);
    }

    protected function registerProviders()
    {
        $this->app->register(TrustedProxyServiceProvider::class);
        $this->app->register(GuzzleLoggerServiceProvider::class);
        $this->app->register(MaintenanceModeServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(JWTAuthServiceProvider::class);
        $this->app->register(FractalServiceProvider::class);
        $this->app->register(PasswordResetServiceProvider::class);
        $this->app->register(NotificationServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        if (!class_exists('Datatables')) {
            class_alias('Yajra\DataTables\DataTables', 'Datatables');
        }
        $this->app->configure('datatables');
        $this->app->register('Yajra\DataTables\DataTablesServiceProvider');
        // $this->app->register(\PragmaRX\Health\ServiceProvider::class);
    }

    protected function registerSingletons()
    {
        $this->app->singleton('nanoid', function () {
            return new Client();
        });

        $this->app->singleton('snowflake', function () {
            //TODO LOAD FROM CONFIG datacenter, worker id, start timestamp, and sequence resolver
            return (new Snowflake())
                ->setStartTimeStamp(strtotime('2019-08-08 08:08:08') * 1000)
                ->setSequenceResolver(new LaravelSequenceResolver($this->app->get('cache')->store()));
        });
    }

    protected function bootPublishing(Filesystem $filesystem): void
    {
        $this->publishes([
            __DIR__ . '/../../config' => $this->app->configPath(),
        ], ['config']);

        $this->publishes([
            __DIR__ . '/../../resources/lang' => $this->app->resourcePath('lang/vendor/auth'),
        ], 'auth-lang');

        $this->publishes([
            __DIR__ . '/../resources/views/mail' => resource_path('views/vendor/mail'),
        ], 'auth-email');

        $this->publishes([
            __DIR__ . '/../resources/views/mail' => resource_path('views/vendor/core'),
        ], 'core');

        //TODO PUBLISH VIEW

        $this->publishes([
            __DIR__ . '/../../database/migrations/create_entity_type_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_entity_type_table'),
            __DIR__ . '/../../database/migrations/create_auth_tables.php.stub' => $this->getMigrationFileName($filesystem, 'create_auth_tables.php'),
            __DIR__ . '/../../database/migrations/create_device_platform_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_device_platform_table'),
            __DIR__ . '/../../database/migrations/create_auth_provider_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_auth_provider_table'),
            __DIR__ . '/../../database/migrations/create_user_session_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_user_session_table'),
            __DIR__ . '/../../database/migrations/create_user_metadata_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_user_metadata_table'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../../database/seeds/AuthSeeder.php' => database_path('seeds/AuthSeeder.php'),
        ], 'seeds');
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param Filesystem $filesystem
     *
     * @return string
     */
    protected function getMigrationFileName(Filesystem $filesystem, $filename)
    {
        sleep(1);
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $filename) {
                return $filesystem->glob($path . "*_{$filename}.php");
            })
            ->push($this->app->databasePath("/migrations/{$timestamp}_{$filename}.php"))
            ->first();
    }
}
