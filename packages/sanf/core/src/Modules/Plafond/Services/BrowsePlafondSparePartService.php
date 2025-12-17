<?php

namespace Sanf\Core\Modules\Plafond\Services;

use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondSparePartRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondSparePartResponseDto;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class BrowsePlafondSparePartService
{
    protected SanfCoreApiClientV2 $sanfCoreApiClient;

    public function __construct(
        SanfCoreApiClientV2 $sanfCoreApiClient
    ) {
        $this->sanfCoreApiClient = $sanfCoreApiClient;
    }

    public function execute(BrowsePlafondSparePartRequestDto $dto): BrowsePlafondSparePartResponseDto
    {
        $skip = max(0, (int) ($dto->skip ?? 0));
        $perPage = max(1, (int) ($dto->limit ?? SanfCoreApiClientV2::DEFAULT_LIMIT));
        $page = (int) floor($skip / $perPage) + 1;

        $response = $this->sanfCoreApiClient->getPlafondSparePartList($page, $perPage);
        $data = $response->data;

        return new BrowsePlafondSparePartResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => optional($response->meta)->total ?? count($data),
                'count' => count($data),
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sortBy' => $dto->sortBy,
            ],
        ]);
    }
}
