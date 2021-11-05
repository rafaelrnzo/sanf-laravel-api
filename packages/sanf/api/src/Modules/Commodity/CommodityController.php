<?php


namespace Sanf\Api\Modules\Commodity;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Commodity\Transformers\CommoditySimpleTransformer;
use Sanf\Api\Modules\Commodity\Transformers\CommodityTransformer;
use Sanf\Api\Modules\Commodity\Transformers\MyCommoditySimpleTransformer;
use Sanf\Api\Modules\Commodity\Transformers\MyCommodityTransformer;
use Sanf\Core\Modules\Commodity\Dto\CreateCommodityDto;
use Sanf\Core\Modules\Commodity\Dto\PaginateCommodityDto;
use Sanf\Core\Modules\Commodity\Dto\PaginateUserCommodityDto;
use Sanf\Core\Modules\Commodity\Dto\UpdateCommodityDto;
use Sanf\Core\Modules\Commodity\Services\CreateCommodityByUserService;
use Sanf\Core\Modules\Commodity\Services\DeleteCommodityByService;
use Sanf\Core\Modules\Commodity\Services\GetDetailCommodityByUserService;
use Sanf\Core\Modules\Commodity\Services\GetDetailCommodityService;
use Sanf\Core\Modules\Commodity\Services\GetListCommodityByUserService;
use Sanf\Core\Modules\Commodity\Services\GetListCommodityService;
use Sanf\Core\Modules\Commodity\Services\PublishUserCommodityService;
use Sanf\Core\Modules\Commodity\Services\UnpublishCommodityByUserService;
use Sanf\Core\Modules\Commodity\Services\UpdateCommodityByUserService;

class CommodityController extends RestApiController
{
    public function getList(Guard $auth, Request $request, GetListCommodityService $service)
    {
        $input = $this->validate($request, [
            'timestamp' => ['nullable', 'integer', 'min:0', 'max:99999999999'],
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new PaginateCommodityDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result->data, new CommoditySimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getDetail(Guard $auth, $xid, GetDetailCommodityService $service)
    {
        $dto = (object)[
            'xid' => $xid,
            'userId' => $auth->id()
        ];
        $result = $service->execute($dto);
        return fractal($result, new CommodityTransformer());
    }

    public function getListByUser(Guard $auth, Request $request, GetListCommodityByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new PaginateUserCommodityDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);

        //TODO CREATE CUSTOM FRACTAL CLASS
        return fractal($result->data, new MyCommoditySimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getDetailByUser(Guard $auth, $xid, GetDetailCommodityByUserService $service)
    {
        $dto = (object)[
            'xid' => $xid,
            'userId' => $auth->id()
        ];
        $result = $service->execute($dto);
        return fractal($result, new MyCommodityTransformer());
    }

    public function postCreateByUser(Guard $auth, Request $request, CreateCommodityByUserService $service)
    {
        $input = $this->validate($request, [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'image_file' => ['nullable'],
            'location_id' => ['required'],
            'location_metadata' => ['required'],
            'phone_number' => ['required', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'business_email' => ['required', 'string'],
        ]);
        $dto = new CreateCommodityDto($input + ['userId' => $auth->id()]);
        $service->execute($dto);
        return $this->responseOk();
    }

    public function putUpdateByUser(Guard $auth, Request $request, $xid, UpdateCommodityByUserService $service)
    {
        $input = $this->validate($request, [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'image_file' => ['nullable'],
            'location_id' => ['required'],
            'location_metadata' => ['required'],
            'phone_number' => ['required', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'business_email' => ['required', 'string'],
        ]);
        $dto = new UpdateCommodityDto($input + [
                'xid' => $xid,
                'userId' => $auth->id()
            ]);
        $service->execute($dto);
        return $this->responseOk();
    }

    public function deleteByUser(Guard $auth, $xid, DeleteCommodityByService $service)
    {
        $dto = (object)[
            'userId' => $auth->id(),
            'xid' => $xid
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    public function postPublishByUser(Guard $auth, $xid, PublishUserCommodityService $service)
    {
        $dto = (object)[
            'userId' => $auth->id(),
            'xid' => $xid
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    public function postUnpublishByUser(Guard $auth, $xid, UnpublishCommodityByUserService $service)
    {
        $dto = (object)[
            'userId' => $auth->id(),
            'xid' => $xid
        ];
        $service->execute($dto);
        return $this->responseOk();
    }
}
