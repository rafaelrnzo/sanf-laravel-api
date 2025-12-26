<?php

namespace Sanf\Core\Modules\Installment\UseCases;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Payloads\BrowseInstallmentPayload;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Installment\Responses\InstallmentItemResponse;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentEntity;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentStatusEnum;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

class BrowseInstallmentUseCase
{
    protected SanfCoreApiClientV2 $apiClient;
    protected UserRepositoryInterface $userRepository;
    protected InstallmentRepositoryInterface $installmentRepository;

    public function __construct(
        SanfCoreApiClientV2 $apiClient,
        UserRepositoryInterface $userRepository,
        InstallmentRepositoryInterface $installmentRepository
    ) {
        $this->apiClient = $apiClient;
        $this->userRepository = $userRepository;
        $this->installmentRepository = $installmentRepository;
    }

    public function execute(BrowseInstallmentPayload $dto)
    {
        $user = $this->userRepository->find([
            'id' => $dto->userId,
            'xid' => $dto->profileXid,
        ]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $page = intdiv($dto->skip, $dto->limit) + 1;
        $perPage = $dto->limit;
        $periodType = $this->normalizePeriodType($dto->periodType ?? 'current_month');
        $sortBy = $this->normalizeSortBy($dto->sortBy ?? 'due_date_latest');

        $response = $this->apiClient->getInstallmentList($page, $perPage, $periodType, $sortBy);

        $installmentLookup = $this->buildInstallmentLookup($response->data ?? [], $dto->profileXid);

        $items = $response->data ?? [];
        if (!is_array($items)) {
            $items = [];
        }

        $data = array_values(array_filter(array_map(function (SanfCoreInstallmentEntity $item) use ($installmentLookup) {
            $contractNo = $item->no_kontrak ?? $item->NO_KONTRAK ?? null;
            $dueDateRaw = $item->jatuh_tempo ?? $item->JATUH_TEMPO ?? null;
            $lookupKey = $this->buildLookupKey($contractNo, $dueDateRaw);
            $lookup = $lookupKey && isset($installmentLookup[$lookupKey])
                ? $installmentLookup[$lookupKey]
                : null;
            $status = $this->mapStatus($item->status_pembayaran_id, $lookup['status'] ?? null);
            $paymentXid = $lookup['payment_xid'] ?? null;
            $sequenceNumber = $item->schedule_no;
            $sequenceTotal = $item->schedule_total;

            if ($contractNo === null && $dueDateRaw === null) {
                return null;
            }

            return new InstallmentItemResponse([
                'contract_no' => $contractNo,
                'financing_type_id' => $item->tipe_pembayaran_id ?? $item->TIPE_PEMBAYARAN_ID ?? null,
                'financing_type_description' => $item->tipe_pembayaran_desc ?? $item->TIPE_PEMBAYARAN_DESC ?? null,
                'total_amount' => isset($item->total_tagihan)
                    ? (float) $item->total_tagihan
                    : (isset($item->TOTAL_TAGIHAN) ? (float) $item->TOTAL_TAGIHAN : null),
                'due_date' => $dueDateRaw,
                'status' => $status,
                'payment_xid' => $paymentXid,
                'sequence_number' => $sequenceNumber,
                'sequence_total' => $sequenceTotal,
            ]);
        }, $items)));

        $total = count($data);
        $data = array_slice($data, $dto->skip ?? 0, $dto->limit ?? $total);
        $data = array_values($data);

        $paginate = (object) [
            'total' => (int) ($response->total ?? $response->count ?? $total),
            'count' => count($data),
            'skip' => (int) $dto->skip,
            'limit' => (int) $dto->limit,
            'sort_by' => $dto->sortBy ?? 'due_date_latest',
        ];

        return (object) [
            'data' => $data,
            'paginate' => $paginate,
        ];
    }

    private function normalizePeriodType(?string $periodType): string
    {
        $periodType = strtolower((string) $periodType);

        return in_array($periodType, ['next_month', 'current_month'], true)
            ? $periodType
            : 'current_month';
    }

    private function normalizeSortBy(?string $sortBy): string
    {
        $sortBy = strtolower((string) $sortBy);

        return in_array($sortBy, ['due_date_oldest', 'due_date_latest'], true)
            ? $sortBy
            : 'due_date_latest';
    }

    private function buildInstallmentLookup(array $items, $userProfileXid): array
    {
        if (empty($items)) {
            return [];
        }

        $contractDueDates = [];
        foreach ($items as $item) {
            $contractNo = $item->no_kontrak ?? $item->NO_KONTRAK ?? null;
            $dueDateRaw = $item->jatuh_tempo ?? $item->JATUH_TEMPO ?? null;
            $dueDateString = $this->normalizeDueDate($dueDateRaw);
            if (!$contractNo || !$dueDateString) {
                continue;
            }

            $key = $this->buildLookupKeyFromParts($contractNo, $dueDateString);
            $contractDueDates[$key] = [
                'contract_no' => $contractNo,
                'due_date' => $dueDateString,
            ];
        }

        if (empty($contractDueDates)) {
            return [];
        }

        $collection = $this->installmentRepository->findByContractsAndDueDates(array_values($contractDueDates), $userProfileXid);

        if (!$collection instanceof Collection) {
            $collection = Collection::make($collection ?? []);
        }

        $sequenceTotals = [];
        foreach ($collection as $installment) {
            $contractNo = $installment->contract_no ?? null;
            if (!$contractNo) {
                continue;
            }

            $sequenceNumber = $installment->sequence_number ?? null;
            if ($sequenceNumber === null) {
                continue;
            }

            $sequenceNumber = (int) $sequenceNumber;
            if (!isset($sequenceTotals[$contractNo]) || $sequenceNumber > $sequenceTotals[$contractNo]) {
                $sequenceTotals[$contractNo] = $sequenceNumber;
            }
        }

        $lookup = [];
        foreach ($collection as $installment) {
            $dueDateString = $this->normalizeDueDate($installment->due_date ?? null);
            if (!$dueDateString) {
                continue;
            }

            $key = $this->buildLookupKeyFromParts($installment->contract_no, $dueDateString);
            if (!$key) {
                continue;
            }

            $payments = $installment->payments ?? null;
            $paymentXid = null;
            if ($payments instanceof Collection) {
                $firstPayment = $payments->first();
                $paymentXid = $firstPayment->xid ?? null;
            } elseif (is_array($payments)) {
                $firstPayment = reset($payments);
                if (is_object($firstPayment) && property_exists($firstPayment, 'xid')) {
                    $paymentXid = $firstPayment->xid;
                }
            }

            $lookup[$key] = [
                'status' => strtoupper((string) $installment->status) ?: InstallmentStatusEnum::ACTIVE,
                'payment_xid' => $paymentXid,
                'sequence_number' => isset($installment->sequence_number) ? (int) $installment->sequence_number : null,
                'sequence_total' => $sequenceTotals[$installment->contract_no] ?? null,
            ];
        }

        return $lookup;
    }

    private function buildLookupKey(?string $contractNo, $dueDate): ?string
    {
        $dueDateString = $this->normalizeDueDate($dueDate);
        if (!$contractNo || !$dueDateString) {
            return null;
        }

        return $this->buildLookupKeyFromParts($contractNo, $dueDateString);
    }

    private function normalizeDueDate($dueDate): ?string
    {
        if (!$dueDate) {
            return null;
        }

        if ($dueDate instanceof Carbon) {
            return $dueDate->toDateString();
        }

        try {
            return Carbon::parse($dueDate)->toDateString();
        } catch (\Throwable $exception) {
            try {
                return Carbon::createFromFormat('d-m-Y', $dueDate)->toDateString();
            } catch (\Throwable $exception) {
                return null;
            }
        }
    }

    private function buildLookupKeyFromParts(string $contractNo, string $dueDateString): string
    {
        return sprintf('%s|%s', $contractNo, $dueDateString);
    }

    private function mapStatus(string $coreStatus, ?string $dbStatus)
    {
        if ($dbStatus === InstallmentStatusEnum::WAITING_PAYMENT) {
            return $dbStatus;
        }

        if ($coreStatus === InstallmentPaymentStatusEnum::LUNAS) {
            return InstallmentStatusEnum::PAID;
        }

        return InstallmentStatusEnum::ACTIVE;
    }
}
