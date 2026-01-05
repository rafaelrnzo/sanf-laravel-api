<?php

namespace Sanf\Api\Modules\Disbursement\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Modules\Disbursement\Exceptions\SparePartDisbrusementValidated;
use Sanf\Core\Modules\Disbursement\Payloads\ValidateSparePartDisbursementPayload;
use Sanf\Core\Modules\Disbursement\UseCases\ValidateSparePartDisbursementUseCase;

class SparePartDisbursementWebhookController extends RestApiController
{
    public function invoiceValidation(
        Request $request,
        ValidateSparePartDisbursementUseCase $useCase
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

        try {
            DB::connection(ConnectionDB::PG_SQL_CMS)->transaction(function () use ($formData, $useCase) {
                $payload = new ValidateSparePartDisbursementPayload($formData);

                $useCase->execute($payload);
            });
        } catch (SparePartDisbrusementValidated $e) {
            return $this->responseOk('Disbursement batch already validated');
        }

        return $this->responseOk();
    }
}
