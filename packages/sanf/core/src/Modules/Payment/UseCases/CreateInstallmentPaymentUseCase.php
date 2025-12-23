<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use NbsPhp\Core\Exceptions\ConcurrentModificationException;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Payment\Entities\PaymentDetailEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentStatusLogItemEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentUserSnapshotEntity;
use Sanf\Core\Modules\Payment\Enums\PaymentCategoryEnum;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Exceptions\InstallmentWaitingPaymentException;
use Sanf\Core\Modules\Payment\Exceptions\PaymentNoInstallmentException;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Core\Modules\Payment\Responses\CreateInstallmentPaymentPayload;
use Sanf\Core\Modules\Payment\Responses\PaymentCalculationInstallmentResponse;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentTypeEnum;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class CreateInstallmentPaymentUseCase
{
    protected PaymentRepositoryInterface $paymentRepository;
    protected InstallmentRepositoryInterface $installmentRepository;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        InstallmentRepositoryInterface $installmentRepository
    )
    {
        $this->paymentRepository = $paymentRepository;
        $this->installmentRepository = $installmentRepository;
    }

    public function execute(CreateInstallmentPaymentPayload $payload): PaymentModel
    {
        if (count($payload->installments) === 0) {
            throw new PaymentNoInstallmentException();
        }

        // find existing installment

        $installmentCollection = collect($payload->installments);
        $contractNums = $installmentCollection->pluck('contract_no')->toArray();
        $dueDates = $installmentCollection->pluck('due_date')->map(fn ($item) => $this->normalizeDueDate($item))->toArray();

        $existingInstallments = $this->installmentRepository->listByContracts($contractNums, $dueDates);

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
            $installment = $payload->installments[0];

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

        $userSnapshot = new PaymentUserSnapshotEntity([
            'id' => null,
            'username' => null,
            'full_name' => null,
            'phone_number' => null,
            'xid' => null,
        ]);

        $payment = $this->paymentRepository->create([
            'xid' => nano_id(),
            'user_auth_id' => $payload->userAuthId,
            'user_profile_xid' => $payload->userProfileXid,
            'amount' => $payload->total_payment,
            'currency' => $payload->currency,
            'status' => $status,
            'category' => $paymentCategory,
            'payment_detail' => $paymentDetail->toArray(),
            'expired_at' => Carbon::now()->addMinutes(config('payment.expire_in')),
            'status_log' => [new PaymentStatusLogItemEntity([
                'status' => $status,
                'updated_at' => Carbon::now()->toIso8601String(),
            ])],
            'user_snapshot' => $userSnapshot->toArray(),
        ]);

        $savedInstallments = $this->saveInstallments($payload->installments, $existingInstallmentGroup);

        $this->savePaymentInstallments($payment->id, $savedInstallments);

        // create midtrans payment
        // save midtrans payment data

        $payment->load('installments');

        return $payment;
    }

    private function validateWaitingPaymentInstallment(Collection $existingInstallments, array $contractNums, array $dueDates)
    {
        $waitingPaymentExist = $existingInstallments->whereIn('contract_no', $contractNums)
            ->whereIn('due_date', $dueDates)
            ->where('status', InstallmentStatusEnum::WAITING_PAYMENT)
            ->first();

        if ($waitingPaymentExist) {
            throw new InstallmentWaitingPaymentException();
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
    private function saveInstallments(array $installments, Collection $existingInstallmentGroup): array
    {
        $result = [];

        foreach ($installments as $installment) {
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
                'xid' => nano_id(),
                'contract_no' => $installment->contract_no,
                'due_date' => Carbon::createFromTimestamp($installment->due_date)->toIso8601String(),
                'amount' => $installment->total_amount,
                'status' => InstallmentStatusEnum::WAITING_PAYMENT,
            ]);

            $result[$newInstallment->id] = $installment;
        }

        return $result;
    }

    /**
     * @param array<int, PaymentCalculationInstallmentResponse> $savedInstallments
     * @return void
     */
    private function savePaymentInstallments(int $paymentId, array $savedInstallments)
    {
        $mapInstallments = array_map(fn ($item) => ['installment_snapshot' => $item], $savedInstallments);

        $this->paymentRepository->createInstallments(
            ['id' => $paymentId],
            $mapInstallments
        );
    }
}
