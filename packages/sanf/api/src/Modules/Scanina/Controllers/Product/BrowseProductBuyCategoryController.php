<?php

namespace Sanf\Api\Modules\Scanina\Controllers\Product;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Scanina\Transformers\BrowseProductFilterResponseTransformer;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductFilterRequestDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Services\GuzzleBrowseProductFilterCategoryService;

class BrowseProductBuyCategoryController extends RestApiController
{
    public function __invoke(Request $request, Guard $userAuth, GuzzleBrowseProductFilterCategoryService $service)
    {
        $queryParam = $this->validate($request, [
            'skip' => 'nullable|integer|max:2147483647',
            'limit' => 'nullable|integer|max:2147483647',
            'sort_by' => 'nullable|string|in:oldest,latest,sort',
            'keyword' => 'nullable|string|max:255',
            'is_only_top_category' => 'nullable|bool'
        ]);

        $productFilterCategoryRequestDto = new BrowseProductFilterRequestDto(
            $queryParam + [
                'user_id' => $userAuth->id(),
                'type' => ScaninaProductTypeEnum::BUY,
            ]
        );

        $result = $service->execute($productFilterCategoryRequestDto);

        return fractal($result->data, BrowseProductFilterResponseTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
