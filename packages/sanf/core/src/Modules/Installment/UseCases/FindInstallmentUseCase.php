<?php

namespace Sanf\Core\Modules\Installment\UseCases;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;
use Sanf\Core\Modules\Installment\Payloads\FindInstallmentPayload;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Installment\Responses\FindInstallmentResponse;
use Sanf\Core\Modules\Installment\Responses\InstallmentContractResponse;
use Sanf\Core\Modules\Installment\Responses\InstallmentEStatementReponse;
use Sanf\Core\Modules\Installment\Responses\InstallmentOutstandingResponse;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentDetailBillEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentDetailContractEntity;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentDetailOverdueEntity;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentStatusEnum;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

class FindInstallmentUseCase implements ApplicationServiceInterface
{
    protected SanfCoreApiClientV2 $apiClient;
    protected UserRepositoryInterface $userRepository;
    protected InstallmentRepositoryInterface $installmentRepository;
    protected PaymentRepositoryInterface $paymentRepository;
    private ?InstallmentModel $installment = null;

    public function __construct(
        SanfCoreApiClientV2 $apiClient,
        UserRepositoryInterface $userRepository,
        InstallmentRepositoryInterface $installmentRepository,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $this->apiClient = $apiClient;
        $this->userRepository = $userRepository;
        $this->installmentRepository = $installmentRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param FindInstallmentPayload|null $dto
     */
    public function execute($dto = null): FindInstallmentResponse
    {
        $user = $this->userRepository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        try {
            $dueDate = Carbon::createFromTimestamp($dto->dueDate, SanfCoreApiClientV2::DEFAULT_TIMEZONE)->format('Y-m-d');
            $response = $this->apiClient->getInstallmentDetail($dto->contractNo, $dueDate);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            throw $exception;
        }

        $payload = $response ?? null;
        if (!$payload) {
            throw new SanfInternalApiDataNotFoundException('Installment detail data not found');
        }

        /**
         * @var SanfCoreInstallmentDetailContractEntity
         */
        $kontrak = (object) ($payload->kontrak ?? []);

        /**
         * @var SanfCoreInstallmentDetailBillEntity
         */
        $tagihan = (object) ($payload->tagihan ?? []);

        /**
         * @var SanfCoreInstallmentDetailOverdueEntity[]
         */
        $overdue = $payload->overdue ?? [];

        $status = $this->resolveInstallmentStatus($dto->contractNo, $dto->dueDate, $dto->profileXid, $response->tagihan->status_pembayaran_id);

        $interestRate = $this->normalizeInterestRate($kontrak->bunga_harian ?? null);

        $contractDto = new InstallmentContractResponse([
            'contractNo' => $kontrak->no_kontrak ?? '',
            'contractDate' => $this->parseDate($kontrak->tanggal_kontrak ?? null),
            'supplierName' => $kontrak->nama_supplier ?? '',
            'financingTypeId' => $kontrak->tipe_pembayaran_id ?? '',
            'financingTypeDesc' => $kontrak->tipe_pembayaran_desc ?? '',
            'financingFacilityId' => $kontrak->jenis_pembiayaan_id ?? '',
            'financingFacilityDesc' => $kontrak->jenis_pembiayaan_desc ?? '',
            'financingMethodId' => $kontrak->cara_pembiayaan_id ?? '',
            'financingMethodDesc' => $kontrak->cara_pembiayaan_desc ?? '',
            'financingAmount' => (float) ($kontrak->total_pembiayaan ?? 0),
            'tenorValue' => (int) ($kontrak->tenor ?? 0),
            'tenorUnit' => $kontrak->tipe_tenor ?? '',
            'statusId' => $kontrak->status_kontrak_id ?? '',
            'statusDesc' => $kontrak->status_kontrak_desc ?? '',
            'dueDate' => $this->parseDate($kontrak->jatuh_tempo ?? null),
            'completedDate' => $this->parseDate($kontrak->tgl_selesai ?? null),
            'interestRate' => $interestRate,
            'plafondType' => 'SPARE_PART_FINANCING', // hardcoded for now
            'downPayment' => (float) ($kontrak->dp_amount ?? 0),
            'paidAmount' => (float) ($kontrak->ar_paid ?? 0),
            'outstandingAmount' => (float) ($kontrak->ar_outs ?? 0),
        ]);

        $outstandingInstallments = array_map(fn (SanfCoreInstallmentDetailOverdueEntity $item) => new InstallmentOutstandingResponse([
            'dueDate' => $this->parseDate($item->due_date),
            'total' => (int) ceil($item->total_overdue),
            'principalLoan' => (int) ceil($item->pokok_hutang),
            'interestAmount' => (int) ceil($item->bunga),
            'penaltyFee' => (int) ceil($item->denda),
        ]), $overdue);

        $installment = $this->getInstallment($dto->contractNo, $dto->dueDate, $dto->profileXid);
        $dueDateString = $this->normalizeDueDate($dto->dueDate);
        $activePayment = $dueDateString
            ? $this->paymentRepository->findActivePendingPaymentByContractAndDueDate(
                $dto->userId,
                $dto->profileXid,
                $dto->contractNo,
                $dueDateString
            )
            : null;

        $allOutstandingAmounts = array_sum(array_pluck($overdue, 'total_overdue'));

        return new FindInstallmentResponse([
            'totalAmount' => (float) (($tagihan->total_tagihan ?? 0) + $allOutstandingAmounts),
            'subtotalInstallment' => (float) ($tagihan->total_tagihan ?? 0),
            'dueDate' => $this->parseDate($tagihan->jatuh_tempo ?? null),
            'penaltyFee' => (float) ($tagihan->denda ?? 0),
            'principalLoan' => (float) ($tagihan->pokok_hutang ?? 0),
            'interestAmount' => (float) ($tagihan->bunga ?? 0),
            'sequenceNo' => (int) ($kontrak->schedule_no ?? 0),
            'sequenceTotal' => (int) ($kontrak->schedule_total ?? 0),
            'status' => $status,
            'paymentXid' => optional($activePayment)->xid,
            'contract' => $contractDto,
            'eStatementFile' => $this->buildEStatementDto($payload->e_statement ?? null),
            'outstandingInstallments' => $outstandingInstallments,
        ]);
    }

    private function parseDate(?string $date): ?int
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::parse($date, SanfCoreApiClientV2::DEFAULT_TIMEZONE)->timestamp;
        } catch (\Throwable $exception) {
            try {
                return Carbon::createFromFormat('d-m-Y', $date, SanfCoreApiClientV2::DEFAULT_TIMEZONE)->timestamp;
            } catch (\Throwable $exception) {
                return strtotime($date) ?: null;
            }
        }
    }

    private function findInstallment(?string $contractNo, $dueDate, $userProfileXid): ?InstallmentModel
    {
        $dueDateString = $this->normalizeDueDate($dueDate);
        $records = $this->installmentRepository->findByContractsAndDueDates([
            [
                'contract_no' => $contractNo,
                'due_date' => $dueDateString,
            ],
        ], $userProfileXid);

        if (!$records instanceof Collection) {
            $records = Collection::make($records ?? []);
        }

        return $records->first();
    }

    private function getInstallment(?string $contractNo, $dueDate, $userProfileXid)
    {
        return $this->installment
            ?? $this->installment = $this->findInstallment($contractNo, $dueDate, $userProfileXid);
    }

    private function resolveInstallmentStatus(?string $contractNo, $dueDate, $userProfileXid, $coreStatus): string
    {
        $dueDateString = $this->normalizeDueDate($dueDate);
        if (!$contractNo || !$dueDateString) {
            return InstallmentStatusEnum::ACTIVE;
        }

        $record = $this->getInstallment($contractNo, $dueDate, $userProfileXid);

        if (!$record) {
            return InstallmentStatusEnum::ACTIVE;
        }

        return $this->mapStatus($coreStatus, $record->status);
    }

    private function mapStatus(string $coreStatus, ?string $dbStatus)
    {
        if ($dbStatus === InstallmentStatusEnum::WAITING_PAYMENT || $dbStatus === InstallmentStatusEnum::IN_PROGRESS) {
            return $dbStatus;
        }

        if ($coreStatus === InstallmentPaymentStatusEnum::LUNAS) {
            return InstallmentStatusEnum::PAID;
        }

        if ($coreStatus === InstallmentPaymentStatusEnum::MENUNGGU_KONFIRMASI) {
            return InstallmentStatusEnum::IN_PROGRESS;
        }

        return InstallmentStatusEnum::ACTIVE;
    }

    private function normalizeDueDate($dueDate): ?string
    {
        if (!$dueDate) {
            return null;
        }

        if ($dueDate instanceof CarbonInterface) {
            return $dueDate->toDateString();
        }

        if (is_int($dueDate) || (is_string($dueDate) && ctype_digit($dueDate))) {
            return Carbon::createFromTimestamp((int) $dueDate, SanfCoreApiClientV2::DEFAULT_TIMEZONE)->toDateString();
        }

        try {
            return Carbon::parse($dueDate)->toDateString();
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function buildEStatementDto($payload): ?InstallmentEStatementReponse
    {
        if (!$payload || empty($payload->file_name)) {
            return null;
        }

        $fileName = (string) $payload->file_name;
        $filePath = (string) ($payload->file_path ?? '');
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION) ?: null;

        $normalizedPath = trim($filePath);
        if ($normalizedPath !== '' && substr($normalizedPath, -1) !== '/') {
            $normalizedPath .= '/';
        }

        $relativePath = $normalizedPath === '' ? $fileName : $normalizedPath . ltrim($fileName, '/');
        $fullUrl = file_get_temp_url($relativePath);

        return new InstallmentEStatementReponse([
            'fileName' => $fileName,
            'fileType' => $fileType,
            'url' => $fullUrl,
        ]);
    }

    private function findLatestPayment(?int $installmentId): ?PaymentModel
    {
        return $installmentId
            ? $this->paymentRepository->findLatestInsallmentPayment($installmentId, [
                'status' => PaymentStatusEnum::PENDING,
            ])
            : null;
    }

    private function normalizeInterestRate($rate): float
    {
        if ($rate === null) {
            return 0.0;
        }

        $normalizedRate = trim((string) $rate);
        $containsPercent = strpos($normalizedRate, '%') !== false;
        $normalizedRate = str_replace(['%', ','], ['', '.'], $normalizedRate);

        if (!is_numeric($normalizedRate)) {
            return 0.0;
        }

        $numericRate = (float) $normalizedRate;

        if ($containsPercent || $numericRate > 1) {
            return $numericRate / 100;
        }

        return $numericRate;
    }
}
