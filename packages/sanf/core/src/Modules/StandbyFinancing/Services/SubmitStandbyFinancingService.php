<?php

namespace Sanf\Core\Modules\StandbyFinancing\Services;

use Carbon\CarbonImmutable;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;
use Sanf\Core\Modules\StandbyFinancing\Dtos\CheckInvoiceRequestDto;
use Sanf\Core\Modules\StandbyFinancing\Dtos\SubmitStandbyFinancingRequestDto;
use Sanf\Core\Modules\StandbyFinancing\Enums\StandbyFinancingDocumentEnum;
use Sanf\Core\Modules\StandbyFinancing\Enums\StandbyFinancingStateEnum;
use Sanf\Core\Modules\StandbyFinancing\Exceptions\StandbyFinancingValidationException;
use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingApplicationModel;
use Sanf\Core\Modules\StandbyFinancing\Repositories\StandbyFinancingRepositoryInterface;

class SubmitStandbyFinancingService
{
    private StandbyFinancingPlafondService $plafondService;
    private StandbyFinancingBankAccountService $bankAccountService;
    private StandbyFinancingRepositoryInterface $repository;

    public function __construct(
        StandbyFinancingPlafondService $plafondService,
        StandbyFinancingBankAccountService $bankAccountService,
        StandbyFinancingRepositoryInterface $repository
    ) {
        $this->plafondService = $plafondService;
        $this->bankAccountService = $bankAccountService;
        $this->repository = $repository;
    }

    public function checkInvoice(string $customerId, CheckInvoiceRequestDto $dto): array
    {
        $invoiceNo = $dto->nomorInvoice;
        $totalInvoice = $dto->totalInvoice;
        $noPlafond = $dto->noplafond;

        if (empty($invoiceNo) || empty($noPlafond) || $totalInvoice <= 0) {
            throw new StandbyFinancingValidationException('Invoice payload is invalid.');
        }

        $plafond = $this->plafondService->validateActiveSbf($customerId, $noPlafond);
        $this->assertInvoiceAvailable($invoiceNo);
        $this->assertAmountWithinAvailablePlafond($plafond, $totalInvoice);

        return [
            'nomor_invoice' => $invoiceNo,
            'total_invoice' => $totalInvoice,
            'noplafond' => is_numeric($noPlafond) ? (int) $noPlafond : $noPlafond,
        ];
    }

    public function submit(string $customerId, SubmitStandbyFinancingRequestDto $dto, array $actor): StandbyFinancingApplicationModel
    {
        $noPlafond = $dto->noPlafond;
        if (empty($noPlafond)) {
            throw new StandbyFinancingValidationException('No plafond is required.');
        }

        $plafond = $this->plafondService->validateActiveSbf($customerId, $noPlafond);
        $supplierPayload = $this->singleSupplier($dto->supplier);
        $supplier = $this->plafondService->findSupplier($plafond, (string) $supplierPayload['supplier_id']);
        $invoicePayloads = $supplierPayload['invoice_list'] ?? [];
        $bankAccountPayload = $dto->bankAccount;
        $documentPayloads = $dto->dokuments ?? $dto->documents ?? [];
        $tenor = $dto->tenor;
        $totalAmount = (float) ($supplierPayload['total_amount'] ?? 0);
        $totalInvoice = (int) ($supplierPayload['total_invoice'] ?? count($invoicePayloads));

        $this->assertTenor($tenor, $supplier);
        $this->assertInvoices($invoicePayloads, $supplier, $totalAmount, $totalInvoice);
        $this->assertBankAccount($customerId, $bankAccountPayload, $totalAmount);
        $this->assertDocuments($documentPayloads);
        $this->assertAmountWithinAvailablePlafond($plafond, $totalAmount);

        $recapId = $this->generateRecapId();
        $now = CarbonImmutable::now();

        return $this->repository->createSubmission([
            'recap_id_b2b' => $recapId,
            'cust_id' => $customerId,
            'no_plafond' => $noPlafond,
            'p_code' => PlafondTypeEnum::SBF,
            'supplier_id' => (string) $supplierPayload['supplier_id'],
            'supplier_name' => $supplier['nama_supplier'] ?: null,
            'total_invoice' => $totalInvoice,
            'total_amount' => $totalAmount,
            'currency' => $this->invoiceCurrency($invoicePayloads, $supplier),
            'period_start' => $dto->periodStart,
            'period_end' => $dto->periodEnd,
            'tenor' => $tenor,
            'tenor_type' => $supplier['tipe_tenor'],
            'payment_method' => $dto->paymentMethod ?? 'transfer',
            'state' => StandbyFinancingStateEnum::SUBMITTED,
            'state_code' => StandbyFinancingStateEnum::STATE_CODE_SUBMITTED,
            'source_channel' => $dto->sourceChannel ?? 'mobile-api',
            'agreement_checkbox' => (bool) ($dto->agreementCheckbox ?? false),
            'request_id' => $dto->requestId ?? null,
            'created_by_user_id' => isset($actor['id']) ? (int) $actor['id'] : null,
            'submitted_at' => $now,
        ], $this->mapInvoices($recapId, $invoicePayloads), [
            'bank_id' => (string) $bankAccountPayload['bank_id'],
            'owner' => $bankAccountPayload['owner'] ?? null,
            'provider' => $bankAccountPayload['provider'] ?? null,
            'account_number' => $bankAccountPayload['account_number'] ?? null,
            'total_amount' => (float) $bankAccountPayload['total_amount'],
            'is_default' => $bankAccountPayload['is_default'] ?? null,
        ], $this->mapDocuments($documentPayloads), $actor);
    }

    private function singleSupplier($supplierPayload): array
    {
        if (!is_array($supplierPayload) || count($supplierPayload) !== 1) {
            throw new StandbyFinancingValidationException('Exactly one supplier is required.');
        }

        return array_values($supplierPayload)[0];
    }

    private function assertInvoices(array $invoicePayloads, array $supplier, float $totalAmount, int $totalInvoice): void
    {
        if (empty($invoicePayloads) || count($invoicePayloads) !== $totalInvoice) {
            throw new StandbyFinancingValidationException('Invoice count does not match supplier total invoice.');
        }

        $sum = 0.0;
        foreach ($invoicePayloads as $invoice) {
            $invoiceNo = (string) ($invoice['nomor_invoice'] ?? '');
            $amount = (float) ($invoice['amount'] ?? 0);
            $currency = (string) ($invoice['currency'] ?? '');
            $invoiceDate = CarbonImmutable::parse($invoice['tanggal_invoice'] ?? null);

            if (empty($invoiceNo) || $amount <= 0) {
                throw new StandbyFinancingValidationException('Invoice data is invalid.');
            }
            if ($invoiceDate->gt(CarbonImmutable::today())) {
                throw new StandbyFinancingValidationException('Invoice date cannot be later than today.');
            }
            if ($currency !== $supplier['curr_id']) {
                throw new StandbyFinancingValidationException('Invoice currency does not match supplier currency.');
            }

            $this->assertInvoiceAvailable($invoiceNo);
            $sum += $amount;
        }

        $this->assertSameAmount($sum, $totalAmount, 'Invoice total amount does not match supplier total amount.');
    }

    private function assertBankAccount(string $customerId, $bankAccountPayload, float $totalAmount): void
    {
        if (!is_array($bankAccountPayload) || empty($bankAccountPayload['bank_id'])) {
            throw new StandbyFinancingValidationException('Bank account is required.');
        }

        $this->assertSameAmount((float) ($bankAccountPayload['total_amount'] ?? 0), $totalAmount, 'Bank account total amount must equal invoice total amount.');

        $accounts = $this->bankAccountService->listForCustomer($customerId);
        if (empty($accounts)) {
            return;
        }

        foreach ($accounts as $account) {
            if ($account['bank_id'] === (string) $bankAccountPayload['bank_id']) {
                return;
            }
        }

        throw new StandbyFinancingValidationException('Bank account is not registered for customer.');
    }

    private function assertDocuments(array $documents): void
    {
        foreach ($documents as $document) {
            if (($document['doc_id'] ?? null) === StandbyFinancingDocumentEnum::VALIDATION && !empty($document['file_name'])) {
                return;
            }
        }

        throw new StandbyFinancingValidationException('Required document ' . StandbyFinancingDocumentEnum::VALIDATION . ' is missing.');
    }

    private function assertTenor(int $tenor, array $supplier): void
    {
        if ($tenor < (int) $supplier['tenor_pencairan_min'] || $tenor > (int) $supplier['tenor_pencairan_max']) {
            throw new StandbyFinancingValidationException('Tenor is outside supplier allowed range.');
        }
    }

    private function assertInvoiceAvailable(string $invoiceNo): void
    {
        if ($this->repository->invoiceExistsInActiveRequest($invoiceNo, StandbyFinancingStateEnum::ACTIVE_INVOICE_STATUSES)) {
            throw new StandbyFinancingValidationException('Invoice already exists in another active request.');
        }
    }

    private function assertAmountWithinAvailablePlafond(array $plafond, float $amount): void
    {
        $available = (float) $plafond['p_sisa'] - $this->repository->sumLockedAmount($plafond['no_plafond']);
        if ($amount <= 0 || $amount > $available) {
            throw new StandbyFinancingValidationException('Total amount exceeds remaining plafond.');
        }
    }

    private function generateRecapId(): string
    {
        $yearMonth = CarbonImmutable::now()->format('ym');
        $sequence = $this->repository->countApplicationsInMonth($yearMonth) + 1;

        return 'SF' . $yearMonth . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function mapInvoices(string $recapId, array $invoicePayloads): array
    {
        return array_map(function ($invoice, $index) use ($recapId) {
            return [
                'recap_id' => $recapId . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'invoice_number' => (string) $invoice['nomor_invoice'],
                'invoice_date' => $invoice['tanggal_invoice'],
                'currency' => (string) $invoice['currency'],
                'amount' => (float) $invoice['amount'],
                'document_id' => $invoice['document_id'] ?? null,
                'invoice_status' => StandbyFinancingStateEnum::INVOICE_STATUS_SUBMITTED,
                'order_no' => $index + 1,
            ];
        }, array_values($invoicePayloads), array_keys(array_values($invoicePayloads)));
    }

    private function mapDocuments(array $documents): array
    {
        return array_map(function ($document) {
            return [
                'doc_id' => (string) $document['doc_id'],
                'doc_desc' => $document['doc_desc'] ?? null,
                'file_path' => $document['file_path'] ?? null,
                'file_name' => $document['file_name'] ?? null,
                'required' => ($document['doc_id'] ?? null) === StandbyFinancingDocumentEnum::VALIDATION,
                'status' => 'UPLOADED',
            ];
        }, $documents);
    }

    private function invoiceCurrency(array $invoicePayloads, array $supplier): string
    {
        return (string) ($invoicePayloads[0]['currency'] ?? $supplier['curr_id'] ?? 'IDR');
    }

    private function assertSameAmount(float $actual, float $expected, string $message): void
    {
        if (abs($actual - $expected) > 0.01) {
            throw new StandbyFinancingValidationException($message);
        }
    }
}
