<?php

namespace Sanf\Integration\Modules\SanfCore;

use GuzzleHttp\Client;
use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Integration\Entities\SanfCoreSparePartDisbursementEntity;
use Sanf\Integration\Responses\SanfCoreV2ListResponse;

class SanfCoreApiClientV2
{
    public const DEFAULT_SKIP = 0;
    public const DEFAULT_LIMIT = 2147483647;
    public const DEFAULT_ORDER = 'Latest';

    protected $client;

    public function __construct()
    {
        $verifyOnProduction = config('app.env') === 'production';

        $this->client = new Client([
            'verify' => $verifyOnProduction,
        ]);
    }

    // Spare Part Disbursement / Spare Part Financing

    public function getSparePartDisbursementList(
        $page,
        $per_page
    ) {
        $response = Request::route('spare-part-disbursement.list', $this->client)
            ->queryParams([
                    'page' => $page,
                    'per_page' => $per_page,
                ])
            ->send();

        $jsonResponse = $response->json();

        $jsonResponse['data'] = array_map(
            fn ($item) => new SanfCoreSparePartDisbursementEntity($item),
            $jsonResponse['data']
        );

        return new SanfCoreV2ListResponse($jsonResponse);
    }
}
