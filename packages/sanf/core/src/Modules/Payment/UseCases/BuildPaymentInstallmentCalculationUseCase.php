<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Sanf\Core\Modules\Installment\Exceptions\InstallmentNotFoundException;
use Sanf\Core\Modules\Installment\Exceptions\PaidInstallmentException;
use Sanf\Core\Modules\Payment\Payloads\PaymentInstallmentCalculationPayload;
use Sanf\Core\Modules\Payment\Responses\PaymentCalculationInstallmentResponse;
use Sanf\Core\Modules\Payment\Responses\PaymentCalculationOutstandingInstallmentResponse;
use Sanf\Core\Modules\Payment\Responses\PaymentInstallmentCalculationResponse;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentDetailEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentDetailOverdueEntity;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentStatusEnum;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class BuildPaymentInstallmentCalculationUseCase
{
    protected SanfCoreApiClientV2 $sanfCoreApiClient;

    public function __construct(
        SanfCoreApiClientV2 $sanfCoreApiClient
    ) {
        $this->sanfCoreApiClient = $sanfCoreApiClient;
    }

    public function execute(PaymentInstallmentCalculationPayload $payload): PaymentInstallmentCalculationResponse
    {
        $installmentsResult = [];
        $coreTimeZone = SanfCoreApiClientV2::DEFAULT_TIMEZONE;
        $totalDiscount = 0;
        $adminFee = 0;
        $subtotalAllInstallments = 0;
        $totalPenaltyFee = 0;

        foreach ($payload->installments as $instalment) {
            $dueDate = Carbon::createFromTimestamp($instalment->due_date, $coreTimeZone)->format('Y-m-d');

            if ($payload->preferCache) {
                /**
                 * @var SanfCoreInstallmentDetailEntity|null
                 */
                $installmentDetail = Cache::remember(
                    "CORE::TAGIHAN_DETAIL:{$payload->profileXid},{$instalment->contract_no},{$dueDate}",
                    Carbon::now()->addMinute(),
                    fn () => $this->sanfCoreApiClient->getInstallmentDetail($instalment->contract_no, $dueDate)
                );
            } else {
                /**
                 * @var SanfCoreInstallmentDetailEntity|null
                 */
                $installmentDetail = $this->sanfCoreApiClient->getInstallmentDetail($instalment->contract_no, $dueDate);
            }

            if ($installmentDetail === null) {
                $e = new InstallmentNotFoundException();
                $e->setData(['installment' => $instalment->toArray()]);
                throw $e;
            }

            if ($installmentDetail->tagihan->status_pembayaran_id == InstallmentPaymentStatusEnum::LUNAS) {
                $e = new PaidInstallmentException();
                $e->setData(['installment' => $instalment->toArray()]);
                throw $e;
            }

            // total amount without discount
            $totalAmount = array_reduce(
                $installmentDetail->overdue ?? [],
                fn ($carry, SanfCoreInstallmentDetailOverdueEntity $item) => $carry + $item->total_overdue,
                $installmentDetail->tagihan->total_tagihan + $installmentDetail->tagihan->diskon
            );

            $subtotalAllInstallments += $totalAmount;
            $totalDiscount += $installmentDetail->tagihan->diskon;
            // get last admin fee
            $adminFee = $installmentDetail->tagihan->admin_fee;

            $totalPenaltyFee = array_reduce(
                $installmentDetail->overdue ?? [],
                fn ($carry, SanfCoreInstallmentDetailOverdueEntity $item) => $carry + $item->denda,
                $installmentDetail->tagihan->denda
            );

            $installmentsResult[] = new PaymentCalculationInstallmentResponse([
                'contract_no' => $installmentDetail->kontrak->no_kontrak,
                'due_date' => Carbon::parse($installmentDetail->tagihan->jatuh_tempo, $coreTimeZone)->startOfDay()->timestamp,
                'total_amount' => $totalAmount,
                'subtotal_installment' => $installmentDetail->tagihan->total_tagihan,
                'principal_loan' => $installmentDetail->tagihan->pokok_hutang,
                'interest_amount' => $installmentDetail->tagihan->bunga,
                'penalty_fee' => $installmentDetail->tagihan->denda,
                'financing_type_id' => $installmentDetail->kontrak->tipe_pembayaran_id,
                'financing_type_desc' => $installmentDetail->kontrak->tipe_pembayaran_desc,
                'sequence_no' => $installmentDetail->kontrak->schedule_no,
                'sequence_total' => $installmentDetail->kontrak->schedule_total,
                'outstanding_installments' => array_map(
                    fn (SanfCoreInstallmentDetailOverdueEntity $item) => new PaymentCalculationOutstandingInstallmentResponse([
                        'due_date' => Carbon::parse($item->due_date, $coreTimeZone)->startOfDay()->timestamp,
                        'total' => $item->total_overdue,
                        'principal_loan' => $item->pokok_hutang,
                        'interest_amount' => $item->bunga,
                        'penalty_fee' => $item->denda,
                    ]),
                    $installmentDetail->overdue ?? []
                ),
            ]);
        }

        $customSubtotalInstallment = 0;
        $customAmount = $payload->customAmount;
        $customPenaltyAmount = $payload->customPenaltyAmount;

        if (count($installmentsResult) === 1) {
            if (!empty($customPenaltyAmount)) {
                $customPenaltyAmount = min($customPenaltyAmount, $totalPenaltyFee);
            }

            if (!empty($customAmount)) {
                $customAmount = min($customAmount, $subtotalAllInstallments - ($customPenaltyAmount ?: $totalPenaltyFee));
            }

            $customSubtotalInstallment = $customAmount + $customPenaltyAmount;
        }

        $totalPayment = ($customSubtotalInstallment ?: $subtotalAllInstallments) - $totalDiscount + $adminFee;

        return new PaymentInstallmentCalculationResponse([
            'total_payment' => $totalPayment,
            'subtotal_all_installment' => $subtotalAllInstallments,
            'discount' => $totalDiscount,
            'admin_fee' => $adminFee,
            'custom_amount' => $customAmount,
            'custom_penalty_amount' => $customPenaltyAmount,
            'currency' => config('payment.currency'),
            'installments' => $installmentsResult,
        ]);
    }
}
