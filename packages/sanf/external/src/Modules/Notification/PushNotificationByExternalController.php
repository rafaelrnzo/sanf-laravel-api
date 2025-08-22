<?php

namespace Sanf\External\Modules\Notification;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Database\MultipleTransactionalSessionInterface;
use Sanf\Core\Modules\Notification\Dtos\SendPushNotificationByExternalRequestDto;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Notification\Services\SendPushNotificationByExternalService;
use Sanf\Core\Services\MultipleTransactionalApplicationService;

class PushNotificationByExternalController extends RestApiController
{
    public function postAdd(
        Request $request,
        SendPushNotificationByExternalService $service,
        MultipleTransactionalSessionInterface $transactionalSession
    )
    {
        $input = $this->validate($request, [
            'is_notify_all' => 'boolean',
            'email' => Rule::requiredIf(function () use ($request) {
                return !$request->get('is_notify_all');
            }),
            'email.*' => 'email|string|max:255',
            'type' => 'required|integer|' . Rule::in(NotificationTypeEnum::ALL_TYPE),
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'screen' => 'nullable|string|max:255',
            'body' => 'nullable|string|max:65535',
            'published_at' => 'nullable|integer|max:99999999999',
            'dashboard_web_data' => 'nullable|array', // @since CR2025
        ]);
        $input['type'] = new NotificationTypeEnum($input['type']);
        $dto = new SendPushNotificationByExternalRequestDto($input);

        $transactionalService = new MultipleTransactionalApplicationService($service, $transactionalSession);
        $transactionalService->execute($dto);

        return $this->responseOk();
    }
}
