<?php

namespace NbsPhp\Core\Controllers;

use Illuminate\Support\Facades\File;
use Laravel\Lumen\Routing\Controller;
// use PragmaRX\Health\Service;

class HealthCheckController extends Controller
{
    /**
     * @var Service
     */
    // private $healthService;

    /**
     * Health constructor.
     *
     * @param Service $healthService
     */
    public function __construct(/* Service $healthService */)
    {
        // $this->healthService = $healthService;
    }

    /**
     * Check all resources.
     *
     * @return array
     * @throws \Exception
     */
    public function check()
    {
        // $this->healthService->setAction('check');
        // return response($this->healthService->health());

        return response(['status' => 'ok']);
    }

    public function checkSimplified()
    {
        // $results = collect($this->healthService->health())->reduce(function ($current, $resource) {
        //     $current[$resource->abbreviation] = $resource->isHealthy();
        //     return $current;
        // });
        // $status = 200;
        // foreach ($results ?? [] as $key => $value) {
        //     if ($value === false) {
        //         $status = 500;
        //         break;
        //     }
        // }
        // return response()->json($results, $status);

        return response()->json(['status' => true], 200);
    }

    /**
     * Check and get one resource.
     *
     * @param $slug
     * @return mixed
     * @throws \Exception
     */
    public function getResource($slug)
    {
        // $this->healthService->setAction('resource');
        // return $this->healthService->resource($slug);

        return response(['status' => 'ok']);
    }

    /**
     * Get all resources.
     *
     * @return mixed
     * @throws \Exception
     */
    public function allResources()
    {
        // return $this->healthService->getResources();

        return response(['status' => 'ok']);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function string()
    {
        // $this->healthService->setAction('string');
        // return response(
        //     $this->healthService->string()
        // );

        return response('ok');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function panel()
    {
        // $this->healthService->setAction('panel');
        // return response((string) view(config('health.views.panel'))->with('laravel', ['health' => config('health')]));

        return response('panel ok');
    }

    public function assetAppJs()
    {
        // $file = File::get(config('health.assets.js'));
        // $response = response()->make($file);
        // $response->header('Content-Type', 'text/javascript');
        // return $response;

        return response()->make('// js ok')->header('Content-Type', 'text/javascript');
    }

    public function assetAppCss()
    {
        // $file = File::get(config('health.assets.css'));
        // $response = response()->make($file);
        // $response->header('Content-Type', 'text/css');
        // return $response;

        return response()->make('/* css ok */')->header('Content-Type', 'text/css');
    }

    public function config()
    {
        // return config('health');

        return [];
    }
}
