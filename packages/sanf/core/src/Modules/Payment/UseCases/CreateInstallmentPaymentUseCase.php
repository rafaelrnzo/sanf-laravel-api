<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use NbsPhp\Core\Exceptions\ConcurrentModificationException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Api\Modules\Payment\Support\MidtransHelper;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Payment\Entities\PaymentDetailEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentInstallmentOutstandingSnapshotEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentInstallmentSnapshotEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentStatusLogItemEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentUserSnapshotEntity;
use Sanf\Core\Modules\Payment\Enums\PaymentCategoryEnum;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Exceptions\CustomPaymentUnavailableException;
use Sanf\Core\Modules\Payment\Exceptions\InstallmentWaitingPaymentException;
use Sanf\Core\Modules\Payment\Exceptions\PaymentNoInstallmentException;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Payloads\CreateInstallmentPaymentPayload;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Core\Modules\Payment\Responses\PaymentCalculationInstallmentResponse;
use Sanf\Core\Modules\Payment\Responses\PaymentCalculationOutstandingInstallmentResponse;
use Sanf\Core\Modules\User\AuthEncryptedModel;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\Midtrans\MidtransClient;
use Sanf\Integration\Modules\Midtrans\Payloads\CreateSnapTransactionPayload;
use Sanf\Integration\Modules\Midtrans\Payloads\SnapCallbacksPayload;
use Sanf\Integration\Modules\Midtrans\Payloads\SnapCustomerDetailsPayload;
use Sanf\Integration\Modules\Midtrans\Payloads\SnapExpiryPayload;
use Sanf\Integration\Modules\Midtrans\Payloads\SnapItemDetailPayload;
use Sanf\Integration\Modules\Midtrans\Payloads\SnapTransactionDetailsPayload;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentTypeEnum;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class CreateInstallmentPaymentUseCase
{
    protected PaymentRepositoryInterface $paymentRepository;
    protected InstallmentRepositoryInterface $installmentRepository;
    protected MidtransClient $midtransClient;
    protected UserRepositoryInterface $userRepository;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        InstallmentRepositoryInterface $installmentRepository,
        UserRepositoryInterface $userRepository,
        MidtransClient $midtransClient
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->installmentRepository = $installmentRepository;
        $this->userRepository = $userRepository;
        $this->midtransClient = $midtransClient;
    }

    public function execute(CreateInstallmentPaymentPayload $payload): PaymentModel
    {
        if (count($payload->installments) === 0) {
            throw new PaymentNoInstallmentException();
        }

        // find existing installment

        $installmentCollection = collect($payload->installments);
        $contractNums = $installmentCollection->pluck('contract_no')->toArray();
        $dueDates = $installmentCollection->pluck('due_date')->map(fn ($item) => $this->installmentDueDateDB($item))->toArray();

        $existingInstallments = $this->installmentRepository->listByContracts($contractNums, $dueDates, $payload->userProfileXid);

        $this->validateWaitingPaymentInstallment($existingInstallments, $contractNums, $dueDates);

        $existingInstallmentGroup = $existingInstallments->groupBy(fn ($item) => $this->installmentGroupKey($item->contract_no, $item->due_date));

        // prepare payment data

        $paymentDetail = new PaymentDetailEntity([
            'total_payment' => $payload->total_payment,
            'subtotal_all_installment' => $payload->subtotal_all_installment,
            'discount' => $payload->discount,
            'admin_fee' => $payload->admin_fee,
            'custom_amount' => $payload->custom_amount,
            'custom_penalty_amount' => $payload->custom_penalty_amount,
        ]);

        $paymentCategory = PaymentCategoryEnum::INSTALLMENT_BILL;

        // TODO: how to get PaymentCategoryEnum::FINAL_PAYMENT category (oustanding plafond == total installment amount)
        if (count($payload->installments) === 1) {
            $installment = (object) $payload->installments[0];

            switch ($installment->financing_type_id) {
                case InstallmentPaymentTypeEnum::HARIAN:
                    $paymentCategory = PaymentCategoryEnum::DAILY_BILL;
                    break;
                case InstallmentPaymentTypeEnum::DOWN_PAYMENT:
                    $paymentCategory = PaymentCategoryEnum::DOWN_PAYMENT_BILL;
                    break;
            }
        }

        $status = PaymentStatusEnum::PENDING;

        $user = $this->userRepository->find([
            'id' => $payload->userAuthId,
            'xid' => $payload->userProfileXid,
        ]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $userSnapshot = new PaymentUserSnapshotEntity([
            'id' => $user->id,
            'username' => $user->username,
            'full_name' => $user->full_name,
            'phone_number' => $user->phone_number,
            'xid' => $user->xid,
        ]);

        $payment = $this->paymentRepository->create([
            'xid' => nano_id_alphanumeric(),
            'user_auth_id' => $payload->userAuthId,
            'user_profile_xid' => $payload->userProfileXid,
            'amount' => $payload->total_payment,
            'currency' => $payload->currency,
            'status' => $status,
            'category' => $paymentCategory,
            'payment_detail' => $paymentDetail->toArray(),
            'expired_at' => Carbon::now()->addMinutes(config('payment.expire_in')),
            'status_log' => [
                    new PaymentStatusLogItemEntity([
                        'status' => $status,
                        'updated_at' => Carbon::now()->toIso8601String(),
                    ]),
                ],
            'user_snapshot' => $userSnapshot->toArray(),
        ]);

        $savedInstallments = $this->saveInstallments($payload->installments, $existingInstallmentGroup, $payload->userAuthId, $payload->userProfileXid);

        $this->savePaymentInstallments($payment->id, $savedInstallments);

        $this->createSnapMidtrans($payload, $payment, $user, $savedInstallments);

        $payment->load('installments', 'midtransTransaction');

        return $payment;
    }

    private function validateWaitingPaymentInstallment(Collection $existingInstallments, array $contractNums, array $dueDates)
    {
        $waitingPaymentExist = $existingInstallments->map(function ($installment) {
            $installment->due_date_iso = $installment->due_date->toIso8601String();

            return $installment;
        })
            ->whereIn('contract_no', $contractNums)
            ->whereIn('due_date_iso', $dueDates)
            ->where('status', InstallmentStatusEnum::WAITING_PAYMENT);

        if (!$waitingPaymentExist->isEmpty()) {
            $e = new InstallmentWaitingPaymentException();
            $e->setData([
                'installments' => $waitingPaymentExist->map(fn ($item) => [
                    'contract_no' => $item->contract_no,
                    'due_date' => $item->due_date->timestamp,
                ]),
            ]);

            throw $e;
        }
    }

    private function normalizeDueDate($dueDate): ?string
    {
        if ($dueDate === null || $dueDate === '') {
            return null;
        }

        $timezoneName = SanfCoreApiClientV2::DEFAULT_TIMEZONE;
        $dateFormat = 'Y-m-d';

        if (is_int($dueDate)) {
            return CarbonImmutable::createFromTimestamp($dueDate, $timezoneName)->format($dateFormat);
        }

        return CarbonImmutable::make($dueDate)->setTimezone($timezoneName)->format($dateFormat);
    }

    private function installmentGroupKey($contractNo, $dueDate)
    {
        return $contractNo . '_' . $this->normalizeDueDate($dueDate);
    }

    /**
     * @param PaymentCalculationInstallmentResponse[] $installments
     * @return array<int, PaymentCalculationInstallmentResponse>
     */
    private function saveInstallments(array $installments, Collection $existingInstallmentGroup, int $userAuthId, string $userProfileXid): array
    {
        $result = [];

        foreach ($installments as $installment) {
            $installment = (object) $installment;
            $key = $this->installmentGroupKey($installment->contract_no, $installment->due_date);

            /**
             * @var InstallmentModel|null
             */
            $existing = optional($existingInstallmentGroup->get($key))->first();

            if ($existing) {
                $updated = $this->installmentRepository->update(
                    [
                        'id' => $existing->id,
                        'version' => $existing->version,
                    ],
                    [
                        'status' => InstallmentStatusEnum::WAITING_PAYMENT,
                        'version' => $existing->version + 1,
                    ]
                );

                if (!$updated) {
                    throw new ConcurrentModificationException();
                }

                $result[$existing->id] = $installment;

                continue;
            }

            $newInstallment = $this->installmentRepository->create([
                'xid' => nano_id_alphanumeric(),
                'user_auth_id' => $userAuthId,
                'user_profile_xid' => $userProfileXid,
                'contract_no' => $installment->contract_no,
                'due_date' => $this->installmentDueDateDB($installment->due_date),
                'amount' => $installment->total_amount,
                'status' => InstallmentStatusEnum::WAITING_PAYMENT,
            ]);

            $result[$newInstallment->id] = $installment;
        }

        return $result;
    }

    private function installmentDueDateDB(int $dueDate): string
    {
        return Carbon::createFromTimestamp($dueDate)->toIso8601String();
    }

    /**
     * @param array<int, PaymentCalculationInstallmentResponse> $savedInstallments
     * @return void
     */
    private function savePaymentInstallments(int $paymentId, array $savedInstallments)
    {
        $mapInstallments = array_map(function (PaymentCalculationInstallmentResponse $item) {
            $newItem = $item->toArray();
            $newItem['due_date'] = Carbon::createFromTimestamp($item->due_date)->toIso8601String();
            $newItem['outstanding_installments'] = array_map(function (PaymentCalculationOutstandingInstallmentResponse $outstanding) {
                $newOutstanding = $outstanding->toArray();
                $newOutstanding['due_date'] = Carbon::createFromTimestamp($outstanding->due_date)->toIso8601String();

                return new PaymentInstallmentOutstandingSnapshotEntity($newOutstanding);
            }, $item->outstanding_installments ?? []);

            return ['installment_snapshot' => new PaymentInstallmentSnapshotEntity($newItem)];
        }, $savedInstallments);

        $this->paymentRepository->createInstallments(
            ['id' => $paymentId],
            $mapInstallments
        );
    }

    /**
     * @param CreateInstallmentPaymentPayload $installmentPayload
     * @param PaymentModel $payment
     * @param AuthEncryptedModel $user
     * @param array<int, PaymentCalculationInstallmentResponse> $savedInstallments
     * @return void
     */
    private function createSnapMidtrans(CreateInstallmentPaymentPayload $installmentPayload, PaymentModel $payment, AuthEncryptedModel $user, array $savedInstallments)
    {
        $midtransOrderId = nano_id_alphanumeric();
        $customAmount = ($installmentPayload->custom_amount ?? 0) + ($installmentPayload->custom_penalty_amount ?? 0);

        if ($customAmount && count($savedInstallments) > 1) {
            throw new CustomPaymentUnavailableException();
        }

        $itemDetails = array_map(fn (PaymentCalculationInstallmentResponse $item) => new SnapItemDetailPayload([
            'price' => $customAmount ?: $item->total_amount,
            'quantity' => 1,
            'name' => sprintf(
                'Installment %s (%s)',
                $item->contract_no,
                $this->normalizeDueDate($item->due_date)
            ),
            'category' => 'Installment',
        ]), array_values($savedInstallments));

        $itemDetails[] = new SnapItemDetailPayload([
            'price' => $installmentPayload->admin_fee,
            'quantity' => 1,
            'name' => 'Admin Fee',
            'category' => 'Admin Fee',
        ]);

        $payload = new CreateSnapTransactionPayload([
            'transaction_details' => new SnapTransactionDetailsPayload([
                'order_id' => $midtransOrderId,
                'gross_amount' => $installmentPayload->total_payment,
            ]),
            'enabled_payments' => MidtransHelper::getEnabledPaymentOptions(),
            'callbacks' => new SnapCallbacksPayload([
                'finish' => route('v2.payments.static-success'),
                'error' => route('v2.payments.static-failed'),
            ]),
            'customer_details' => new SnapCustomerDetailsPayload([
                'first_name' => $user->full_name,
                'email' => $user->username,
                'phone' => $user->phone_number,
            ]),
            'item_details' => $itemDetails,
            'expiry' => $this->snapExpiry($payment),
        ]);

        $snap = $this->midtransClient->createSnapTransaction($payload);

        $this->paymentRepository->createMidtransTransaction([
            'midtrans_order_id' => $midtransOrderId,
            'midtrans_snap_token' => $snap->token,
            'midtrans_snap_redirect_url' => $snap->redirect_url,
            'payment_id' => $payment->id,
            'payment_xid' => $payment->xid,
            'gross_amount' => $installmentPayload->total_payment,
            'raw_payload' => $payload->toArray(),
        ]);
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
