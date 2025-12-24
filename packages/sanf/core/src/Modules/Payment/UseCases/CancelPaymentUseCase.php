<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use NbsPhp\Core\Exceptions\ConcurrentModificationException;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Exceptions\PaymentCannotBeCancelledException;
use Sanf\Core\Modules\Payment\Models\MidtransTransactionModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Integration\Modules\Midtrans\MidtransClient;

final class CancelPaymentUseCase
{
    protected PaymentRepositoryInterface $repository;
    protected MidtransClient $midtransClient;
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

    public function execute(string $xid, int $userAuthId, string $userProfileXid): ?PaymentModel
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

        $data->load(['activeMidtransTransaction', 'installments']);

        $midtransTransaction = $data->activeMidtransTransaction;

        if (!$this->isPendingPayment($data) || !$this->isCancelableMidtransTransaction($midtransTransaction)) {
            throw new PaymentCannotBeCancelledException();
        }

        $this->cancelMidtransTransaction($midtransTransaction);

        $this->cancelPayment($data);

        $this->cancelInstallments($data);

        return $data;
    }

    private function isPendingPayment(PaymentModel $payment): bool
    {
        if ($payment->status === null) {
            return false;
        }

        return strtoupper((string) $payment->status) === PaymentStatusEnum::PENDING;
    }

    private function isCancelableMidtransTransaction(MidtransTransactionModel $transaction): bool
    {
        if ($transaction->transaction_status === null) {
            return true;
        }

        $normalizedStatus = strtolower($transaction->transaction_status);

        $cancelableStatuses = ['pending'];

        return in_array($normalizedStatus, $cancelableStatuses, true);
    }

    private function cancelPayment(
        PaymentModel $payment
    ): void {
        $payload = [
            'status' => PaymentStatusEnum::CANCELLED,
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

    private function cancelMidtransTransaction(
        MidtransTransactionModel $transaction
    ): void
    {
        if ($transaction->midtrans_transaction_id) {
            $this->midtransClient->cancelTransaction($transaction->midtrans_order_id);
        }

        $updated = $this->repository->updateMidtransTransaction(
            [
                'id' => $transaction->id,
                'version' => $transaction->version,
            ],
            [
                'transaction_status' => 'cancel',
                'version' => $transaction->version + 1,
            ]
        );

        if (!$updated) {
            throw new ConcurrentModificationException();
        }
    }
}
