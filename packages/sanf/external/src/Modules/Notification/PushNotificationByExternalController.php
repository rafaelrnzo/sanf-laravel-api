<?php

namespace Sanf\External\Modules\Notification;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationByExternalRequestDto;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Notification\Services\AddPushNotificationByExternalService;

class PushNotificationByExternalController extends RestApiController
{
    public function postAdd(Request $request, AddPushNotificationByExternalService $service)
    {
        $input = $this->validate($request, [
            'id' => ['required', 'string', 'max:36'],
            'type' => ['required', 'integer', Rule::in(NotificationTypeEnum::ALL_TYPE)],
            'email' => ['required', 'string', 'max:255'],
            'customer_id' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'screen' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:65535'],
            'published_at' => ['nullable', 'integer', 'max:99999999999'],
        ]);
        $input['type'] = new NotificationTypeEnum($input['type']);
        $dto = new AddPushNotificationByExternalRequestDto($input);
        $service->execute($dto);
        return $this->responseOk();
    }
}
