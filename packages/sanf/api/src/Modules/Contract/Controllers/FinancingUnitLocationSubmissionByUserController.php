<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\MyFinancingUnitLocationSubmissionSimpleTransformer;
use Sanf\Api\Modules\Contract\Transformers\MyFinancingUnitLocationSubmissionTransformer;
use Sanf\Core\Modules\Contract\Dtos\AddFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Dtos\BrowseFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Dtos\ReadFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Services\AddFinancingUnitLocationSubmissionByUserService;
use Sanf\Core\Modules\Contract\Services\BrowseFinancingUnitLocationSubmissionByUserService;
use Sanf\Core\Modules\Contract\Services\ReadFinancingUnitLocationSubmissionByUserService;

final class FinancingUnitLocationSubmissionByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, BrowseFinancingUnitLocationSubmissionByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
            'timestamp' => ['nullable', 'integer'],
        ]);
        $dto = new BrowseFinancingUnitLocationSubmissionByUserRequestDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result->data, new MyFinancingUnitLocationSubmissionSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getRead(Guard $auth, $xid, ReadFinancingUnitLocationSubmissionByUserService $service)
    {
        $dto = new ReadFinancingUnitLocationSubmissionByUserRequestDto([
            'xid' => $xid,
            'userId' => $auth->id()
        ]);
        $result = $service->execute($dto);
        return fractal($result, new MyFinancingUnitLocationSubmissionTransformer());
    }

    public function postAdd(Guard $auth, Request $request, AddFinancingUnitLocationSubmissionByUserService $service)
    {
        $input = $this->validate($request, [
//            'email' => ['required', 'email', 'max:255'],
//            'title' => ['required', 'string', 'max:255'],
//            'description' => ['nullable', 'string', 'max:65535'],
//            'total' => ['nullable', 'integer', 'max:2147483647'],
//            'price' => ['nullable', 'numeric', 'max:999999999999999.9999'],
//            'is_enabled' => ['nullable', 'boolean'],
//            'images' => ['nullable', 'array'],
//            'timestamp' => ['nullable', 'integer', 'max:99999999999'],
//            'date' => ['required', 'string', 'date_format:Y-m-d'],
        ]);
        $dto = new AddFinancingUnitLocationSubmissionByUserRequestDto($input + ['userId' => $auth->id()]);
        $service->execute($dto);
        return $this->responseOk();
    }
}
