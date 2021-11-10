<?php


namespace Sanf\Core\Modules\User\Services;


use Illuminate\Support\Facades\Log;
use Sanf\Integration\Exceptions\SanfInternalApiException;
use Sanf\Integration\InternalApiClient;
use function collect;

class GetListShareholderService
{

    protected InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {

        $this->client = $client;
    }

    public function execute($dto)
    {
        try {
            $response = $this->client->getShareholders($dto->xid);
        } catch (SanfInternalApiException $exception) {
            Log::info('shareholder: shareholder data notfound for xid ' . $dto->xid);
            return [];
        }

        return collect($response['data'])
            ->map(function ($item) {
                return (object)[
                    "no" => $item['SR_NO'] ?? '',
                    "title" => ucwords(strtolower($item['CUST_TITLE'] ?? '')),
                    "name" => ucwords(strtolower($item['CUST_NAME'] ?? '')),
                    "share_percentage" => $item['PERC_SHARE'] ?? '',
                    "position" => $item['JABATAN'] ?? '',
                    'type' => $item['F_PC'] ?? '',
                ];
            });
    }
}
