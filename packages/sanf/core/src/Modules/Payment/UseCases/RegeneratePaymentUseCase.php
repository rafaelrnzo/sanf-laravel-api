<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Illuminate\Support\Carbon;
use NbsPhp\Core\Exceptions\ConcurrentModificationException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Exceptions\PaymentCannotBeCancelledException;
use Sanf\Core\Modules\Payment\Exceptions\UnableChangePaymentMethodException;
use Sanf\Core\Modules\Payment\Models\MidtransTransactionModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Core\Modules\User\AuthEncryptedModel;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\Midtrans\MidtransClient;
use Sanf\Integration\Modules\Midtrans\Payloads\CreateSnapTransactionPayload;
use Sanf\Integration\Modules\Midtrans\Payloads\SnapExpiryPayload;
use Sanf\Integration\Modules\Midtrans\Payloads\SnapTransactionDetailsPayload;

final class RegeneratePaymentUseCase
{
    protected PaymentRepositoryInterface $repository;
    protected MidtransClient $midtransClient;
    protected UserRepositoryInterface $userRepository;

    public function __construct(
        PaymentRepositoryInterface $repository,
        MidtransClient $midtransClient,
        UserRepositoryInterface $userRepository
    ) {
        $this->repository = $repository;
        $this->midtransClient = $midtransClient;
        $this->userRepository = $userRepository;
    }

    public function execute(string $xid, int $userAuthId, string $userProfileXid): ?PaymentModel
    {
        $user = $this->userRepository->find([
            'id' => $userAuthId,
            'xid' => $userProfileXid,
        ]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $filters = [
            'xid' => $xid,
            'user_auth_id' => $userAuthId,
            'user_profile_xid' => $userProfileXid,
        ];

        $payment = $this->repository->find($filters);

        if ($payment === null) {
            return null;
        }

        $payment->load(['midtransTransaction']);

        $midtransTransaction = $payment->midtransTransaction;

        if (!$this->isPendingPayment($payment) || !$this->isCancelableMidtransTransaction($midtransTransaction)) {
            throw new PaymentCannotBeCancelledException();
        }

        if ($midtransTransaction !== null) {
            $this->cancelMidtransTransaction($midtransTransaction);
        }

        $payment->midtransTransaction = $this->createSnapMidtrans($payment, $user);

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

        $this->repository->deleteMidtransTransaction(['id' => $transaction->id]);
    }

    private function createSnapMidtrans(PaymentModel $payment, AuthEncryptedModel $user): MidtransTransactionModel
    {
        $midtransOrderId = nano_id_alphanumeric();

        $payment->loadMissing('installments');
        $oldTransaction = $payment->midtransTransaction;
        $oldPayload = $oldTransaction->raw_payload;

        if (empty($oldPayload)) {
            throw new UnableChangePaymentMethodException('Unable to change payment method, cancel this payment and recreate.');
        }

        $payload = new CreateSnapTransactionPayload(array_merge(
            $oldPayload->toArray(),
            [
                'transaction_details' => new SnapTransactionDetailsPayload([
                    'order_id' => $midtransOrderId,
                    'gross_amount' => $oldPayload->transaction_details->gross_amount,
                ]),
                'expiry' => $this->snapExpiry($payment),
            ]
        ));

        $snap = $this->midtransClient->createSnapTransaction($payload);

        return $this->repository->createMidtransTransaction([
            'midtrans_order_id' => $midtransOrderId,
            'midtrans_snap_token' => $snap->token,
            'midtrans_snap_redirect_url' => $snap->redirect_url,
            'payment_id' => $payment->id,
            'payment_xid' => $payment->xid,
            'gross_amount' => $payment->amount,
            'raw_payload' => $payload->toArray(),
        ]);
    }

    private function isCancelableMidtransTransaction(?MidtransTransactionModel $transaction): bool
    {
        if ($transaction === null) {
            return true;
        }

        if ($transaction->transaction_status === null) {
            return true;
        }

        $normalizedStatus = strtolower($transaction->transaction_status);

        $cancelableStatuses = ['pending'];

        return in_array($normalizedStatus, $cancelableStatuses, true);
    }

    private function snapExpiry(PaymentModel $payment): SnapExpiryPayload
    {
        $startTime = Carbon::now();
        $duration = (int) config('midtrans.snap.expiry.duration', 30);
        $unit = config('midtrans.snap.expiry.unit', 'minutes');

        if ($payment->expired_at) {
            $diffInMinutes = max($startTime->diffInMinutes(Carbon::make($payment->expired_at), false), 1);
            $duration = $diffInMinutes;
            $unit = 'minutes';
        }

        return new SnapExpiryPayload([
            'start_time' => $startTime->format('Y-m-d H:i:s O'),
            'unit' => $unit,
            'duration' => $duration,
        ]);
    }
}
