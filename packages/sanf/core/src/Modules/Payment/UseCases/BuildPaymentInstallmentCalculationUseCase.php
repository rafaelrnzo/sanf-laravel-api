<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Sanf\Core\Modules\Installment\Exceptions\InstallmentNotFoundException;
use Sanf\Core\Modules\Installment\Exceptions\PaidInstallmentException;
use Sanf\Core\Modules\Payment\Exceptions\CustomPaymentUnavailableException;
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

            if (in_array($installmentDetail->tagihan->status_pembayaran_id, [InstallmentPaymentStatusEnum::LUNAS, InstallmentPaymentStatusEnum::MENUNGGU_KONFIRMASI])) {
                $e = new PaidInstallmentException();
                $e->setData(['installment' => $instalment->toArray()]);
                throw $e;
            }

            // total amount without discount
            $totalAmount = array_reduce(
                $installmentDetail->overdue ?? [],
                fn ($carry, SanfCoreInstallmentDetailOverdueEntity $item) => $carry + $this->roundUpCurrency($item->total_overdue),
                $this->roundUpCurrency($installmentDetail->tagihan->total_tagihan) + $this->roundUpCurrency($installmentDetail->tagihan->diskon)
            );

            $subtotalAllInstallments += $totalAmount;
            $totalDiscount += $this->roundUpCurrency($installmentDetail->tagihan->diskon);
            // get last admin fee
            $adminFee = $this->roundUpCurrency($installmentDetail->tagihan->admin_fee);

            $totalPenaltyFee = array_reduce(
                $installmentDetail->overdue ?? [],
                fn ($carry, SanfCoreInstallmentDetailOverdueEntity $item) => $carry + $this->roundUpCurrency($item->denda),
                $this->roundUpCurrency($installmentDetail->tagihan->denda)
            );

            $installmentsResult[] = new PaymentCalculationInstallmentResponse([
                'contract_no' => $installmentDetail->kontrak->no_kontrak,
                'due_date' => Carbon::parse($installmentDetail->tagihan->jatuh_tempo, $coreTimeZone)->timestamp,
                'total_amount' => $this->roundUpCurrency($totalAmount),
                'subtotal_installment' => $this->roundUpCurrency($installmentDetail->tagihan->total_tagihan),
                'principal_loan' => $this->roundUpCurrency($installmentDetail->tagihan->pokok_hutang),
                'interest_amount' => $this->roundUpCurrency($installmentDetail->tagihan->bunga),
                'penalty_fee' => $this->roundUpCurrency($installmentDetail->tagihan->denda),
                'financing_type_id' => $installmentDetail->kontrak->tipe_pembayaran_id,
                'financing_type_desc' => $installmentDetail->kontrak->tipe_pembayaran_desc,
                'sequence_no' => $installmentDetail->kontrak->schedule_no,
                'sequence_total' => $installmentDetail->kontrak->schedule_total,
                'outstanding_installments' => array_map(
                    fn (SanfCoreInstallmentDetailOverdueEntity $item) => new PaymentCalculationOutstandingInstallmentResponse([
                        'due_date' => Carbon::parse($item->due_date, $coreTimeZone)->timestamp,
                        'total' => $this->roundUpCurrency($item->total_overdue),
                        'principal_loan' => $this->roundUpCurrency($item->pokok_hutang),
                        'interest_amount' => $this->roundUpCurrency($item->bunga),
                        'penalty_fee' => $this->roundUpCurrency($item->denda),
                    ]),
                    $installmentDetail->overdue ?? []
                ),
            ]);
        }

        $customSubtotalInstallment = 0;
        $customAmount = $payload->customAmount;
        $customPenaltyAmount = $payload->customPenaltyAmount;

        if (($customAmount > 0 || $customPenaltyAmount > 0) && count($installmentsResult) > 1) {
            throw new CustomPaymentUnavailableException();
        }

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
            'total_payment' => $this->roundUpCurrency($totalPayment),
            'subtotal_all_installment' => $this->roundUpCurrency($subtotalAllInstallments),
            'discount' => $this->roundUpCurrency($totalDiscount),
            'admin_fee' => $this->roundUpCurrency($adminFee),
            'custom_amount' => $this->roundUpCurrency($customAmount),
            'custom_penalty_amount' => $this->roundUpCurrency($customPenaltyAmount),
            'currency' => config('payment.currency'),
            'installments' => $installmentsResult,
        ]);
    }

    private function roundUpCurrency($value): ?int
    {
        return $value !== null ? (int) ceil((float) $value) : null;
    }
}
