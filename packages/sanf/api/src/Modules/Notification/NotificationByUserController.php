<?php

namespace Sanf\Api\Modules\Notification;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use NbsPhp\Notification\Dtos\BrowseNotificationByUserRequestDto;
use NbsPhp\Notification\Services\BrowseNotificationByUserService;

final class NotificationByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, BrowseNotificationByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);

        $dto = new BrowseNotificationByUserRequestDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result->data, new MyNotificationSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
