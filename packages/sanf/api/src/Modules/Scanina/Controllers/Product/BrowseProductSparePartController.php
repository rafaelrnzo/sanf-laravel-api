<?php

namespace Sanf\Api\Modules\Scanina\Controllers\Product;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Scanina\Transformers\BrowseProductSparePartResponseTransformer;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSparePartRequestDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductSortByEnum;
use Sanf\Core\Modules\Scanina\Services\GuzzleBrowseProductSparePartService;

class BrowseProductSparePartController extends RestApiController
{
    public function __invoke(Request $request, Guard $userAuth, GuzzleBrowseProductSparePartService $service)
    {
        $queryParam = $this->validate($request, [
            'skip' => 'nullable|integer|max:2147483647',
            'limit' => 'nullable|integer|max:2147483647',
            'sort_by' => ['nullable', 'string', Rule::in(ScaninaProductSortByEnum::ALL)],
            'keyword' => 'nullable|string|max:255',
            'category_id' => 'nullable|string',
            'brand_id' => 'nullable|string',
            'rating' => 'nullable|int|in:1,2,3,4,5',
            'min_price' => 'nullable|numeric|max:999999999999999.9999',
            'max_price' => 'nullable|numeric|max:999999999999999.9999',
            'merchant_id' => 'nullable|string',
        ]);

        $productSparePartRequestDto = new BrowseProductSparePartRequestDto(
            $queryParam + [
                'user_id' => $userAuth->id(),
            ]
        );
        $productSparePartRequestDto->categoryXid = $queryParam['category_id'] ?? null;
        $productSparePartRequestDto->brandXid = $queryParam['brand_id'] ?? null;
        $productSparePartRequestDto->merchantXid = $queryParam['merchant_id'] ?? null;

        $result = $service->execute($productSparePartRequestDto);

        return fractal($result->data, BrowseProductSparePartResponseTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
