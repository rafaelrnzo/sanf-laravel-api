<?php

namespace Sanf\Api\Modules\Scanina\Controllers\Product;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Scanina\Transformers\BrowseProductRentResponseTransformer;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentRequestDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductConditionEnum;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductSortByEnum;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductStatusEnum;
use Sanf\Core\Modules\Scanina\Services\GuzzleBrowseProductRentService;

class BrowseProductRentController extends RestApiController
{
    public function __invoke(Request $request, Guard $userAuth, GuzzleBrowseProductRentService $service)
    {
        $queryParam = $this->validate($request, [
            'skip' => 'nullable|integer|max:2147483647',
            'limit' => 'nullable|integer|max:2147483647',
            'sort_by' => ['nullable', 'string', Rule::in(ScaninaProductSortByEnum::ALL)],
            'keyword' => 'nullable|string|max:255',
            'location_id' => 'nullable|string',
            'category_id' => 'nullable|string',
            'brand_id' => 'nullable|string',
            'model_id' => 'nullable|string',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'has_assurance' => 'nullable|boolean',
            'rating' => 'nullable|int|in:1,2,3,4,5',
            'is_scan_qualified' => 'nullable|boolean',
            'status' => ['nullable', 'integer', Rule::in(ScaninaProductStatusEnum::ALL)],
            'min_price' => 'nullable|numeric|max:999999999999999.9999',
            'max_price' => 'nullable|numeric|max:999999999999999.9999',
            'min_hour_meter' => 'nullable|integer',
            'condition' => ['nullable', 'integer', Rule::in(ScaninaProductConditionEnum::ALL)],
            'merchant_id' => 'nullable|string',
        ]);

        $productRentRequestDto = new BrowseProductRentRequestDto(
            $queryParam + [
                'user_id' => $userAuth->id(),
            ]
        );
        $productRentRequestDto->locationXid = $queryParam['location_id'] ?? null;
        $productRentRequestDto->categoryXid = $queryParam['category_id'] ?? null;
        $productRentRequestDto->brandXid = $queryParam['brand_id'] ?? null;
        $productRentRequestDto->merchantXid = $queryParam['merchant_id'] ?? null;

        $result = $service->execute($productRentRequestDto);

        return fractal($result->data, BrowseProductRentResponseTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
