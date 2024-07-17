<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\PlafondDisbursementCoreFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\PlafondDisbursementFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\ReadPlafondDisbursementRequestDto;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementNotFoundException;
use Sanf\Core\Modules\Plafond\Queries\ReadPlafondDisbursementEloquentBuilder;
use Sanf\Core\Modules\Plafond\Repositories\PlafondDisbursementRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class SubmitPlafondDisbursementCoreUseCase implements ApplicationServiceInterface
{
    private $disbursementRepository;
    private $coreClient;

    public function __construct(
        PlafondDisbursementRepositoryInterface $disbursementRepository,
        SanfCoreApiClient $coreClient
    ) {
        $this->disbursementRepository = $disbursementRepository;
        $this->coreClient = $coreClient;
    }

    /**
     * @return mixed
     */
    public function execute($formRequest = null)
    {
        /** @var PlafondDisbursementFormRequest $formRequest */
        $dto = new ReadPlafondDisbursementRequestDto([
            'user_id' => $formRequest->userId,
            'profile_xid' => $formRequest->clientId,
            'plafond_xid' => $formRequest->plafondId,
            'disbursement_xid' => $formRequest->disbursementId,
        ]);

        $plafondDisbursements = $this->disbursementRepository->query(new ReadPlafondDisbursementEloquentBuilder($dto));
        if (count($plafondDisbursements) === 0) {
            throw new PlafondDisbursementNotFoundException();
        }

        $disbursementData = $plafondDisbursements[0];

        $paymentAccDocument = [];
        if (!is_null($disbursementData->disbursement_relation->payment_acc_doc_path)) {
            $paymentAccDocument = (object) [
                'percepatan_name' => $disbursementData->disbursement_relation->payment_acc_doc_origin_name,
                'percepatan_path' => config('image-path.plafond.disbursement.payment_acc_document'),
            ];
        }
        if ($disbursementData->status_id === PlafondDisbursementStatusEnum::REVISION && !is_null($disbursementData->disbursement_relation->payment_acc_web_doc_path)) {
            $paymentAccDocument = (object) [
                'percepatan_name' => $disbursementData->disbursement_relation->payment_acc_web_doc_origin_name,
                'percepatan_path' => config('image-path.plafond.disbursement.payment_acc_document'),
            ];
        }

        $plafondDisbursementCoreFormRequest = new PlafondDisbursementCoreFormRequest([
            'cust_id' => $disbursementData->customer_id,
            'p_code' => PlafondTypeEnum::FACTORING,
            'disbursement_no' => $disbursementData->disbursement_no,
            'plafond_id' => $disbursementData->plafond_id,
            'bouwheer' => $disbursementData->customer_name,
            'bouwheer_code' => $disbursementData->customer_code,
            'amount' => $this->getTotalAmount($disbursementData->client_amount, $disbursementData->customer_amount, $disbursementData->admin_amount),
            'invoices' => $this->mapInvoices($disbursementData->disbursement_relation->invoices_relation),
            'allocations' => $this->mapAllocations($disbursementData->disbursement_relation->allocations_relation),
            'percepatan_doc' => !empty($paymentAccDocument) ? [$paymentAccDocument] : null,
            'pendukung_doc' => $this->mapOtherDocuments($disbursementData->disbursement_relation->documents_relation),
            'created_at' => unix_timestamp($disbursementData->created_at),
        ]);

        $coreResponse = $this->coreClient->submitPlafondDisbursement($plafondDisbursementCoreFormRequest);

        return $plafondDisbursementCoreFormRequest;
    }

    public function getTotalAmount(float $clientAmount, float $customerAmount, float $adminAmount): float
    {
        $totalAmount = 0;
        if ($clientAmount > 0) {
            $totalAmount = $clientAmount;
        }
        if ($customerAmount > 0) {
            $totalAmount = $customerAmount;
        }
        if ($adminAmount > 0) {
            $totalAmount = $adminAmount;
        }

        return $totalAmount;
    }

    public function mapInvoices(array $invoices): array
    {
        return array_map(function ($invoice) {
            $totalAmount = ($invoice->invoice_amount + $invoice->vat_amount + $invoice->other_amount) - ($invoice->tax_amount + $invoice->backharge_amount);

            return (object) [
                'invc_number' => $invoice->document_no,
                'invc_date' => $invoice->document_date,
                'invc_amount' => (float) $invoice->invoice_amount,
                'pph23_amount' => (float) $invoice->tax_amount,
                'ppn_amount' => (float) $invoice->vat_amount,
                'backharge_amount' => (float) $invoice->backharge_amount,
                'other_amount' => (float) $invoice->other_amount,
                'invc_due' => ($invoice->due_at) ? Carbon::parse($invoice->due_at)->format('Y-m-d') : null,
                'total_amount' => (float) $totalAmount,
                'sales_amount' => (float) $totalAmount,
            ];
        }, $invoices);
    }

    public function mapAllocations(array $allocations): array
    {
        return array_map(function ($allocation) {
            return (object) [
                'id' => $allocation->bank_id,
                'owner' => $allocation->owner,
                'provider' => $allocation->provider,
                'account_number' => $allocation->account_no,
                'is_default' => ($allocation->is_default) ? 'Y' : 'N',
                'amount' => (float) $allocation->amount,
                'notes' => (string) $allocation->notes,
            ];
        }, $allocations);
    }

    public function mapOtherDocuments(array $documents): array
    {
        return array_map(function ($document) {
            return (object) [
                'pendukung_name' => $document->origin_name,
                'pendukung_path' => config('image-path.plafond.disbursement.other_document'),
            ];
        }, $documents);
    }
}
