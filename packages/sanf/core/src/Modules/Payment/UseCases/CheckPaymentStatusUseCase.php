<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\ConcurrentModificationException;
use Sanf\Api\Modules\Payment\Support\MidtransPaymentMethodResolver;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Payment\Entities\PaymentInstallmentSnapshotEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentStatusLogItemEntity;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Models\MidtransTransactionModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Integration\Modules\Midtrans\MidtransClient;
use Sanf\Integration\Modules\Midtrans\Responses\MidtransTransactionStatusResponse;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCoreInstallmentPaymentItem;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCorePayInstallmentPayload;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class CheckPaymentStatusUseCase
{
    protected PaymentRepositoryInterface $repository;
    protected MidtransClient $midtransClient;
    protected InstallmentRepositoryInterface $installmentRepository;
    protected SanfCoreApiClientV2 $sanfCoreApiClient;

    public function __construct(
        PaymentRepositoryInterface $repository,
        MidtransClient $midtransClient,
        InstallmentRepositoryInterface $installmentRepository,
        SanfCoreApiClientV2 $sanfCoreApiClient
    ) {
        $this->repository = $repository;
        $this->midtransClient = $midtransClient;
        $this->installmentRepository = $installmentRepository;
        $this->sanfCoreApiClient = $sanfCoreApiClient;
    }

    public function execute(string $xid, int $userAuthId, string $userProfileXid): ?string
    {
        $filters = [
            'xid' => $xid,
            'user_auth_id' => $userAuthId,
            'user_profile_xid' => $userProfileXid,
        ];

        $data = $this->repository->find($filters);

        if (!$data) {
            return null;
        }

        if ($data->status !== PaymentStatusEnum::PENDING) {
            return $data->status;
        }

        $data->load(['midtransTransaction', 'installments']);

        $midtransTransaction = $data->midtransTransaction;

        $midtransTransactionId = $midtransTransaction->midtrans_transaction_id ?: $midtransTransaction->midtrans_order_id;

        $statusResponse = $this->midtransClient->getTransactionStatus($midtransTransactionId);

        if ($statusResponse === null && $this->isPaymentExpired($data->expired_at)) {
            $this->updatePaymentAsExpired($data);

            $this->updateInstallmentsStatus($data, InstallmentStatusEnum::ACTIVE);

            return PaymentStatusEnum::EXPIRED;
        }

        if ($statusResponse && $this->shouldUpdateMidtransTransaction($midtransTransaction, $statusResponse)) {
            $this->updateMidtransTransaction($midtransTransaction, $statusResponse);

            $newStatus = $this->mapMidtransStatusToPaymentStatus(
                $statusResponse->transaction_status,
                $statusResponse->fraud_status,
                $data->expired_at
            );

            $targetInstallmentStatus = $this->mapPaymentStatusToInstallmentStatus($newStatus ?? $data->status);

            $this->updateInstallmentsStatus($data, $targetInstallmentStatus);

            $installmentSubmitted = false;
            if ($newStatus === PaymentStatusEnum::SUCCESS) {
                try {
                    $this->sanfCorePayInstallment($data, $midtransTransaction, $statusResponse);

                    $installmentSubmitted = true;
                } catch (\Throwable $th) {
                    report($th);
                }
            }

            $this->updatePaymentStatus($data, $statusResponse, $installmentSubmitted);

            $data->status = $newStatus;
        }

        return $data->status;
    }

    private function updatePaymentStatus(
        PaymentModel $payment,
        MidtransTransactionStatusResponse $statusResponse,
        bool $installmentSubmitted
    ): void {
        $paymentStatus = $this->mapMidtransStatusToPaymentStatus(
            $statusResponse->transaction_status,
            $statusResponse->fraud_status,
            $payment->expired_at
        );

        if ($paymentStatus === null || $paymentStatus === $payment->status) {
            return;
        }

        $payload = [
            'status' => $paymentStatus,
            'status_log' => $this->appendStatusLog($payment->status_log, $paymentStatus),
            'version' => $payment->version + 1,
        ];

        if ($paymentStatus === PaymentStatusEnum::SUCCESS || $paymentStatus === PaymentStatusEnum::PAID_LATE) {
            $payload['paid_at'] = Carbon::parse($statusResponse->settlement_time, MidtransClient::TIMEZONE)->utc();
        }

        if ($paymentStatus === PaymentStatusEnum::SUCCESS) {
            $payload['core_installment_submitted'] = $installmentSubmitted;
        }

        $updated = $this->repository->updatePayment(
            [
                'id' => $payment->id,
                'version' => $payment->version,
            ],
            $payload
        );

        if (!$updated) {
            throw new ConcurrentModificationException();
        }
    }

    private function updatePaymentAsExpired(PaymentModel $payment)
    {
        $paymentStatus = PaymentStatusEnum::EXPIRED;

        $payload = [
            'status' => $paymentStatus,
            'status_log' => $this->appendStatusLog($payment->status_log, $paymentStatus),
            'version' => $payment->version + 1,
        ];

        $updated = $this->repository->updatePayment(
            [
                'id' => $payment->id,
                'version' => $payment->version,
            ],
            $payload
        );

        if (!$updated) {
            throw new ConcurrentModificationException();
        }
    }

    private function updateInstallmentsStatus(PaymentModel $payment, ?string $targetStatus): void
    {
        if ($targetStatus === null) {
            return;
        }

        $payment->loadMissing('installments');

        foreach ($payment->installments as $installment) {
            if ($installment->status === $targetStatus) {
                continue;
            }

            $updated = $this->installmentRepository->update(
                [
                    'id' => $installment->id,
                    'version' => $installment->version,
                ],
                [
                    'status' => $targetStatus,
                    'version' => $installment->version + 1,
                ]
            );

            if (!$updated) {
                throw new ConcurrentModificationException();
            }
        }
    }

    private function shouldUpdateMidtransTransaction(
        MidtransTransactionModel $transaction,
        MidtransTransactionStatusResponse $statusResponse
    ): bool {
        if (!$statusResponse->transaction_status) {
            return false;
        }

        return $transaction->transaction_status != $statusResponse->transaction_status;
    }

    private function updateMidtransTransaction(
        MidtransTransactionModel $transaction,
        MidtransTransactionStatusResponse $statusResponse
    ): void {
        $updatePayload = array_filter([
            'midtrans_transaction_id' => $statusResponse->transaction_id,
            'payment_type' => $statusResponse->payment_type,
            'transaction_status' => $statusResponse->transaction_status,
            'transaction_time' => $this->parseTransactionTime($statusResponse->transaction_time),
            'fraud_status' => $statusResponse->fraud_status,
            'gross_amount' => $this->parseGrossAmount($statusResponse->gross_amount),
        ], static fn ($value) => $value !== null);

        $updatePayload['raw_response'] = $statusResponse->raw;
        $updatePayload['version'] = $transaction->version + 1;

        $updated = $this->repository->updateMidtransTransaction(
            [
                'id' => $transaction->id,
                'version' => $transaction->version,
            ],
            $updatePayload
        );

        if (!$updated) {
            throw new ConcurrentModificationException();
        }
    }

    private function parseTransactionTime(?string $transactionTime): ?Carbon
    {
        if ($transactionTime === null) {
            return null;
        }

        return Carbon::parse($transactionTime, MidtransClient::TIMEZONE);
    }

    private function parseGrossAmount(?string $grossAmount): ?float
    {
        if ($grossAmount === null) {
            return null;
        }

        return (float) $grossAmount;
    }

    private function mapMidtransStatusToPaymentStatus(?string $midtransStatus, ?string $fraudStatus, ?string $paymentExpiredAt): ?string
    {
        if ($midtransStatus === null) {
            return null;
        }

        $normalized = strtolower($midtransStatus);

        $paymentSuccess = $this->isPaymentExpired($paymentExpiredAt) ? PaymentStatusEnum::PAID_LATE : PaymentStatusEnum::SUCCESS;

        switch ($normalized) {
            case 'capture':
                return $fraudStatus === 'accept'
                    ? $paymentSuccess
                    : PaymentStatusEnum::PENDING;
            case 'settlement':
            case 'success':
                return $paymentSuccess;
            case 'pending':
                return PaymentStatusEnum::PENDING;
            case 'deny':
            case 'failure':
                return PaymentStatusEnum::FAILED;
            case 'expire':
            case 'expired':
                return PaymentStatusEnum::EXPIRED;
            case 'cancel':
            case 'canceled':
            case 'cancelled':
            case 'refund':
            case 'partial_refund':
                return PaymentStatusEnum::CANCELLED;
            default:
                return null;
        }
    }

    private function mapPaymentStatusToInstallmentStatus($paymentStatus): ?string
    {
        if ($paymentStatus === null) {
            return null;
        }

        $normalized = strtoupper((string) $paymentStatus);

        if ($normalized === PaymentStatusEnum::SUCCESS) {
            return InstallmentStatusEnum::IN_PROGRESS;
        }

        if ($normalized !== PaymentStatusEnum::PENDING) {
            return InstallmentStatusEnum::ACTIVE;
        }

        return null;
    }

    private function sanfCorePayInstallment($payment, $midtransTransaction, $midtransTransactionStatus)
    {
        $paymentMethod = MidtransPaymentMethodResolver::resolve($midtransTransactionStatus->raw);
        $paymentDetail = $payment->payment_detail;

        $mandiriBill = $paymentMethod->biller_code . $paymentMethod->bill_key;
        $vaNumber = $mandiriBill ?: $paymentMethod->virtual_account_number;

        $customAmount = $paymentDetail->custom_amount ?? 0;
        $customPenaltyAmount = $paymentDetail->custom_penalty_amount ?? 0;
        $isPaymentCustomize = $payment->installments->count() === 1 && ($customAmount > 0 || $customPenaltyAmount > 0);

        $payInstallmentPayload = new SanfCorePayInstallmentPayload([
            'id_transaksi' => $midtransTransaction->midtrans_order_id,
            'status_pembayaran' => 'PAID',
            'tgl_pembayaran' => Carbon::make($midtransTransactionStatus->transaction_time)->format('Y-m-d H:i:s'),
            'metode_bayar' => 'VA', // only virtual account for now
            'bank' => $paymentMethod->provider,
            'nomor_va' => $vaNumber,
            'total_bayar' => $payment->amount,
            'detail_pembayaran' => $payment->installments->map(function (InstallmentModel $installment) use ($payment, $isPaymentCustomize, $customAmount, $customPenaltyAmount) {
                /** @var PaymentInstallmentSnapshotEntity */
                $installmentSnapshot = $installment->pivot->installment_snapshot;

                return new SanfCoreInstallmentPaymentItem([
                    'no_kontrak' => $installmentSnapshot->contract_no,
                    'cust_id' => $payment->user_profile_xid,
                    'due_date' => Carbon::make($installmentSnapshot->due_date)->setTimezone(SanfCoreApiClientV2::DEFAULT_TIMEZONE)->format('Y-m-d'),
                    'schedule_no' => $installmentSnapshot->sequence_no,
                    'amount_tagihan' => $isPaymentCustomize ? $customAmount : $installmentSnapshot->principal_loan,
                    'amount_pinalty' => $isPaymentCustomize ? $customPenaltyAmount : $installmentSnapshot->penalty_fee,
                    'total_pembayaran' => $installmentSnapshot->total_amount,
                ]);
            })->toArray(),
        ]);

        $this->sanfCoreApiClient->payInstallment($payInstallmentPayload);
    }

    private function appendStatusLog(?array $statusLog, string $paymentStatus)
    {
        $statusLog ??= [];
        $statusLog[] = (new PaymentStatusLogItemEntity([
            'status' => $paymentStatus,
            'updated_at' => Carbon::now()->toIso8601String(),
        ]))->toArray();

        return $statusLog;
    }

    private function isPaymentExpired(string $expiredAt)
    {
        return Carbon::now()->greaterThanOrEqualTo($expiredAt);
    }
}
