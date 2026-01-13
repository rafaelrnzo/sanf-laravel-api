<?php

namespace Sanf\External\Modules\Notification;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Database\MultipleTransactionalSessionInterface;
use Sanf\Core\Modules\Installment\Jobs\SendPushNotificationBillChangeJob;
use Sanf\Core\Modules\Installment\Jobs\SendPushNotificationContractPublishedJob;
use Sanf\Core\Modules\Installment\Jobs\SendPushNotificationInstallmentAlmostDueJob;
use Sanf\Core\Modules\Installment\Jobs\SendPushNotificationInstallmentDueTodayJob;
use Sanf\Core\Modules\Installment\Jobs\SendPushNotificationInstallmentOverdueJob;
use Sanf\Core\Modules\Installment\Payloads\SendPushNotificationBillChangeJobPayload;
use Sanf\Core\Modules\Installment\Payloads\SendPushNotificationContractPublishedJobPayload;
use Sanf\Core\Modules\Installment\Payloads\SendPushNotificationInstallmentJobPayload;
use Sanf\Core\Modules\Notification\Dtos\SendPushNotificationByExternalRequestDto;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Notification\Services\SendPushNotificationByExternalService;
use Sanf\Core\Services\MultipleTransactionalApplicationService;
use Sanf\External\Modules\Notification\Enums\PostSanfindUserTypeEnum;

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

    public function postAddSanfindUser(
        Request $request
    )
    {
        $types = array_values(array_map(fn ($item) => $item->getValue(), PostSanfindUserTypeEnum::values()));

        $dataRules = [];

        if (in_array($request->input('type'), [
            PostSanfindUserTypeEnum::INSTALLMENT_OVERDUE,
            PostSanfindUserTypeEnum::INSTALLMENT_DUE_TODAY,
            PostSanfindUserTypeEnum::INSTALLMENT_ALMOST_DUE,
            PostSanfindUserTypeEnum::CONTRACT_PUBLISHED,
        ])) {
            $dataRules = [
                'data.contract_no' => 'required|string',
                'data.due_date' => 'required|date:Y-m-d',
                'data.total_amount' => 'required|numeric',
                'data.customer_id_sanfind' => 'required|string',
            ];
        }

        if (in_array($request->input('type'), [
            PostSanfindUserTypeEnum::BILL_TO_INSTALLMENT,
        ])) {
            $dataRules = [
                'data.contract_no' => 'required|string',
                'data.due_date' => 'required|date:Y-m-d',
                'data.customer_id_sanfind' => 'required|string',
                'data.tenor_value' => 'required|numeric',
                'data.tenor_unit' => ['required', Rule::in(['day', 'month', 'year'])],
            ];
        }

        $input = $this->validate($request, array_merge($dataRules, [
            'type' => ['required', Rule::in($types)],
            'data' => ['nullable'],
        ]));

        switch ($input['type']) {
            case PostSanfindUserTypeEnum::INSTALLMENT_OVERDUE:
                $payload = new SendPushNotificationInstallmentJobPayload($input['data']);
                dispatch(new SendPushNotificationInstallmentOverdueJob($payload));
                break;
            case PostSanfindUserTypeEnum::INSTALLMENT_DUE_TODAY:
                $payload = new SendPushNotificationInstallmentJobPayload($input['data']);
                dispatch(new SendPushNotificationInstallmentDueTodayJob($payload));
                break;
            case PostSanfindUserTypeEnum::INSTALLMENT_ALMOST_DUE:
                $payload = new SendPushNotificationInstallmentJobPayload($input['data']);
                dispatch(new SendPushNotificationInstallmentAlmostDueJob($payload));
                break;
            case PostSanfindUserTypeEnum::CONTRACT_PUBLISHED:
                $payload = new SendPushNotificationContractPublishedJobPayload($input['data']);
                dispatch_now(new SendPushNotificationContractPublishedJob($payload));
                break;
            case PostSanfindUserTypeEnum::BILL_TO_INSTALLMENT:
                $payload = new SendPushNotificationBillChangeJobPayload($input['data']);
                dispatch_now(new SendPushNotificationBillChangeJob($payload));
                break;
        }

        return $this->responseOk();
    }
}
