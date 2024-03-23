<?php

namespace Sanf\Api\Modules\Scanina\Controllers\Region;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Scanina\Transformers\BrowseRegionResponseTransformer;
use Sanf\Core\Modules\Scanina\Services\GuzzleBrowseCityService;

class BrowseCityController extends RestApiController
{
    public function __invoke(Request $request, Guard $userAuth, GuzzleBrowseCityService $service)
    {
        $queryParam = $this->validate($request, [
            'skip' => 'nullable|integer|max:2147483647',
            'limit' => 'nullable|integer|max:2147483647',
            'sort_by' => 'nullable|string|in:oldest,latest',
            'keyword' => 'nullable|string|max:255',
        ]);

        $dto = (object) [
            'skip' => $queryParam['skip'] ?? 0,
            'limit' => $queryParam['limit'] ?? 10,
            'sortBy' => $queryParam['sort_by'] ?? 'latest',
            'keyword' => $queryParam['keyword'] ?? null,
        ];

        $result = $service->execute($dto);

        return fractal($result->data, BrowseRegionResponseTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
