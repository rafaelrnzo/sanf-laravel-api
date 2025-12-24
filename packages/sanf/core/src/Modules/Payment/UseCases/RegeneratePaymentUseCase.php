<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use NbsPhp\Core\Exceptions\ConcurrentModificationException;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Exceptions\PaymentCannotBeCancelledException;
use Sanf\Core\Modules\Payment\Models\MidtransTransactionModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Integration\Modules\Midtrans\MidtransClient;
use Sanf\Integration\Modules\Midtrans\Payloads\CreateSnapTransactionPayload;
use Sanf\Integration\Modules\Midtrans\Payloads\SnapTransactionDetailsPayload;

final class RegeneratePaymentUseCase
{
    protected PaymentRepositoryInterface $repository;
    protected MidtransClient $midtransClient;

    public function __construct(
        PaymentRepositoryInterface $repository,
        MidtransClient $midtransClient
    ) {
        $this->repository = $repository;
        $this->midtransClient = $midtransClient;
    }

    public function execute(string $xid, int $userAuthId, string $userProfileXid): ?PaymentModel
    {
        $filters = [
            'xid' => $xid,
            'user_auth_id' => $userAuthId,
            'user_profile_xid' => $userProfileXid,
        ];

        $payment = $this->repository->find($filters);

        if ($payment === null) {
            return null;
        }

        $payment->load(['activeMidtransTransaction']);

        $midtransTransaction = $payment->activeMidtransTransaction;

        if (!$this->isPendingPayment($payment) || !$this->isCancelableMidtransTransaction($midtransTransaction)) {
            throw new PaymentCannotBeCancelledException();
        }

        if ($midtransTransaction !== null) {
            $this->cancelMidtransTransaction($midtransTransaction);
        }

        $this->createSnapMidtrans($payment);

        return $payment;
    }

    private function isPendingPayment(PaymentModel $payment): bool
    {
        if ($payment->status === null) {
            return false;
        }

        return strtoupper((string) $payment->status) === PaymentStatusEnum::PENDING;
    }

    private function cancelMidtransTransaction(MidtransTransactionModel $transaction): void
    {
        if ($transaction->midtrans_order_id) {
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

    private function createSnapMidtrans(PaymentModel $payment): MidtransTransactionModel
    {
        $midtransOrderId = nano_id();

        $payload = new CreateSnapTransactionPayload([
            'transaction_details' => new SnapTransactionDetailsPayload([
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) round($payment->amount),
            ]),
        ]);

        $snap = $this->midtransClient->createSnapTransaction($payload);

        return $this->repository->createMidtransTransaction([
            'midtrans_order_id' => $midtransOrderId,
            'midtrans_snap_token' => $snap->token,
            'midtrans_snap_redirect_url' => $snap->redirect_url,
            'payment_id' => $payment->id,
            'payment_xid' => $payment->xid,
            'gross_amount' => $payment->amount,
        ]);
    }

    private function isCancelableMidtransTransaction(?MidtransTransactionModel $transaction): bool
    {
        if ($transaction === null) {
            return false;
        }

        if ($transaction->transaction_status === null) {
            return false;
        }

        $normalizedStatus = strtolower($transaction->transaction_status);

        $cancelableStatuses = ['pending'];

        return in_array($normalizedStatus, $cancelableStatuses, true);
    }
}
