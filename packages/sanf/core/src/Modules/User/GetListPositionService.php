<?php

namespace Sanf\Core\Modules\User;

use Sanf\Integration\InternalApiClient;

class GetListPositionService
{

    protected $integrationListPosition;

    public function __construct(InternalApiClient $integrationListPosition)
    {
        $this->listPosition = $integrationListPosition;
    }

    public function execute()
    {
        $response = $this->listPosition->getPosition();

        return collect($response['data'])
            ->map(function ($item) {
                return (object)[
                    "id" => $item['ID'] ?? '',
                    "name" => $item['DESCRIPTION'] ?? '',
                ];
            });
    }
}