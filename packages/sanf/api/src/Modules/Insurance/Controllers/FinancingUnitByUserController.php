<?php

namespace Sanf\Api\Modules\Insurance\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Insurance\Transformers\FinancingUnitV2Transformer;
use Sanf\Core\Modules\Insurance\Services\BrowseAvailableFinancingUnitByUserV2Service;
use Sanf\Core\Modules\Invoice\Dtos\BrowseFinancingUnitByUserV2RequestDto;

final class FinancingUnitByUserController extends RestApiController
{
    /**
     * @since CR2025 uses V2 that adds city in response and discard timestamp request parameter
     */
    public function getBrowse(Guard $auth, Request $request, $xid, BrowseAvailableFinancingUnitByUserV2Service $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new BrowseFinancingUnitByUserV2RequestDto($input + [
                'profileXid' => $xid,
                'userId' => $auth->id(),
            ]);
        $result = $service->execute($dto);

        return fractal($result->data, new FinancingUnitV2Transformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
