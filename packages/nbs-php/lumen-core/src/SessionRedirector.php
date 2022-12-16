<?php

namespace NbsPhp\Core;

use Illuminate\Http\RedirectResponse;
use Illuminate\Session\Store as SessionStore;
use Laravel\Lumen\Application;

class SessionRedirector
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The session store instance.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Create a new redirector instance.
     *
     * @param  Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Create a new redirect response to the given path.
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @param  bool  $secure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function to($path, $status = 302, $headers = [], $secure = null)
    {
        $path = $this->app->make('url')->to($path, [], $secure);

        return $this->createRedirect($path, $status, $headers);
    }

    /**
     * Create a new redirect response to a named route.
     *
     * @param  string  $route
     * @param  array  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function route($route, $parameters = [], $status = 302, $headers = [])
    {
        $path = $this->app->make('url')->route($route, $parameters);

        return $this->to($path, $status, $headers);
    }

    /**
     * Create a new redirect response.
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function createRedirect($path, $status, $headers)
    {
        return tap(new RedirectResponse($path, $status, $headers), function ($redirect) {
            if (isset($this->session)) {
                $redirect->setSession($this->session);
            }

            $redirect->setRequest($this->app->make('request'));
        });
    }

    /**
     * Set the active session store.
     *
     * @param  \Illuminate\Session\Store  $session
     * @return void
     */
    public function setSession(SessionStore $session)
    {
        $this->session = $session;
    }
}
