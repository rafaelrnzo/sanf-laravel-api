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
use NbsPhp\Core\Commands\ReloadUserPermissionCommand;
use NbsPhp\Core\Commands\KeyGenerateCommand;
use NbsPhp\Core\Commands\VendorPublishCommand;
use NbsPhp\Core\Repositories\ProfileRepositoryInterface;
use NbsPhp\Core\Response\ResponseMapperInterface;
use NbsPhp\Core\Response\RestResponseMapper;
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

    protected function registerMiddleware()
    {
        $this->app->middleware([
            \NbsPhp\Core\Middleware\TrustProxies::class,
            \NbsPhp\Core\Middleware\ForceUpdateAppMiddleware::class
        ]);
        $this->app->routeMiddleware([
            'auth' => \NbsPhp\Core\Middleware\AuthenticateMiddleware::class,
            'horizonBasicAuth' => \NbsPhp\Core\Middleware\HorizonBasicAuthMiddleware::class,
            'basicClient' => \NbsPhp\Core\Middleware\BasicClientAuthMiddleware::class,
        ]);
    }

    protected function registerBindings()
    {
        $this->app->bind(ResponseMapperInterface::class, RestResponseMapper::class);
        $this->app->bind(ProfileRepositoryInterface::class, config('auth.profile_repository'));
    }

    protected function registerProviders()
    {
        $this->app->register(TrustedProxyServiceProvider::class);
        $this->app->register(MaintenanceModeServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(JWTAuthServiceProvider::class);
        $this->app->register(FractalServiceProvider::class);
        $this->app->register(PasswordResetServiceProvider::class);
        $this->app->register(NotificationServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        class_alias('Yajra\DataTables\DataTables', 'Datatables');
        $this->app->configure('datatables');
        $this->app->register('Yajra\DataTables\DataTablesServiceProvider');
        $this->app->register(\PragmaRX\Health\ServiceProvider::class);
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
            __DIR__.'/../resources/views/mail' => resource_path('views/vendor/mail'),
        ], 'auth-email');

        //TODO PUBLISH VIEW

        $this->publishes([
            __DIR__ . '/../../database/migrations/create_entity_type_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_entity_type_table'),
            __DIR__ . '/../../database/migrations/create_auth_tables.php.stub' => $this->getMigrationFileName($filesystem, 'create_auth_tables.php'),
            __DIR__ . '/../../database/migrations/create_device_platform_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_device_platform_table'),
            __DIR__ . '/../../database/migrations/create_auth_provider_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_auth_provider_table'),
            __DIR__ . '/../../database/migrations/create_user_session_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_user_session_table'),
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
