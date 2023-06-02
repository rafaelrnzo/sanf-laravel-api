<?php

namespace Sanf\Api\Modules\Scanina\Controllers\Product;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Scanina\Transformers\BrowseProductBuyResponseTransformer;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyRequestDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductConditionEnum;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductSortByEnum;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductStatusEnum;
use Sanf\Core\Modules\Scanina\Services\GuzzleBrowseProductBuyService;

class BrowseProductBuyController extends RestApiController
{
    public function __invoke(Request $request, Guard $userAuth, GuzzleBrowseProductBuyService $service)
    {
        $queryParam = $this->validate($request, [
            'skip' => 'nullable|integer|max:2147483647',
            'limit' => 'nullable|integer|max:2147483647',
            'sort_by' => ['nullable', 'string', Rule::in(ScaninaProductSortByEnum::ALL)],
            'keyword' => 'nullable|string|max:255',
            'location_id' => 'nullable|string',
            'category_id' => 'nullable|string',
            'brand_id' => 'nullable|string',
            'type_id' => 'nullable|string',
            'model_id' => 'nullable|string',
            'min_year' => 'nullable|integer|max:2147483647',
            'max_year' => 'nullable|integer|max:2147483647',
            'has_assurance' => 'nullable|boolean',
            'rating' => 'nullable|int|in:1,2,3,4,5',
            'is_scan_qualified' => 'nullable|boolean',
            'status' => ['nullable', 'integer', Rule::in(ScaninaProductStatusEnum::ALL)],
            'min_price' => 'nullable|numeric|max:999999999999999.9999',
            'max_price' => 'nullable|numeric|max:999999999999999.9999',
            'min_hour_meter' => 'nullable|integer',
            'max_hour_meter' => 'nullable|integer',
            'condition' => ['nullable', 'integer', Rule::in(ScaninaProductConditionEnum::ALL)],
            'merchant_id' => 'nullable|string',
        ]);

        $productBuyRequestDto = new BrowseProductBuyRequestDto(
            $queryParam + [
                'user_id' => $userAuth->id(),
            ]
        );
        $productBuyRequestDto->locationXid = $queryParam['location_id'] ?? null;
        $productBuyRequestDto->categoryXid = $queryParam['category_id'] ?? null;
        $productBuyRequestDto->brandXid = $queryParam['brand_id'] ?? null;
        $productBuyRequestDto->typeXid = $queryParam['type_id'] ?? null;
        $productBuyRequestDto->modelXid = $queryParam['model_id'] ?? null;
        $productBuyRequestDto->merchantXid = $queryParam['merchant_id'] ?? null;

        $result = $service->execute($productBuyRequestDto);

        return fractal($result->data, BrowseProductBuyResponseTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
