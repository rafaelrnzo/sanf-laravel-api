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
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductStatusEnum;
use Sanf\Core\Modules\Scanina\Services\GuzzleBrowseProductRentService;

class BrowseProductRentController extends RestApiController
{
    public function __invoke(Request $request, Guard $userAuth, GuzzleBrowseProductRentService $service)
    {
        $queryParam = $this->validate($request, [
            'skip' => 'nullable|integer|max:2147483647',
            'limit' => 'nullable|integer|max:2147483647',
            'sort_by' => 'nullable|string|in:oldest,latest',
            'keyword' => 'nullable|string|max:255',
            'location_id' => 'nullable|string',
            'category_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
            'model_id' => 'nullable|integer',
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
            'merchant_id' => 'nullable|integer',
        ]);

        $productRentRequestDto = new BrowseProductRentRequestDto(
            $queryParam + [
                'user_id' => $userAuth->id(),
            ]
        );

        $result = $service->execute($productRentRequestDto);

        return fractal($result->data, BrowseProductRentResponseTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
