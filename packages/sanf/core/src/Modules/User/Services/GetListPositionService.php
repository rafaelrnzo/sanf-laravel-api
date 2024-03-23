<?php

namespace Sanf\Core\Modules\User\Services;

use function collect;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetListPositionService
{
    protected $integrationListPosition;

    public function __construct(SanfCoreApiClient $integrationListPosition)
    {
        $this->listPosition = $integrationListPosition;
    }

    public function execute()
    {
        $response = $this->listPosition->getPosition();

        return collect($response['data'])
            ->map(function ($item) {
                return (object) [
                    'id' => $item['ID'] ?? '',
                    'name' => $item['DESCRIPTION'] ?? '',
                ];
            });
    }
}
