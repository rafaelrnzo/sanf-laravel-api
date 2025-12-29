<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Payment\Jobs\MidtransWebhookJob;
use Sanf\Core\Modules\Payment\Payloads\MidtransWebhookPayload;

final class MidtransWebhookController extends RestApiController
{
    public function postHandle(Request $request)
    {
        $form = $this->validate($request, [
            'order_id' => ['nullable', 'string'],
            'status_code' => ['nullable', 'string'],
            'transaction_id' => ['nullable', 'string'],
            'transaction_status' => ['nullable', 'string'],
            'fraud_status' => ['nullable', 'string'],
            'gross_amount' => ['nullable'],
            'signature_key' => ['nullable', 'string'],
        ]);

        $payload = new MidtransWebhookPayload(array_merge(
            $form,
            [
                'received_at' => (string) Carbon::now(),
                'raw_request' => $request->all(),
            ]
        ));

        dispatch(new MidtransWebhookJob($payload));

        return $this->responseOk('OK', [
            'processed' => true,
        ]);
    }
}
