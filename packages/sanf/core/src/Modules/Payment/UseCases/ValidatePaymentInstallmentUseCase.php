<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Carbon\CarbonImmutable;
use Sanf\Core\Modules\Payment\Payloads\PaymentInstallmentPayload;
use Sanf\Core\Modules\Payment\Responses\ValidatePaymentInstallmentResponse;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentEntity;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentListFilterTypeEnum;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentStatusEnum;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class ValidatePaymentInstallmentUseCase
{
    protected SanfCoreApiClientV2 $sanfCoreApiClient;

    public function __construct(SanfCoreApiClientV2 $sanfCoreApiClient)
    {
        $this->sanfCoreApiClient = $sanfCoreApiClient;
    }

    /**
     * @param PaymentInstallmentPayload[] $installments
     * @return ValidatePaymentInstallmentResponse
     */
    public function execute(array $installments): ValidatePaymentInstallmentResponse
    {
        $page = 1;
        $perPage = SanfCoreApiClientV2::DEFAULT_LIMIT;

        $currentMonthList = $this->sanfCoreApiClient->getInstallmentList($page, $perPage, InstallmentListFilterTypeEnum::CURRENT_MONTH, null, InstallmentPaymentStatusEnum::BELUM_LUNAS);
        $nextMonthList = $this->sanfCoreApiClient->getInstallmentList($page, $perPage, InstallmentListFilterTypeEnum::NEXT_MONTH, null, InstallmentPaymentStatusEnum::BELUM_LUNAS);

        $currentMonthContracts = $this->groupInstallmentsByContract($currentMonthList->data ?? []);
        $nextMonthContracts = $this->groupInstallmentsByContract($nextMonthList->data ?? []);

        $providedCurrentContracts = [];
        $pendingNextInstallments = [];
        $validInstallmentIdxs = [];
        $invalidInstallmentIdxs = [];
        $unexistsInstallments = [];

        foreach ($installments as $installmentIdx => $installment) {
            $contractNumber = $installment->contract_no;
            $rawDueDate = $installment->due_date;
            $dueDate = $this->normalizeDueDate($rawDueDate);

            $contractExistsInCurrent = isset($currentMonthContracts[$contractNumber]);
            $contractExistsInNext = isset($nextMonthContracts[$contractNumber]);
            $existsInNext = $contractExistsInNext && isset($nextMonthContracts[$contractNumber][$dueDate]);
            $existsInCurrent = $contractExistsInCurrent && isset($currentMonthContracts[$contractNumber][$dueDate]);

            if ($existsInCurrent) {
                $providedCurrentContracts[$contractNumber] = true;
            }

            if ($existsInNext && $contractExistsInCurrent) {
                $pendingNextInstallments[$contractNumber][] = $installmentIdx;
            }

            if ($existsInNext || $existsInCurrent) {
                $validInstallmentIdxs[] = $installmentIdx;
            }

            if (!$existsInNext && !$existsInCurrent) {
                $unexistsInstallments[] = $installment;
            }
        }

        foreach ($pendingNextInstallments as $contractNumber => $items) {
            if (isset($providedCurrentContracts[$contractNumber])) {
                continue;
            }

            foreach ($items as $item) {
                $invalidInstallmentIdxs[] = $item;
            }
        }

        $validInstallmentIdxs = array_values(array_diff($validInstallmentIdxs, $invalidInstallmentIdxs));

        $validInstallments = [];
        $invalidInstallments = [];
        foreach ($installments as $installmentIdx => $installment) {
            if (in_array($installmentIdx, $validInstallmentIdxs, true)) {
                $validInstallments[] = $installment;
            }

            if (in_array($installmentIdx, $invalidInstallmentIdxs, true)) {
                $invalidInstallments[] = $installment;
            }
        }

        return new ValidatePaymentInstallmentResponse([
            'validInstallments' => $validInstallments,
            'invalidInstallments' => $invalidInstallments,
            'unexistsInstallments' => $unexistsInstallments,
        ]);
    }

    /**
     * @param SanfCoreInstallmentEntity[] $installments
     * @return array<string, array<string, bool>>
     */
    private function groupInstallmentsByContract(array $installments): array
    {
        $grouped = [];

        foreach ($installments as $installment) {
            $contractNumber = $installment->no_kontrak;
            $dueDate = $installment->jatuh_tempo;

            if (!$contractNumber || !$dueDate || in_array($installment->status_pembayaran_id, [InstallmentPaymentStatusEnum::LUNAS, InstallmentPaymentStatusEnum::MENUNGGU_KONFIRMASI])) {
                continue;
            }

            $grouped[$contractNumber][$dueDate] = true;
        }

        return $grouped;
    }

    private function normalizeDueDate($dueDate): ?string
    {
        if ($dueDate === null || $dueDate === '') {
            return null;
        }

        $timezoneName = SanfCoreApiClientV2::DEFAULT_TIMEZONE;
        $dateFormat = 'Y-m-d';

        return CarbonImmutable::createFromTimestamp((int) $dueDate, $timezoneName)->format($dateFormat);
    }
}
