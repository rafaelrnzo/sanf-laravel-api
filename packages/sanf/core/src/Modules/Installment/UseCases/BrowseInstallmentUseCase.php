<?php

namespace Sanf\Core\Modules\Installment\UseCases;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Payloads\BrowseInstallmentPayload;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Installment\Responses\InstallmentItemResponse;
use Sanf\Core\Modules\Installment\Support\InstallmentStatusMapper;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
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
        $user = $this->userRepository->findById($dto->userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $periodType = $this->normalizePeriodType($dto->periodType ?? 'current_month');
        $sortBy = $this->normalizeSortBy($dto->sortBy ?? 'due_date_latest');
        $coreTimeZone = SanfCoreApiClientV2::DEFAULT_TIMEZONE;
        $status = InstallmentPaymentStatusEnum::BELUM_LUNAS;

        $items = $this->getAllInstallmentItems($periodType, $sortBy, $status, $dto->contractNo);

        $installmentLookup = $this->buildInstallmentLookup($items, $dto->profileXid);

        $dataCollection = Collection::make($items)
            ->map(function (SanfCoreInstallmentEntity $item) use ($installmentLookup, $coreTimeZone) {
                $contractNo = $item->no_kontrak ?? $item->NO_KONTRAK ?? null;
                $dueDateRaw = $item->jatuh_tempo ?? $item->JATUH_TEMPO ?? null;
                $lookupKey = $this->buildLookupKey($contractNo, $dueDateRaw);
                $lookup = $lookupKey && isset($installmentLookup[$lookupKey])
                    ? $installmentLookup[$lookupKey]
                    : null;
                $status = InstallmentStatusMapper::map(
                    $item->status_pembayaran_id,
                    $lookup['status'] ?? null,
                    $lookup['payment'] ?? null
                );
                $paymentXid = $lookup['payment_xid'] ?? null;
                $sequenceNumber = $item->schedule_no;
                $sequenceTotal = $item->schedule_total;

                if ($contractNo === null && $dueDateRaw === null) {
                    return null;
                }

                $dueDateTimestamp = $this->parseDueDateTimestamp($dueDateRaw, $coreTimeZone);

                return new InstallmentItemResponse([
                    'contract_no' => $contractNo,
                    'financing_type_id' => $item->tipe_pembayaran_id ?? $item->TIPE_PEMBAYARAN_ID ?? null,
                    'financing_type_description' => $item->tipe_pembayaran_desc ?? $item->TIPE_PEMBAYARAN_DESC ?? null,
                    'total_amount' => isset($item->total_tagihan)
                        ? (float) $item->total_tagihan
                        : (isset($item->TOTAL_TAGIHAN) ? (float) $item->TOTAL_TAGIHAN : null),
                    'due_date' => $dueDateTimestamp,
                    'status' => $status,
                    'payment_xid' => $paymentXid,
                    'sequence_number' => $sequenceNumber,
                    'sequence_total' => $sequenceTotal,
                ]);
            })
            ->values();

        if ($dto->dueDateAfter !== null) {
            $dataCollection = $dataCollection
                ->filter(fn (InstallmentItemResponse $item) => $item->dueDate > $dto->dueDateAfter)
                ->values();
        }

        $total = $dataCollection->count();
        $skip = (int) ($dto->skip ?? 0);
        $limit = $dto->limit !== null ? (int) $dto->limit : null;
        $data = $dataCollection->slice($skip, $limit)->values()->all();

        $paginate = (object) [
            'total' => (int) $total,
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

            $payments = $installment->payments;
            $firstPayment = $payments->first();

            $paymentXid = null;
            if (optional($firstPayment)->status === PaymentStatusEnum::PENDING) {
                $paymentXid = $firstPayment->xid;
            }

            $lookup[$key] = [
                'status' => strtoupper((string) $installment->status) ?: InstallmentStatusEnum::ACTIVE,
                'payment_xid' => $paymentXid,
                'payment' => $firstPayment,
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
        $targetTimezone = SanfCoreApiClientV2::DEFAULT_TIMEZONE;

        if (!$dueDate) {
            return null;
        }

        try {
            return Carbon::parse($dueDate, $targetTimezone)->setTimezone($targetTimezone)->toDateString();
        } catch (\Throwable $exception) {
            try {
                return Carbon::createFromFormat('Y-m-d', $dueDate)->toDateString();
            } catch (\Throwable $exception) {
                return null;
            }
        }
    }

    private function parseDueDateTimestamp($dueDate, string $timezone): ?int
    {
        if (!$dueDate) {
            return null;
        }

        try {
            return Carbon::parse($dueDate, $timezone)->endOfDay()->timestamp;
        } catch (\Throwable $exception) {
            try {
                return Carbon::createFromFormat('Y-m-d', $dueDate, $timezone)->endOfDay()->timestamp;
            } catch (\Throwable $exception) {
                return null;
            }
        }
    }

    private function buildLookupKeyFromParts(string $contractNo, string $dueDateString): string
    {
        return sprintf('%s|%s', $contractNo, $dueDateString);
    }

    /**
     * @return SanfCoreInstallmentEntity[]
     */
    private function getAllInstallmentItems(
        string $periodType,
        string $sortBy,
        ?int $status,
        ?string $contractNo
    ): array {
        $page = 1;
        $perPage = SanfCoreApiClientV2::DEFAULT_LIMIT;

        $response = $this->apiClient->getInstallmentList($page, $perPage, $periodType, $sortBy, $status, $contractNo);

        return $response->data ?? [];
    }
}
