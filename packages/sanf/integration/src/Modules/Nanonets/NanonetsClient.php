<?php

namespace Sanf\Integration\Modules\Nanonets;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use NbsPhp\ApiWrapper\Api\Request;

class NanonetsClient
{
    public const DEFAULT_SKIP = 0;
    public const DEFAULT_LIMIT = 2147483647;
    public const DEFAULT_ORDER = 'Latest';

    protected $client;

    public function __construct()
    {
        $this->client = app(Client::class);
    }

    public function scanDocument(UploadedFile $file)
    {
        $response = Request::route('document.scan', $this->client)
            ->pathParams([
                'uuid' => config('nanonets-api.uuid.scan'),
            ])
            // ->multipart([
            //     [
            //         'name' => 'file',
            //         'contents' => $file,
            //     ],
            // ])
            ->send();

        return $response->json(false);
    }
}
