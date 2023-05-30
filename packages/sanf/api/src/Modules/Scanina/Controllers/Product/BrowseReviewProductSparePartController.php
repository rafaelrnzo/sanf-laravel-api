<?php

namespace Sanf\Api\Modules\Scanina\Controllers\Product;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Scanina\Transformers\BrowseProductReviewResponseTransformer;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductFilterRequestDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Services\GuzzleBrowseProductReviewService;

class BrowseReviewProductSparePartController extends RestApiController
{
    public function __invoke(
        Request $request,
        string $xid,
        Guard $userAuth,
        GuzzleBrowseProductReviewService $service
    ) {
        $queryParam = $this->validate($request, [
            'skip' => 'nullable|integer|max:2147483647',
            'limit' => 'nullable|integer|max:2147483647',
            'sort_by' => 'nullable|string|in:oldest,latest',
            'keyword' => 'nullable|string|max:255',
        ]);

        $productFilterCategoryRequestDto = new BrowseProductFilterRequestDto(
            $queryParam + [
                'user_id' => $userAuth->id(),
                'type' => ScaninaProductTypeEnum::SPARE_PART,
                'product_xid' => $xid,
            ]
        );

        $result = $service->execute($productFilterCategoryRequestDto);

        return fractal($result->data, BrowseProductReviewResponseTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
