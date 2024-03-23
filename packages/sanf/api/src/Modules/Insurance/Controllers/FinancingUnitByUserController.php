<?php

namespace Sanf\Api\Modules\Insurance\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Insurance\Transformers\FinancingUnitTransformer;
use Sanf\Core\Modules\Insurance\Services\BrowseAvailableFinancingUnitByUserService;
use Sanf\Core\Modules\Invoice\Dtos\BrowseFinancingUnitByUserRequestDto;

final class FinancingUnitByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, $xid, BrowseAvailableFinancingUnitByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new BrowseFinancingUnitByUserRequestDto($input + [
                'profileXid' => $xid,
                'userId' => $auth->id(),
            ]);
        $result = $service->execute($dto);

        return fractal($result->data, new FinancingUnitTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
