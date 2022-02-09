<?php

namespace NbsPhp\Notification\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use NbsPhp\Notification\Dtos\BrowseNotificationByUserRequestDto;
use NbsPhp\Notification\Services\BrowseNotificationByUserService;
use NbsPhp\Notification\Transformers\NotificationTransformer;

final class NotificationByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, BrowseNotificationByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
            'timestamp' => ['nullable', 'integer', 'max:99999999999'],
        ]);

        $dto = new BrowseNotificationByUserRequestDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result->data, new NotificationTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
