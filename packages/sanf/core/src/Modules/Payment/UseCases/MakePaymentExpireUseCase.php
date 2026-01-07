<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Illuminate\Support\Carbon;
use NbsPhp\Core\Exceptions\ConcurrentModificationException;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Payment\Entities\PaymentStatusLogItemEntity;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Exceptions\PaymentNotExpiredException;
use Sanf\Core\Modules\Payment\Exceptions\PaymentSettledException;
use Sanf\Core\Modules\Payment\Models\MidtransTransactionModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Integration\Modules\Midtrans\Enums\MidtransTransactionStatusEnum;
use Sanf\Integration\Modules\Midtrans\MidtransClient;

final class MakePaymentExpireUseCase
{
    private PaymentRepositoryInterface $repository;
    private MidtransClient $midtransClient;
    protected InstallmentRepositoryInterface $installmentRepository;

    public function __construct(
        PaymentRepositoryInterface $repository,
        MidtransClient $midtransClient,
        InstallmentRepositoryInterface $installmentRepository
    ) {
        $this->repository = $repository;
        $this->midtransClient = $midtransClient;
        $this->installmentRepository = $installmentRepository;
    }

    /**
     * @param string $xid
     * @throws ResourceNotFoundException
     * @throws PaymentSettledException
     * @throws PaymentNotExpiredException
     * @throws ConcurrentModificationException
     * @return void
     */
    public function execute(string $xid)
    {
        $payment = $this->repository->find([
            'xid' => $xid,
        ]);

        if (!$payment) {
            throw new ResourceNotFoundException('Payment not found');
        }

        if ($payment->status !== PaymentStatusEnum::PENDING && $payment->status !== PaymentStatusEnum::EXPIRE_IN_PROGRESS) {
            throw new PaymentSettledException();
        }

        if (Carbon::now()->lessThan($payment->expired_at)) {
            throw new PaymentNotExpiredException();
        }

        $newStatus = PaymentStatusEnum::EXPIRE_IN_PROGRESS;

        if ($payment->status !== $newStatus) {
            $payment->status_log = $this->appendStatusLog($payment->status_log, $newStatus);
        }

        $updated = $this->repository->updatePayment(
            [
                'id' => $payment->id,
                'version' => $payment->version,
            ],
            [
                'status' => $newStatus,
                'status_log' => $payment->status_log,
                'version' => ++$payment->version,
            ]
        );

        if (!$updated) {
            throw new ConcurrentModificationException();
        }

        $this->cancelInstallments($payment);

        $this->midtransCheck($payment->midtransTransaction, $payment);
    }

    private function midtransCheck(MidtransTransactionModel $midtransTrx, PaymentModel $payment)
    {

        $transactionId = $midtransTrx->midtrans_transaction_id ?: $midtransTrx->midtrans_order_id;

        $midtransTrxClient = null;

        try {
            $midtransTrxClient = $this->midtransClient->getTransactionStatus($transactionId);
        } catch (\Throwable $th) {
            report($th);
        }

        $paymentStatus = null;
        $expireRaw = [];

        if ($midtransTrxClient === null) {
            $paymentStatus = PaymentStatusEnum::EXPIRED;
        } else {
            switch ($midtransTrxClient->transaction_status) {
                case MidtransTransactionStatusEnum::SETTLEMENT:
                case MidtransTransactionStatusEnum::CAPTURE:
                    $paymentStatus = PaymentStatusEnum::PAID_LATE;
                    break;

                case MidtransTransactionStatusEnum::PENDING:
                    $expireRaw = $this->midtransClient->expireTransaction($transactionId);
                    $paymentStatus = PaymentStatusEnum::EXPIRED;
                    break;

                default:
                    $paymentStatus = PaymentStatusEnum::EXPIRED;
                    break;
            }
        }

        if ($midtransTrxClient !== null) {
            $updated = $this->repository->updateMidtransTransaction(
                [
                    'id' => $midtransTrx->id,
                    'version' => $midtransTrx->version,
                ],
                [
                    'raw_response' => array_merge($midtransTrxClient->raw, $expireRaw),
                    'transaction_time' => $midtransTrxClient->transaction_time,
                    'transaction_status' => $midtransTrxClient->transaction_status,
                    'fraud_status' => $midtransTrxClient->fraud_status,
                    'version' => $midtransTrx->version + 1,
                ]
            );

            if (!$updated) {
                throw new ConcurrentModificationException();
            }
        }

        $paidAt = null;
        if ($midtransTrxClient !== null && $paymentStatus === PaymentStatusEnum::PAID_LATE) {
            $paidAt = Carbon::parse($midtransTrxClient->transaction_time, MidtransClient::TIMEZONE);
        }

        $paymentUpdated = $this->repository->updatePayment(
            [
                'id' => $payment->id,
                'version' => $payment->version,
            ],
            [
                'status' => $paymentStatus,
                'paid_at' => $paidAt,
                'status_log' => $this->appendStatusLog($payment->status_log, $paymentStatus),
                'version' => $payment->version + 1,
            ]
        );

        if (!$paymentUpdated) {
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

    private function cancelInstallments(PaymentModel $payment): void
    {
        $payment->loadMissing('installments');

        foreach ($payment->installments as $installment) {
            $updated = $this->installmentRepository->update(
                [
                    'id' => $installment->id,
                    'version' => $installment->version,
                ],
                [
                    'status' => InstallmentStatusEnum::ACTIVE,
                    'version' => $installment->version + 1,
                ]
            );

            if (!$updated) {
                throw new ConcurrentModificationException();
            }
        }
    }
}
