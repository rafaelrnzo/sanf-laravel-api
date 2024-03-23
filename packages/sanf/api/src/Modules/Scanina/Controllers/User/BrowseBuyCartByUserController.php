<?php

namespace Sanf\Api\Modules\Scanina\Controllers\User;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Scanina\Transformers\BrowseProductBuyResponseTransformer;
use Sanf\Core\Modules\Scanina\Services\GuzzleBrowseProductBuyCartService;

class BrowseBuyCartByUserController extends RestApiController
{
    public function __invoke(
        string $xid,
        Request $request,
        Guard $userAuth,
        GuzzleBrowseProductBuyCartService $service
    ) {
        $queryParam = $this->validate($request, [
            'skip' => 'nullable|integer|max:2147483647',
            'limit' => 'nullable|integer|max:2147483647',
            'sort_by' => 'nullable|string|in:oldest,latest',
            'keyword' => 'nullable|string|max:255',
        ]);

        $dto = (object) [
            'userId' => $userAuth->id(),
            'profileXid' => $xid,
            'skip' => $queryParam['skip'] ?? 0,
            'limit' => $queryParam['limit'] ?? 10,
            'sortBy' => $queryParam['sort_by'] ?? 'latest',
            'keyword' => $queryParam['keyword'] ?? null,
            'timestamp' => $queryParam['timestamp'] ?? null,
        ];

        $result = $service->execute($dto);

        return fractal($result->data, BrowseProductBuyResponseTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
