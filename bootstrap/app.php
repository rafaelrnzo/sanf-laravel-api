<?php

use Illuminate\Routing\Redirector;
use NbsPhp\Core\Exceptions\Handler;
use NbsPhp\Core\Response\JsonResponseMapper;
use NbsPhp\Core\Response\ResponseMapperInterface;

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/
$app->bind(ResponseMapperInterface::class, JsonResponseMapper::class);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration driectory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/
// TODO MOVE TO CORE
$app->configure('app');
$app->configure('session');
$app->configure('jwt');
$app->configure('auth');
$app->configure('database');
$app->configure('errors');
$app->configure('filesystems');
$app->configure('services');
$app->configure('mail');
$app->configure('response-codes');
$app->configure('fcm');
$app->configure('notifications');
$app->configure('http-logger');
$app->configure('guzzle-logger');
$app->configure('encryption');
$app->configure('additional');
$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);
$app->configure('tinker');
$app->configure('payment');
$app->configure('midtrans');
/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    \Illuminate\Session\Middleware\StartSession::class,
]);

$app->singleton(Illuminate\Session\SessionManager::class, function () use ($app) {
    return $app->loadComponent('session', Illuminate\Session\SessionServiceProvider::class, 'session');
});

$app->singleton('session.store', function () use ($app) {
    return $app->loadComponent('session', Illuminate\Session\SessionServiceProvider::class, 'session.store');
});

$app->singleton('redirectSession', function ($app) {
    $redirector = new \NbsPhp\Core\SessionRedirector($app);

    // If the session is set on the application instance, we'll inject it into
    // the redirector instance. This allows the redirect responses to allow
    // for the quite convenient "with" methods that flash to the session.
    if (isset($app['session.store'])) {
        $redirector->setSession($app['session.store']);
    }

    return $redirector;
});

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/
$app->register(\Sentry\Laravel\ServiceProvider::class);
// To enable Sentry Performance Monitoring, the `TracingServiceProvider` has to be registered additionally:
// $app->register(Sentry\Laravel\Tracing\ServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(Illuminate\Mail\MailServiceProvider::class);
$app->register(NbsPhp\Core\Providers\CoreServiceProvider::class);
$app->register(NbsPhp\Notification\NotificationServiceProvider::class);
$app->register(Sanf\Integration\IntegrationServiceProvider::class);
$app->register(Sanf\Core\Providers\CoreServiceProvider::class);
$app->register(Sanf\Console\Providers\ConsoleServiceProvider::class);
$app->register(Sanf\Api\Providers\ApiServiceProvider::class);
$app->register(Sanf\Web\Providers\WebServiceProvider::class);
$app->register(Sanf\Dashboard\Providers\DashboardServiceProvider::class);
$app->register(\Sanf\External\ExternalServiceProvider::class);
$app->register(\Laravel\Tinker\TinkerServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
