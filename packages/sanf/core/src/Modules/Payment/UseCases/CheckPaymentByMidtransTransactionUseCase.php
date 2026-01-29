<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Illuminate\Support\Carbon;
use NbsPhp\Core\Exceptions\ConcurrentModificationException;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use Sanf\Api\Modules\Payment\Support\MidtransPaymentMethodResolver;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Payment\Entities\PaymentInstallmentSnapshotEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentStatusLogItemEntity;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Exceptions\PaymentSettledException;
use Sanf\Core\Modules\Payment\Models\MidtransTransactionModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Integration\Modules\Midtrans\Enums\MidtransTransactionStatusEnum;
use Sanf\Integration\Modules\Midtrans\MidtransClient;
use Sanf\Integration\Modules\Midtrans\Responses\MidtransTransactionStatusResponse;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCoreInstallmentPaymentItem;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCorePayInstallmentPayload;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class CheckPaymentByMidtransTransactionUseCase
{
    protected PaymentRepositoryInterface $paymentRepository;
    protected MidtransClient $midtransClient;
    protected InstallmentRepositoryInterface $installmentRepository;
    protected SanfCoreApiClientV2 $sanfCoreApiClient;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        MidtransClient $midtransClient,
        InstallmentRepositoryInterface $installmentRepository,
        SanfCoreApiClientV2 $sanfCoreApiClient
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->midtransClient = $midtransClient;
        $this->installmentRepository = $installmentRepository;
        $this->sanfCoreApiClient = $sanfCoreApiClient;
    }

    public function execute(string $midtransTransactionId): PaymentModel
    {
        $midtransClientTrx = $this->midtransClient->getTransactionStatus($midtransTransactionId);

        if ($midtransClientTrx === null) {
            throw new ResourceNotFoundException('Midtrans transaction not found');
        }

        $midtransTransaction = $this->paymentRepository->findMidtransTransaction([
            'midtrans_order_id' => $midtransClientTrx->order_id,
        ]);

        $midtransTransaction->loadMissing(['payment']);

        /**
         * @var ?PaymentModel
         */
        $payment = optional($midtransTransaction)->payment;

        if ($payment === null) {
            throw new ResourceNotFoundException('Payment not found');
        }

        if (!in_array($payment->status, [PaymentStatusEnum::PENDING, PaymentStatusEnum::EXPIRE_IN_PROGRESS])) {
            throw new PaymentSettledException();
        }

        $this->updateMidtransTransaction($midtransTransaction, $midtransClientTrx);

        $paymentUpdatePayload = [];

        if ($payment->status === PaymentStatusEnum::EXPIRE_IN_PROGRESS) {
            $paymentUpdatePayload = $this->handlePaymentExpireInProgress($payment, $midtransClientTrx);
        }

        if ($payment->status === PaymentStatusEnum::PENDING) {
            $paymentUpdatePayload = $this->handlePaymentPending($payment, $midtransClientTrx);
        }

        $this->updatePayment($payment, $paymentUpdatePayload);

        $payment->refresh();

        return $payment;
    }

    private function updateMidtransTransaction(
        MidtransTransactionModel $transaction,
        MidtransTransactionStatusResponse $midtransClientTrx
    ): void {
        $updatePayload = array_filter([
            'midtrans_transaction_id' => $midtransClientTrx->transaction_id,
            'payment_type' => $midtransClientTrx->payment_type,
            'transaction_status' => $midtransClientTrx->transaction_status,
            'transaction_time' => Carbon::parse($midtransClientTrx->transaction_time, MidtransClient::TIMEZONE),
            'fraud_status' => $midtransClientTrx->fraud_status,
            'gross_amount' => $midtransClientTrx->gross_amount,
        ], static fn ($value) => $value !== null);

        $updatePayload['raw_response'] = $midtransClientTrx->raw;
        $updatePayload['version'] = $transaction->version + 1;

        $updated = $this->paymentRepository->updateMidtransTransaction(
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

    private function appendStatusLog(?array $statusLog, string $paymentStatus)
    {
        $statusLog ??= [];
        $statusLog[] = (new PaymentStatusLogItemEntity([
            'status' => $paymentStatus,
            'updated_at' => Carbon::now()->toIso8601String(),
        ]))->toArray();

        return $statusLog;
    }

    private function handlePaymentExpireInProgress(PaymentModel $payment, MidtransTransactionStatusResponse $midtransClientTrx)
    {
        $newPaymentStatus = null;

        // midtrans status expire
        if (
            in_array($midtransClientTrx->transaction_status, [
                MidtransTransactionStatusEnum::EXPIRE,
            ])
        ) {
            $newPaymentStatus = PaymentStatusEnum::EXPIRED;
        }

        // midtrans status paid
        if (
            in_array($midtransClientTrx->transaction_status, [
                MidtransTransactionStatusEnum::SETTLEMENT,
                MidtransTransactionStatusEnum::CAPTURE,
            ])
        ) {
            $newPaymentStatus = PaymentStatusEnum::PAID_LATE;
        }

        if ($newPaymentStatus === null) {
            return [];
        }

        $paidAt = null;
        if ($newPaymentStatus === PaymentStatusEnum::PAID_LATE) {
            $paidAt = Carbon::parse($midtransClientTrx->settlement_time, MidtransClient::TIMEZONE)->utc();
        }

        $paymentUpdatePayload = [
            'status' => $newPaymentStatus,
            'paid_at' => $paidAt,
            'status_log' => $this->appendStatusLog($payment->status_log, $newPaymentStatus),
        ];

        return $paymentUpdatePayload;
    }

    private function updatePayment(PaymentModel $payment, array $paymentData)
    {
        if (empty($paymentData)) {
            return;
        }

        $paymentData['version'] = $payment->version + 1;

        $paymentUpdated = $this->paymentRepository->updatePayment(
            [
                'id' => $payment->id,
                'version' => $payment->version,
            ],
            $paymentData
        );

        if (!$paymentUpdated) {
            throw new ConcurrentModificationException();
        }
    }

    private function handlePaymentPending(PaymentModel $payment, MidtransTransactionStatusResponse $midtransClientTrx)
    {
        $paymentUpdatePayload = [];

        // handle when payment pending but expired
        if (
            Carbon::now()->greaterThanOrEqualTo($payment->expired_at)
            || $midtransClientTrx->transaction_status === MidtransTransactionStatusEnum::EXPIRE
        ) {
            $newPaymentStatus = PaymentStatusEnum::EXPIRED;

            $paymentUpdatePayload = [
                'status' => $newPaymentStatus,
                'status_log' => $this->appendStatusLog($payment->status_log, $newPaymentStatus),
            ];

            $this->updateInstallmentsStatus($payment, InstallmentStatusEnum::ACTIVE);

            return $paymentUpdatePayload;
        }

        // handle when payment pending but paid
        if (
            in_array($midtransClientTrx->transaction_status, [
                MidtransTransactionStatusEnum::SETTLEMENT,
                MidtransTransactionStatusEnum::CAPTURE,
            ])
        ) {
            $newPaymentStatus = PaymentStatusEnum::SUCCESS;

            $paymentUpdatePayload = [
                'status' => $newPaymentStatus,
                'paid_at' => Carbon::parse($midtransClientTrx->transaction_time, MidtransClient::TIMEZONE),
                'status_log' => $this->appendStatusLog($payment->status_log, $newPaymentStatus),
                'core_installment_submitted' => true,
            ];

            $this->updateInstallmentsStatus($payment, InstallmentStatusEnum::IN_PROGRESS);

            try {
                $this->sanfCorePayInstallment($payment);
            } catch (\Throwable $th) {
                report($th);

                $paymentUpdatePayload['core_installment_submitted'] = false;
            }

            return $paymentUpdatePayload;
        }

        // handle when payment pending but failed
        if (
            in_array($midtransClientTrx->transaction_status, [
                MidtransTransactionStatusEnum::DENY,
                MidtransTransactionStatusEnum::CANCEL,
                MidtransTransactionStatusEnum::FAILURE,
            ])
        ) {
            $newPaymentStatus = PaymentStatusEnum::FAILED;

            $paymentUpdatePayload = [
                'status' => $newPaymentStatus,
                'status_log' => $this->appendStatusLog($payment->status_log, $newPaymentStatus),
            ];

            $this->updateInstallmentsStatus($payment, InstallmentStatusEnum::ACTIVE);

            return $paymentUpdatePayload;
        }
    }

    private function updateInstallmentsStatus(PaymentModel $payment, string $status): void
    {
        $payment->loadMissing('installments');

        foreach ($payment->installments as $installment) {
            $updated = $this->installmentRepository->update(
                [
                    'id' => $installment->id,
                    'version' => $installment->version,
                ],
                [
                    'status' => $status,
                    'version' => $installment->version + 1,
                ]
            );

            if (!$updated) {
                throw new ConcurrentModificationException();
            }
        }
    }

    private function sanfCorePayInstallment(PaymentModel $payment)
    {
        $midtransTransaction = $payment->midtransTransaction;
        $paymentMethod = MidtransPaymentMethodResolver::resolve($midtransTransaction->raw_response);
        $paymentDetail = $payment->payment_detail;

        $transactionTime = Carbon::parse($midtransTransaction->transaction_time, MidtransClient::TIMEZONE)->setTimezone(SanfCoreApiClientV2::DEFAULT_TIMEZONE);

        $mandiriBill = $paymentMethod->biller_code . $paymentMethod->bill_key;
        $vaNumber = $mandiriBill ?: $paymentMethod->virtual_account_number;

        $customAmount = $paymentDetail->custom_amount ?? 0;
        $customPenaltyAmount = $paymentDetail->custom_penalty_amount ?? 0;
        $isPaymentCustomize = $payment->installments->count() === 1 && ($customAmount > 0 || $customPenaltyAmount > 0);

        $payInstallmentPayload = new SanfCorePayInstallmentPayload([
            'id_transaksi' => $midtransTransaction->midtrans_order_id,
            'status_pembayaran' => 'PAID',
            'tgl_pembayaran' => $transactionTime->format('Y-m-d H:i:s'),
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

        $this->sanfCoreApiClient->setUser($payment->user_profile_xid);

        $this->sanfCoreApiClient->payInstallment($payInstallmentPayload);
    }
}
