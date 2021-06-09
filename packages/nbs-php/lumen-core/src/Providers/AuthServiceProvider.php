<?php


namespace NbsPhp\Core\Providers;


use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRoutes();
    }
    /**
     * Register routes.
     */
    private function registerRoutes()
    {
        collect(($routes = $this->getRoutes()))->each(function ($route) {
            $this->registerRoute($route);
        });
    }
    /**
     * Get the list of routes.
     *
     * @return array
     */
    private function getRoutes()
    {
        return config('auth.routes.list');
    }
    /**
     * @param $route
     * @param null $name
     */
    protected function registerRoute($route, $name = null)
    {
        $attributes = [
            'middleware' => isset($route['middleware']) ? $route['middleware'] : [],
        ];

        $this->getRouter()->group($attributes, function () use ($route, $name) {
            $action = isset($route['controller'])
                ? "{$route['controller']}@{$route['action']}"
                : $route['action'];

            $this->getRouter()->{$route['method']}($route['uri'], [
                'as' => $name ?: $route['name'],
                'uses' => $action,
            ]);
        });
    }

    /**
     * Get the current router.
     *
     * @return mixed
     */
    protected function getRouter()
    {
        return app()->router;
    }
}
