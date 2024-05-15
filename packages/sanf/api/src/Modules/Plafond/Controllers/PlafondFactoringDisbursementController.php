<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Plafond\Transformers\PlafondFactoringDisbursementTransformer;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementAllocationFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementBowheerFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementDocumentFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementInvoiceFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\PlafondDisbursementFormRequest;
use Sanf\Core\Modules\Plafond\UseCase\SubmitPlafondDisbursementUseCase;

class PlafondFactoringDisbursementController extends RestApiController
{
    public function browse(
        Guard $auth,
        string $xid,
        string $plafond_xid,
        Request $request
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);

        $result = (object) [
            'data' => json_decode('[{"xid":"ABC123","bouwheer":"PT Cipta Karya Abadi","code":"ABC123","total_amount":58555000.01,"status":10,"notes":"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.","invoices":[{"photos":[{"url":"https://domain.com/XawrIqwejGQ.jpeg","file_name":"XawrIqwejGQ.jpeg","origin_name":"invoice_1.jpeg"},{"url":"https://domain.com/ZawrIqwejGQ.jpeg","file_name":"ZawrIqwejGQ.jpeg","origin_name":"invoice_2.jpeg"}],"url":"https://domain.com/xawrIqwejGQ.pdf","file_name":"xawrIqwejGQ.pdf","origin_name":"Invoice Document.pdf","invoice_no":"01 - INV / I - 24 / SEJAHTERA","invoice_date":"2024-01-10","invoice_amount":926977091.04,"tax_amount":18539541,"vat_amount":101967480,"backharge_amount":0,"other_amount":0,"total_amount":1010405030.04,"order_no":1}],"allocations":[{"xid":"ABC123","account_name":"PT ABC","account_provider":"BCA","account_no":"123abc","notes":"lorem ipsum","is_default":true,"amount":2020810060.08}],"payment_acc_document":{"file_name":"tMZ1vq1fT53MNhbTCAgTlbcXh41qsdksLDK6KkRu.png","origin_name":"timun.png"},"other_document":[{"file_name":"tMZ1vq1fT53MNhbTCAgTlbcXh41qsdksLDK6KkRu.png","origin_name":"timun.png"},{"file_name":"tMZ1vq1fT53MNhbTCAgTlbcXh41qsdksLDK6KkRu.png","origin_name":"timun.png"}],"created_at":1713673136,"updated_at":1712214624}]'),
            'paginate' => (object) [
                'total' => 1,
                'count' =>  1,
                'skip' => 0,
                'limit' =>  10,
                'sortBy' => 'Latest',
            ],
        ];

        return fractal($result->data)
            ->transformWith(PlafondFactoringDisbursementTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function add(
        Guard $auth,
        string $xid,
        string $plafond_xid,
        Request $request,
        TransactionalSessionInterface $transactionalSession,
        SubmitPlafondDisbursementUseCase $submitUseCase
    ) {
        $this->validate($request, [
            'bouwheer.id' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'bouwheer.name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'bouwheer.email' => ['required', 'email', 'max:32'],
            'bouwheer.code' => ['required', 'string', 'max:32'],
            'invoices' => ['required', 'array'],
            'invoices.*.photos' => ['nullable', 'array'],
            'invoices.*.photos.*.file_name' => ['nullable', 'string'],
            'invoices.*.photos.*.origin_name' => ['nullable', 'string'],
            'invoices.*.file_name' => ['required', 'string'],
            'invoices.*.origin_name' => ['required', 'string'],
            'invoices.*.invoice_no' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'invoices.*.invoice_date' => ['required', 'date_format:Y-m-d'],
            'invoices.*.invoice_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.tax_amount'  => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.vat_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.backharge_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.other_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.total_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.order_no' => ['required', 'integer'],
            'total_invoice_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'allocations' => ['required', 'array'],
            'allocations.*.xid' => ['required', 'string', 'max:32'],
            'allocations.*.account_name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.account_provider' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.account_no' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.notes' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.is_default' => ['required', 'boolean'],
            'allocations.*.amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'allocations.*.order_no' => ['required', 'integer'],
            'payment_acc_document' => ['nullable', 'array'],
            'payment_acc_document.file_name' => ['nullable', 'string'],
            'payment_acc_document.origin_name' => ['nullable', 'string'],
            'other_document' => ['nullable', 'array'],
            'other_document.*.file_name' => ['required', 'string'],
            'other_document.*.origin_name' => ['required', 'string'],
            'customer_review' => ['required', 'boolean'],
            'created_at' => ['required', 'integer'],
        ]);

        $formRequest = new PlafondDisbursementFormRequest([
            'user_id' => $auth->id(),
            'client_id' => $xid,
            'plafond_id' => $plafond_xid,
            'bouwheer' => new DisbursementBowheerFormRequest([
                'id' => $request->get('bouwheer')['id'],
                'name' => $request->get('bouwheer')['name'],
                'email' => $request->get('bouwheer')['email'],
                'code' => $request->get('bouwheer')['code'],
            ]),
            'invoices' => array_map(function ($invoice) {
                $invoice['invoice_amount'] = (float) ($invoice['invoice_amount'] ?? 0.0);
                $invoice['tax_amount'] = (float) ($invoice['tax_amount'] ?? 0.0);
                $invoice['vat_amount'] = (float) ($invoice['vat_amount'] ?? 0.0);
                $invoice['backharge_amount'] = (float) ($invoice['backharge_amount'] ?? 0.0);
                $invoice['other_amount'] = (float) ($invoice['other_amount'] ?? 0.0);
                $invoice['total_amount'] = (float) ($invoice['total_amount'] ?? 0.0);
                $invoice['photos'] = array_map(function ($photo) {
                    return new DisbursementDocumentFormRequest([
                        'name' => $photo['file_name'],
                        'origin' => $photo['origin_name'],
                    ]);
                }, $invoice['photos']);

                return new DisbursementInvoiceFormRequest($invoice);
            }, $request->get('invoices')),
            'total_invoice_amount' => (float) $request->get('total_invoice_amount'),
            'allocations' => array_map(function ($allocation) {
                return new DisbursementAllocationFormRequest([
                    'id' => $allocation['xid'],
                    'name' => $allocation['account_name'],
                    'provider' => $allocation['account_provider'],
                    'account_no' => $allocation['account_no'],
                    'notes' => $allocation['notes'],
                    'is_default' => $allocation['is_default'],
                    'amount' => (float) $allocation['amount'],
                    'order_no' => $allocation['order_no'],
                ]);
            }, $request->get('allocations')),
            'payment_acc_document' => new DisbursementDocumentFormRequest([
                'name' => $request->get('payment_acc_document')['file_name'] ?? null,
                'origin' => $request->get('payment_acc_document')['origin_name'] ?? null,
            ]),
            'other_document' => array_map(function ($document) {
                return new DisbursementDocumentFormRequest([
                    'name' => $document['file_name'],
                    'origin' => $document['origin_name'],
                ]);
            }, $request->get('other_document')),
            'customer_review' => $request->get('customer_review'),
            'created_at' => $request->get('created_at'),
        ]);

        $transactionalService = new TransactionalApplicationService($submitUseCase, $transactionalSession);
        $transactionalService->execute($formRequest);

        return $this->responseOk();
    }

    public function update(
        Guard $auth,
        string $xid,
        string $plafond_xid,
        string $disbursement_xid,
        Request $request
    ) {
        $this->validate($request, [
            'bouwheer' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'code' => ['required', 'string', 'max:32'],
            'invoices' => ['required', 'array'],
            'invoices.*.photos' => ['required', 'array'],
            'invoices.*.photos.*.file_name' => ['required', 'string'],
            'invoices.*.photos.*.origin_name' => ['required', 'string'],
            'invoices.*.file_name' => ['required', 'string'],
            'invoices.*.origin_name' => ['required', 'string'],
            'invoices.*.invoice_no' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'invoices.*.invoice_date' => ['required', 'date_format:Y-m-d'],
            'invoices.*.invoice_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.tax_amount'  => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.vat_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.backharge_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.other_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.total_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'invoices.*.order_no' => ['required', 'integer'],
            'total_invoice_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'allocations' => ['required', 'array'],
            'allocations.*.xid' => ['required', 'string', 'max:32'],
            'allocations.*.account_name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.account_provider' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.account_no' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.notes' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.is_default' => ['required', 'boolean'],
            'allocations.*.amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'payment_acc_document' => ['nullable', 'array'],
            'payment_acc_document.file_name' => ['nullable', 'string'],
            'payment_acc_document.origin_name' => ['nullable', 'string'],
            'other_document' => ['nullable', 'array'],
            'other_document.*.file_name' => ['required', 'string'],
            'other_document.*.origin_name' => ['required', 'string'],
            'customer_review' => ['required', 'boolean'],
            'created_at' => ['required', 'integer'],
        ]);

        return $this->responseOk();
    }
}
