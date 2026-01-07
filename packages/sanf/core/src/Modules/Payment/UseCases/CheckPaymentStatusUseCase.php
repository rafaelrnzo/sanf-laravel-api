<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\ConcurrentModificationException;
use Sanf\Api\Modules\Payment\Support\MidtransPaymentMethodResolver;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Payment\Entities\PaymentInstallmentSnapshotEntity;
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

        $data->load(['midtransTransaction', 'installments']);

        $midtransTransaction = $data->midtransTransaction;

        if (optional($midtransTransaction)->midtrans_order_id) {
            $statusResponse = $this->midtransClient->getTransactionStatus($midtransTransaction->midtrans_order_id);

            if ($statusResponse && $this->shouldUpdateMidtransTransaction($midtransTransaction, $statusResponse)) {
                $this->updateMidtransTransaction($midtransTransaction, $statusResponse);

                $this->updatePaymentStatus($data, $statusResponse);

                $data->status = $this->mapMidtransStatusToPaymentStatus($statusResponse->transaction_status, $statusResponse->fraud_status);

                $this->updateInstallmentsStatus($data);

                if ($data->status === PaymentStatusEnum::SUCCESS) {
                    $this->sanfCorePayInstallment($data, $midtransTransaction, $statusResponse);
                }
            }
        }

        return $data->status;
    }

    private function updatePaymentStatus(
        PaymentModel $payment,
        MidtransTransactionStatusResponse $statusResponse
    ): void {
        $paymentStatus = $this->mapMidtransStatusToPaymentStatus($statusResponse->transaction_status, $statusResponse->fraud_status);

        if ($paymentStatus === null || $paymentStatus === $payment->status) {
            return;
        }

        $payload = [
            'status' => $paymentStatus,
            'version' => $payment->version + 1,

        ];

        if ($paymentStatus === PaymentStatusEnum::SUCCESS) {
            $payload['paid_at'] = $this->determinePaidAt($statusResponse->transaction_time);
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

    private function updateInstallmentsStatus(PaymentModel $payment): void
    {
        $targetStatus = $this->mapPaymentStatusToInstallmentStatus($payment->status);

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
        if ($transaction->midtrans_transaction_id === null && $statusResponse->transaction_id !== null) {
            return true;
        }

        if ($statusResponse->transaction_status !== null && $transaction->transaction_status !== $statusResponse->transaction_status) {
            return true;
        }

        return false;
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

    private function determinePaidAt(?string $transactionTime): Carbon
    {
        if ($transactionTime === null) {
            return Carbon::now();
        }

        return Carbon::parse($transactionTime);
    }

    private function parseGrossAmount(?string $grossAmount): ?float
    {
        if ($grossAmount === null) {
            return null;
        }

        return (float) $grossAmount;
    }

    private function mapMidtransStatusToPaymentStatus(?string $midtransStatus, ?string $fraudStatus): ?string
    {
        if ($midtransStatus === null) {
            return null;
        }

        $normalized = strtolower($midtransStatus);

        switch ($normalized) {
            case 'capture':
                return $fraudStatus === 'accept'
                    ? PaymentStatusEnum::SUCCESS
                    : PaymentStatusEnum::PENDING;
            case 'settlement':
            case 'success':
                return PaymentStatusEnum::SUCCESS;
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
            return InstallmentStatusEnum::PAID;
        }

        if (in_array($normalized, [PaymentStatusEnum::CANCELLED, PaymentStatusEnum::EXPIRED, PaymentStatusEnum::FAILED])) {
            return InstallmentStatusEnum::ACTIVE;
        }

        return null;
    }

    private function sanfCorePayInstallment($payment, $midtransTransaction, $midtransTransactionStatus)
    {
        $paymentMethod = MidtransPaymentMethodResolver::resolve($midtransTransactionStatus->raw);

        $payInstallmentPayload = new SanfCorePayInstallmentPayload([
            'id_transaksi' => $midtransTransaction->midtrans_order_id,
            'status_pembayaran' => 'PAID',
            'tgl_pembayaran' => Carbon::make($midtransTransactionStatus->transaction_time)->format('Y-m-d H:i:s'),
            'metode_bayar' => 'VA', // only virtual account for now
            'bank' => $paymentMethod->provider,
            'nomor_va' => $paymentMethod->virtual_account_number,
            'total_bayar' => $payment->amount,
            'detail_pembayaran' => $payment->installments->map(function (InstallmentModel $installment) use ($payment) {
                /** @var PaymentInstallmentSnapshotEntity */
                $installmentSnapshot = $installment->pivot->installment_snapshot;

                return new SanfCoreInstallmentPaymentItem([
                    'no_kontrak' => $installmentSnapshot->contract_no,
                    'cust_id' => $payment->user_profile_xid,
                    'due_date' => Carbon::make($installmentSnapshot->due_date)->setTimezone(SanfCoreApiClientV2::DEFAULT_TIMEZONE)->format('Y-m-d'),
                    'schedule_no' => $installmentSnapshot->sequence_no,
                    'amount_tagihan' => $installmentSnapshot->principal_loan,
                    'amount_pinalty' => $installmentSnapshot->penalty_fee,
                    'total_pembayaran' => $installmentSnapshot->total_amount,
                ]);
            })->toArray(),
        ]);

        $this->sanfCoreApiClient->payInstallment($payInstallmentPayload);
    }
}
