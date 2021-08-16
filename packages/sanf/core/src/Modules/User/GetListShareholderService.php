<?php


namespace Sanf\Core\Modules\User;


use Sanf\Integration\InternalApiClient;

class GetListShareholderService
{

    protected InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {

        $this->client = $client;
    }

    public function execute($dto)
    {
        $response = $this->client->getShareholders($dto->xid);

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