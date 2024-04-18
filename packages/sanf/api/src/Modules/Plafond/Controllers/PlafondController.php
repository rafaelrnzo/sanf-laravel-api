<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Plafond\Transformers\PlafondFactoringTransformer;
use Sanf\Api\Modules\Plafond\Transformers\PlafondHistoryTransformer;
use Sanf\Api\Modules\Plafond\Transformers\PlafondSimpleTransformer;
use Sanf\Api\Modules\Plafond\Transformers\PlafondTransformer;
use Sanf\Api\Modules\Plafond\Transformers\PlafondTypeListTransformer;
use Sanf\Core\Modules\Plafond\Dtos\AddPlafondRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondByProfileRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondFactoringRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondHistoryByUserRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\ReadPlafondByProfileAndTypeRequestDto;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;
use Sanf\Core\Modules\Plafond\Services\ApplyIncreasePlafondByUserService;
use Sanf\Core\Modules\Plafond\Services\ApplyNewPlafondByUserService;
use Sanf\Core\Modules\Plafond\Services\BrowsePlafondByUserService;
use Sanf\Core\Modules\Plafond\Services\BrowsePlafondFactoringService;
use Sanf\Core\Modules\Plafond\Services\BrowsePlafondHistoryByUserService;
use Sanf\Core\Modules\Plafond\Services\ListPlafondTypeService;
use Sanf\Core\Modules\Plafond\Services\ReadPlafondByUserAndTypeService;
use Sanf\Core\Modules\User\Services\GetDetailCustomerProfileByUserService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PlafondController extends RestApiController
{
    public function getBrowseTypesOldest(ListPlafondTypeService $service)
    {
        // TODO refactor this static pagination filter
        $dto = (object) [
            'limit' => 10,
            'skip' => 0,
            'sort_by' => 'default',
        ];
        $result = $service->execute($dto);
        $filter = $result->data->filter(function ($model, $key) {
            return $model->id !== PlafondTypeEnum::FACTORING;
        });
        $result->data = $filter->all();

        return fractal($result->data, PlafondTypeListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getBrowseTypes(ListPlafondTypeService $service)
    {
        // TODO refactor this static pagination filter
        $dto = (object) [
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

    public function getBrowseHistoryByUserProfile(Guard $auth, Request $request, $xid, BrowsePlafondHistoryByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new BrowsePlafondHistoryByUserRequestDto($input + ['profileXid' => $xid, 'userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result->data, new PlafondHistoryTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getReadByUserProfileAndType(Guard $auth, $xid, $typeId, ReadPlafondByUserAndTypeService $service)
    {
        $dto = new ReadPlafondByProfileAndTypeRequestDto([
            'userId' => $auth->id(),
            'typeId' => $typeId,
            'profileXid' => $xid,
        ]);

        if ($typeId === PlafondTypeEnum::FACTORING) {
            throw new BadRequestHttpException('Please update your apps');
        }

        $result = $service->execute($dto);

        return fractal($result, new PlafondTransformer());
    }

    public function postAddByUserProfile(
        Guard $auth,
        $xid,
        Request $request,
        ApplyNewPlafondByUserService $plafondService,
        GetDetailCustomerProfileByUserService $profileService
    ) {
        $input = $this->validate($request, [
            'type_id' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
        ]);

        $isPlafondFactoring = $request->get('type_id') === PlafondTypeEnum::FACTORING;
        $isEmptyNotes = is_null($request->get('notes')) || empty($request->get('notes'));
        if ($isPlafondFactoring && $isEmptyNotes === true) {
            throw new BadRequestHttpException('Please update your apps');
        }

        $profile = $profileService->execute((object) [
            'userId' => $auth->id(),
            'customerId' => $xid,
        ]);
        $dto = new AddPlafondRequestDto($input + [
            'profileXid' => $xid,
            'profile' => $profile,
            'userId' => $auth->id(),
        ]);
        $plafondService->execute($dto);

        return $this->responseOk();
    }

    public function postIncreaseByUserProfile(
        Guard $auth,
        $xid,
        Request $request,
        ApplyIncreasePlafondByUserService $plafondService,
        GetDetailCustomerProfileByUserService $profileService
    ) {
        $input = $this->validate($request, [
            'type_id' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
        ]);

        $isPlafondFactoring = $request->get('type_id') === PlafondTypeEnum::FACTORING;
        $isEmptyNotes = is_null($request->get('notes')) || empty($request->get('notes'));
        if ($isPlafondFactoring && $isEmptyNotes === true) {
            throw new BadRequestHttpException('Please update your apps');
        }

        $profile = $profileService->execute((object) [
            'userId' => $auth->id(),
            'customerId' => $xid,
        ]);
        $dto = new AddPlafondRequestDto($input + [
            'profileXid' => $xid,
            'profile' => $profile,
            'userId' => $auth->id(),
        ]);
        $plafondService->execute($dto);

        return $this->responseOk();
    }

    public function browsePlafondFactoring(
        Guard $auth,
        $xid,
        Request $request,
        BrowsePlafondFactoringService $service
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new BrowsePlafondFactoringRequestDto($input + ['profileXid' => $xid, 'userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result->data)
            ->transformWith(PlafondFactoringTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
