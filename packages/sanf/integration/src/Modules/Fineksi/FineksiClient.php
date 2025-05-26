<?php

namespace Sanf\Integration\Modules\Fineksi;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use NbsPhp\ApiWrapper\Api\Request;

class FineksiClient
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
        $base64File = base64_encode(file_get_contents($file->getPathname()));

        $response = Request::route('document.scan.fineksi', $this->client)
             ->json([
                'filedata' => $base64File
             ])
            ->send();

        return $response->json(false);
    }
}
