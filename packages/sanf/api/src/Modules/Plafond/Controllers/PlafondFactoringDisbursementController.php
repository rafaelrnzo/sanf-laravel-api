<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Plafond\Transformers\PlafondFactoringDisbursementSimpleTransformer;
use Sanf\Api\Modules\Plafond\Transformers\PlafondFactoringDisbursementTransformer;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondDisbursementRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementAllocationFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementBowheerFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementDocumentFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementInvoiceFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\PlafondDisbursementFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\ReadPlafondDisbursementRequestDto;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Sanf\Core\Modules\Plafond\UseCases\AddPlafondDisbursementUseCase;
use Sanf\Core\Modules\Plafond\UseCases\BrowsePlafondDisbursementUseCase;
use Sanf\Core\Modules\Plafond\UseCases\ReadPlafondDisbursementUseCase;
use Sanf\Core\Modules\Plafond\UseCases\UpdatePlafondDisbursementUseCase;

class PlafondFactoringDisbursementController extends RestApiController
{
    public function browse(
        Guard $auth,
        string $xid,
        string $plafond_xid,
        Request $request,
        BrowsePlafondDisbursementUseCase $browseUseCase
    ) {
        $formData = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
            'status_id' => ['nullable', 'integer', Rule::in(PlafondDisbursementStatusEnum::ALL_TAB)],
        ]);

        $browsePlafondDisbursementRequestDto = new BrowsePlafondDisbursementRequestDto(
            array_merge($formData, [
                'user_id' => $auth->id(),
                'profile_xid' => $xid,
                'plafond_xid' => $plafond_xid,
            ])
        );

        $browsePlafondDisbursementResponseDto = $browseUseCase->execute($browsePlafondDisbursementRequestDto);

        return fractal($browsePlafondDisbursementResponseDto->data)
            ->transformWith(PlafondFactoringDisbursementSimpleTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($browsePlafondDisbursementResponseDto->paginate));
    }

    public function read(
        Guard $auth,
        string $xid,
        string $plafond_xid,
        string $disbursement_xid,
        ReadPlafondDisbursementUseCase $readUseCase
    ) {
        $readPlafondDisbursementRequestDto = new ReadPlafondDisbursementRequestDto([
            'user_id' => $auth->id(),
            'profile_xid' => $xid,
            'plafond_xid' => $plafond_xid,
            'disbursement_xid' => $disbursement_xid,
        ]);

        $readPlafondDisbursementResponseDto = $readUseCase->execute($readPlafondDisbursementRequestDto);

        return fractal($readPlafondDisbursementResponseDto)
            ->transformWith(PlafondFactoringDisbursementTransformer::class);
    }

    public function add(
        Guard $auth,
        string $xid,
        string $plafond_xid,
        Request $request,
        TransactionalSessionInterface $transactionalSession,
        AddPlafondDisbursementUseCase $addUseCase
    ) {
        $this->validate($request, [
            'bouwheer.id' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'bouwheer.name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'bouwheer.email' => ['required', 'email', 'max:32'],
            'bouwheer.code' => ['required', 'string', 'max:32'],
            'bouwheer.cust_id' => ['nullable', 'string', 'max:32'],
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
            'invoices.*.due_at' => ['nullable', 'date_format:Y-m-d'],
            'total_invoice_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'allocations' => ['required', 'array'],
            'allocations.*.xid' => ['required', 'string', 'max:32'],
            'allocations.*.account_name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.account_provider' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.account_no' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.notes' => ['nullable', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
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
                'cust_id' => $request->get('bouwheer')['cust_id'] ?? null,
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

        $transactionalService = new TransactionalApplicationService($addUseCase, $transactionalSession);
        $transactionalService->execute($formRequest);

        return $this->responseOk();
    }

    public function update(
        Guard $auth,
        string $xid,
        string $plafond_xid,
        string $disbursement_xid,
        Request $request,
        TransactionalSessionInterface $transactionalSession,
        UpdatePlafondDisbursementUseCase $updateUseCase
    ) {
        $this->validate($request, [
            'bouwheer.id' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'bouwheer.name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'bouwheer.email' => ['required', 'email', 'max:32'],
            'bouwheer.code' => ['required', 'string', 'max:32'],
            'bouwheer.cust_id' => ['nullable', 'string', 'max:32'],
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
            'invoices.*.due_at' => ['nullable', 'date_format:Y-m-d'],
            'total_invoice_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'allocations' => ['required', 'array'],
            'allocations.*.xid' => ['required', 'string', 'max:32'],
            'allocations.*.account_name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.account_provider' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.account_no' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'allocations.*.notes' => ['nullable', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
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
            'disbursement_id' => $disbursement_xid,
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

        $transactionalService = new TransactionalApplicationService($updateUseCase, $transactionalSession);
        $transactionalService->execute($formRequest);

        return $this->responseOk();
    }
}
