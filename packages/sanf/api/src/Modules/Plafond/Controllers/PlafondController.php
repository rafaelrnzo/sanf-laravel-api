<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Plafond\Transformers\PlafondSimpleTransformer;
use Sanf\Api\Modules\Plafond\Transformers\PlafondTransformer;
use Sanf\Api\Modules\Plafond\Transformers\PlafondTypeListTransformer;
use Sanf\Core\Modules\Plafond\Dtos\AddPlafondRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondByProfileRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\ReadPlafondByProfileAndTypeRequestDto;
use Sanf\Core\Modules\Plafond\Services\ApplyPlafondByUserService;
use Sanf\Core\Modules\Plafond\Services\BrowsePlafondByUserService;
use Sanf\Core\Modules\Plafond\Services\ListPlafondTypeService;
use Sanf\Core\Modules\Plafond\Services\ReadPlafondByUserAndTypeService;

class PlafondController extends RestApiController
{
    public function getBrowseTypes(ListPlafondTypeService $service)
    {
        // TODO refactor this static pagination filter
        $dto = (object)[
            'limit' => 10,
            'skip' => 0,
            'sort_by' => 'default',
        ];
        $result = $service->execute($dto);

        return fractal($result->data, PlafondTypeListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getBrowseByUserProfile(Guard $auth, Request $request, $xid, BrowsePlafondByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new BrowsePlafondByProfileRequestDto($input + ['profileXid' => $xid, 'userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result->data, new PlafondSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getReadByUserProfileAndType(Guard $auth, $xid, $typeId, ReadPlafondByUserAndTypeService $service)
    {
        $dto = new ReadPlafondByProfileAndTypeRequestDto([
            'userId' => $auth->id(),
            'typeId' => $typeId,
            'profileXid' => $xid,
        ]);
        $result = $service->execute($dto);
        return fractal($result, new PlafondTransformer());
    }

    public function postAddByUserProfile(Guard $auth, $xid, Request $request, ApplyPlafondByUserService $service)
    {
        $input = $this->validate($request, [
            'plafond_type_id' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string', 'max:255'],
        ]);
        $dto = new AddPlafondRequestDto($input + ['profileXid' => $xid, 'userId' => $auth->id()]);
        $service->execute($dto);
        return $this->responseOk();
    }
}
