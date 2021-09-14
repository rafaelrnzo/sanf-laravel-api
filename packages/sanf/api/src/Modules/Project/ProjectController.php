<?php


namespace Sanf\Api\Modules\Project;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Project\Transformers\MyProjectSimpleTransformer;
use Sanf\Api\Modules\Project\Transformers\MyProjectTransformer;
use Sanf\Api\Modules\Project\Transformers\ProjectSimpleTransformer;
use Sanf\Api\Modules\Project\Transformers\ProjectTransformer;
use Sanf\Core\Modules\Project\Dto\CreateProjectDto;
use Sanf\Core\Modules\Project\Dto\PaginateProjectDto;
use Sanf\Core\Modules\Project\Dto\PaginateUserProjectDto;
use Sanf\Core\Modules\Project\Dto\UpdateProjectDto;
use Sanf\Core\Modules\Project\Services\CreateUserProjectService;
use Sanf\Core\Modules\Project\Services\DeleteUserProjectService;
use Sanf\Core\Modules\Project\Services\GetDetailProjectService;
use Sanf\Core\Modules\Project\Services\GetDetailUserProjectService;
use Sanf\Core\Modules\Project\Services\GetListProjectService;
use Sanf\Core\Modules\Project\Services\GetListUserProjectService;
use Sanf\Core\Modules\Project\Services\PublishUserProjectService;
use Sanf\Core\Modules\Project\Services\UnpublishUserProjectService;
use Sanf\Core\Modules\Project\Services\UpdateUserProjectService;

class ProjectController extends RestApiController
{
    public function getList(Guard $auth, Request $request, GetListProjectService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new PaginateProjectDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result->data, new ProjectSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getDetail(Guard $auth, $xid, GetDetailProjectService $service)
    {
        $dto = (object)[
            'xid' => $xid,
            'userId' => $auth->id()
        ];
        $result = $service->execute($dto);
        return fractal($result, new ProjectTransformer());
    }

    public function getListByUser(Guard $auth, Request $request, GetListUserProjectService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new PaginateUserProjectDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);

        //TODO CREATE CUSTOM FRACTAL CLASS
        return fractal($result->data, new MyProjectSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getDetailByUser(Guard $auth, $xid, GetDetailUserProjectService $service)
    {
        $dto = (object)[
            'xid' => $xid,
            'userId' => $auth->id()
        ];
        $result = $service->execute($dto);
        return fractal($result, new MyProjectTransformer());
    }

    public function postCreateByUser(Guard $auth, Request $request, CreateUserProjectService $service)
    {
        $input = $this->validate($request, [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'submission_limit_at' => ['required', 'integer'],
            'image_file' => ['nullable'],
            'location_id' => ['required'],
            'location_metadata' => ['required'],
            'phone_number' => ['required', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'business_email' => ['required', 'string'],
        ]);
        $dto = new CreateProjectDto($input + ['userId' => $auth->id()]);
        $service->execute($dto);
        return $this->responseOk();
    }

    public function putUpdateByUser(Guard $auth, Request $request, $xid, UpdateUserProjectService $service)
    {
        $input = $this->validate($request, [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'submission_limit_at' => ['required', 'integer'],
            'image_file' => ['nullable'],
            'location_id' => ['required'],
            'location_metadata' => ['required'],
            'phone_number' => ['required', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'business_email' => ['required', 'string'],
        ]);
        $dto = new UpdateProjectDto($input + [
                'xid' => $xid,
                'userId' => $auth->id()
            ]);
        $service->execute($dto);
        return $this->responseOk();
    }

    public function deleteByUser(Guard $auth, $xid, DeleteUserProjectService $service)
    {
        $dto = (object)[
            'userId' => $auth->id(),
            'xid' => $xid
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    public function postPublishByUser(Guard $auth, $xid, PublishUserProjectService $service)
    {
        $dto = (object)[
            'userId' => $auth->id(),
            'xid' => $xid
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    public function postUnpublishByUser(Guard $auth, $xid, UnpublishUserProjectService $service)
    {
        $dto = (object)[
            'userId' => $auth->id(),
            'xid' => $xid
        ];
        $service->execute($dto);
        return $this->responseOk();
    }
}
