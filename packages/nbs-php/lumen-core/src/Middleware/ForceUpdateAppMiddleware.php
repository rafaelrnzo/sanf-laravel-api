<?php

namespace NbsPhp\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class ForceUpdateAppMiddleware
{
    protected $request;
    protected $agent;

    public function __construct(Request $request, Agent $agent)
    {
        $this->agent = $agent;
        $this->request = $request;
    }

    /**
     * Handle incoming requests.
     *
     * @param Request $request
     * @param \Closure $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \InvalidArgumentException
     */
    public function handle($request, Closure $next)
    {
        //TODO THROW EXCEPTION
        if ($this->isBelowVersionRequirement()) {
            return response()->json([
                'success' => false,
                'code' => 'APP001',
                'message' => 'Please Update Application',
                'timestamp' => date('Y-m-d H:i:s'),
            ], 400);
        }

        return $next($request);
    }

    //TODO CREATE SEPERATE SERVICE
    public function isBelowVersionRequirement(): bool
    {
        if ($this->agent->isAndroidOS()
            && config('client-versions.android.minimum_version_number') > $this->request->header('X-App-Version-Number')
        ) {
            return true;
        } elseif ($this->agent->isiOS()
            && config('client-versions.ios.minimum_version_number') > $this->request->header('X-App-Version-Number')
        ) {
            return true;
        }

        return false;
    }
}
