<?php

namespace Sanf\Api\Modules\Notification;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use NbsPhp\Notification\Dtos\BrowseNotificationByUserRequestDto;
use NbsPhp\Notification\Dtos\ReadNotificationByUserRequestDto;
use NbsPhp\Notification\Services\BrowseNotificationByUserService;
use Sanf\Core\Modules\Notification\Services\MarkAsReadNotificationByUserService;

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

        return fractal($result->data, new MyNotificationSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function patchMarkAsRead(Guard $auth, Request $request, MarkAsReadNotificationByUserService $service, TransactionalSessionInterface $transactionalSession)
    {
        $input = $this->validate($request, [
            'xids' => ['nullable', 'array'],
        ]);
        $dto = new ReadNotificationByUserRequestDto($input + ['userId' => $auth->id()]);
        $transactionalService = new TransactionalApplicationService($service, $transactionalSession);
        $transactionalService->execute($dto);

        return $this->responseOk();
    }
}
