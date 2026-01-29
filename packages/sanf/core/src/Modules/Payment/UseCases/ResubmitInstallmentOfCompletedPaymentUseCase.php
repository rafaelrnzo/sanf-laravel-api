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
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Exceptions\PaymentInstallmentAlreadySubmittedException;
use Sanf\Core\Modules\Payment\Exceptions\PaymentStatusInvalidException;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCoreInstallmentPaymentItem;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCorePayInstallmentPayload;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class ResubmitInstallmentOfCompletedPaymentUseCase
{
    protected PaymentRepositoryInterface $paymentRepository;
    protected InstallmentRepositoryInterface $installmentRepository;
    protected SanfCoreApiClientV2 $sanfCoreApiClient;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        InstallmentRepositoryInterface $installmentRepository,
        SanfCoreApiClientV2 $sanfCoreApiClient
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->installmentRepository = $installmentRepository;
        $this->sanfCoreApiClient = $sanfCoreApiClient;
    }

    public function execute(string $paymentXid)
    {
        $payment = $this->paymentRepository->find(['xid' => $paymentXid]);

        if (!$payment) {
            throw new ResourceNotFoundException('Payment not found');
        }

        if ($payment->status !== PaymentStatusEnum::SUCCESS) {
            throw new PaymentStatusInvalidException();
        }

        if ($payment->core_installment_submitted) {
            throw new PaymentInstallmentAlreadySubmittedException();
        }

        $this->updatePaymentFlag($payment);

        $this->updateInstallmentsStatus($payment);

        $this->sanfCorePayInstallment($payment);
    }

    private function sanfCorePayInstallment(PaymentModel $payment)
    {
        $midtransTransaction = $payment->midtransTransaction;
        $paymentMethod = MidtransPaymentMethodResolver::resolve($midtransTransaction->raw_response);
        $paymentDetail = $payment->payment_detail;

        $mandiriBill = $paymentMethod->biller_code . $paymentMethod->bill_key;
        $vaNumber = $mandiriBill ?: $paymentMethod->virtual_account_number;

        $customAmount = $paymentDetail->custom_amount ?? 0;
        $customPenaltyAmount = $paymentDetail->custom_penalty_amount ?? 0;
        $isPaymentCustomize = $payment->installments->count() === 1 && ($customAmount > 0 || $customPenaltyAmount > 0);

        $payInstallmentPayload = new SanfCorePayInstallmentPayload([
            'id_transaksi' => $midtransTransaction->midtrans_order_id,
            'status_pembayaran' => 'PAID',
            'tgl_pembayaran' => $midtransTransaction->transaction_time->format('Y-m-d H:i:s'),
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

    private function updatePaymentFlag(PaymentModel $payment)
    {
        $this->paymentRepository->updatePayment(
            ['id' => $payment->id],
            ['core_installment_submitted' => true]
        );
    }

    private function updateInstallmentsStatus(PaymentModel $payment): void
    {
        $targetStatus = InstallmentStatusEnum::PAID;

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
}
