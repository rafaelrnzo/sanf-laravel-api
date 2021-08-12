<?php

namespace Sanf\Core\Modules\User;

use Sanf\Integration\InternalApiClient;

class GetListTitleService
{

    protected $integrationListTitle;

    public function __construct(InternalApiClient $integrationListTitle)
    {
        $this->integrationListTitle = $integrationListTitle;
    }

    public function execute($dto)
    {
        $response = $this->integrationListTitle->getTitle($dto->type);

        return collect($response['data'])
            ->map(function ($item) {
                return (object)[
                    "id" => $item['ID'] ?? '',
                    "name" => $item['CUST_TITLE'] ?? '',
                ];
            });
    }
}