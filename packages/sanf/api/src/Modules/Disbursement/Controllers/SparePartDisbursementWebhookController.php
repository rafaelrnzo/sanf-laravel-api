<?php

namespace Sanf\Api\Modules\Disbursement\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Modules\Disbursement\Exceptions\SparePartDisbrusementValidated;
use Sanf\Core\Modules\Disbursement\Payloads\StatusFromCoreSparePartDisbursementPayload;
use Sanf\Core\Modules\Disbursement\Payloads\ValidateSparePartDisbursementPayload;
use Sanf\Core\Modules\Disbursement\UseCases\StatusSparePartDisbursementUseCase;
use Sanf\Core\Modules\Disbursement\UseCases\ValidateSparePartDisbursementUseCase;
use Sanf\Core\Modules\Log\Enums\WebhookLogKeyEnum;
use Sanf\Core\Modules\Log\Payloads\CreateWebhookLogPayload;
use Sanf\Core\Modules\Log\UseCases\WebhookLogUseCase;

class SparePartDisbursementWebhookController extends RestApiController
{
    public function invoiceValidation(
        Request $request,
        ValidateSparePartDisbursementUseCase $useCase,
        WebhookLogUseCase $webhookLogUseCase
    ) {
        $formData = $this->validate($request, [
            'batch_id' => ['required', 'string'],
            'validation_complete' => ['nullable', 'boolean'],
            'customers' => ['required', 'array'],
            'customers.*.cust_id' => ['required'],
            'customers.*.cust_id_sanfind' => ['required', 'string'],
            'customers.*.tipe_pembayaran_id' => ['required', 'string'],
            'customers.*.tipe_pembayaran_desc' => ['required', 'string'],
            'customers.*.no_plafond' => ['required', 'string'],
            'customers.*.status_code' => ['required', 'string'],
            'customers.*.status_message' => ['required', 'string'],
            'customers.*.summary' => ['required', 'array'],
            'customers.*.summary.total_invoice' => ['required', 'numeric'],
            'customers.*.summary.approved' => ['required', 'numeric'],
            'customers.*.summary.rejected' => ['required', 'numeric'],
            'customers.*.invoices' => ['required', 'array'],
            'customers.*.invoices.*.cust_id' => ['required'],
            'customers.*.invoices.*.cust_id_sanfind' => ['required', 'string'],
            'customers.*.invoices.*.no_invoice' => ['required', 'string'],
            'customers.*.invoices.*.status' => ['required', 'boolean'],
            'customers.*.invoices.*.message' => ['nullable', 'string'],
        ]);

        $receveivedAt = (string) Carbon::now();

        $payload = new ValidateSparePartDisbursementPayload($formData);

        try {
            DB::connection(ConnectionDB::PG_SQL_CMS)->transaction(
                fn () => $useCase->execute($payload)
            );
        } catch (SparePartDisbrusementValidated $e) {
            return $this->responseOk('Disbursement batch already validated');
        }

        try {
            $webhookLogUseCase->create(new CreateWebhookLogPayload([
                'xid' => nano_id_alphanumeric(10),
                'key' => WebhookLogKeyEnum::SPARE_PART_DISBURSEMENT_VALIDATION,
                'reference_id' => str_limit($payload->batch_id, 255),
                'payload' => $request->all(),
                'received_at' => $receveivedAt,
                'processed_at' => (string) Carbon::now(),
            ]));
        } catch (\Throwable $th) {
            report($th);
        }

        return $this->responseOk();
    }

    public function updateStatus(
        Request $request,
        StatusSparePartDisbursementUseCase $useCase,
        WebhookLogUseCase $webhookLogUseCase
    ) {
        $formData = $this->validate($request, [
            'batch_id' => ['required', 'string'],
            'cust_id' => ['required'],
            'cust_id_sanfind' => ['required', 'string'],
            'status_batch_id' => ['required', 'string'],
            'status_batch_desc' => ['required', 'string'],
        ]);

        $receveivedAt = (string) Carbon::now();

        $payload = new StatusFromCoreSparePartDisbursementPayload($formData);

        DB::connection(ConnectionDB::PG_SQL_CMS)->transaction(
            fn () => $useCase->updateFromCore($payload)
        );

        try {
            $webhookLogUseCase->create(new CreateWebhookLogPayload([
                'xid' => nano_id_alphanumeric(10),
                'key' => WebhookLogKeyEnum::SPARE_PART_DISBURSEMENT_STATUS,
                'reference_id' => str_limit("{$payload->batch_id}|{$payload->cust_id}|{$payload->cust_id_sanfind}", 255),
                'payload' => $request->all(),
                'received_at' => $receveivedAt,
                'processed_at' => (string) Carbon::now(),
            ]));
        } catch (\Throwable $th) {
            report($th);
        }

        return $this->responseOk();
    }
}
