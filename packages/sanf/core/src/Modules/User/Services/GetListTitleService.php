<?php

namespace Sanf\Core\Modules\User\Services;

use function collect;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetListTitleService
{
    protected $integrationListTitle;

    public function __construct(SanfCoreApiClient $integrationListTitle)
    {
        $this->integrationListTitle = $integrationListTitle;
    }

    public function execute($dto)
    {
        $response = $this->integrationListTitle->getTitle($dto->type);

        return collect($response['data'])
            ->map(function ($item) {
                return (object) [
                    'id' => $item['ID'] ?? '',
                    'name' => $item['CUST_TITLE'] ?? '',
                ];
            });
    }
}
