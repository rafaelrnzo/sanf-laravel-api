<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Sanf\Core\Modules\Payment\Enums\PaymentPreviewPlatformEnum;
use Sanf\Core\Modules\Payment\Repositories\PaymentPreviewRepositoryInterface;
use Sanf\Core\Modules\Payment\Responses\PaymentPreviewInstallmentResponse;
use Sanf\Core\Modules\Payment\Responses\PaymentPreviewOutstandingInstallmentResponse;
use Sanf\Core\Modules\Payment\Responses\PaymentPreviewResponse;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentDetailOverdueEntity;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentStatusEnum;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class GetPaymentPreviewUseCase
{
    protected PaymentPreviewRepositoryInterface $repository;
    protected SanfCoreApiClientV2 $sanfCoreApiClient;

    public function __construct(
        PaymentPreviewRepositoryInterface $repository,
        SanfCoreApiClientV2 $sanfCoreApiClient
    ) {
        $this->repository = $repository;
        $this->sanfCoreApiClient = $sanfCoreApiClient;
    }

    public function execute(string $profileXid, ?float $customAmount, ?float $customPenaltyAmount): PaymentPreviewResponse
    {
        $filters = [
            'user_profile_xid' => $profileXid,
            'platform' => PaymentPreviewPlatformEnum::MOBILE,
        ];

        $data = $this->repository->find($filters);

        $installmentsResult = [];
        $coreTimeZone = SanfCoreApiClientV2::DEFAULT_TIMEZONE;
        $totalDiscount = 0;
        $adminFee = 0;
        $subtotalAllInstallments = 0;
        $totalPenaltyFee = 0;

        foreach ($data->installments as $instalment) {
            $dueDate = Carbon::make($instalment->due_date)->format('Y-m-d');

            $installmentDetail = Cache::remember(
                "CORE::TAGIHAN_DETAIL:{$profileXid},{$instalment->contract_no},{$dueDate}",
                Carbon::now()->addMinute(),
                fn () => $this->sanfCoreApiClient->getInstallmentDetail($instalment->contract_no, $dueDate)
            );

            if ($installmentDetail === null || $installmentDetail->tagihan->status_pembayaran_id == InstallmentPaymentStatusEnum::LUNAS) {
                continue;
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

            $installmentsResult[] = new PaymentPreviewInstallmentResponse([
                'contract_no' => $installmentDetail->kontrak->no_kontrak,
                'due_date' => Carbon::parse($installmentDetail->tagihan->jatuh_tempo, $coreTimeZone)->endOfDay()->timestamp,
                'total_amount' => $totalAmount,
                'subtotal_installment' => $installmentDetail->tagihan->total_tagihan,
                'principal_loan' => $installmentDetail->tagihan->pokok_hutang,
                'interest_amount' => $installmentDetail->tagihan->bunga,
                'penalty_fee' => $installmentDetail->tagihan->denda,
                'financing_type_id' => $installmentDetail->kontrak->tipe_pembayaran_id,
                'financing_type_desc' => $installmentDetail->kontrak->tipe_pembayaran_desc,
                'outstanding_installments' => array_map(
                    fn (SanfCoreInstallmentDetailOverdueEntity $item) => new PaymentPreviewOutstandingInstallmentResponse([
                                'due_date' => Carbon::parse($item->due_date, $coreTimeZone)->endOfDay()->timestamp,
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

        return new PaymentPreviewResponse([
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
