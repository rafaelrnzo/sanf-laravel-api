<?php

namespace Sanf\Core\Modules\Installment\UseCases;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Payloads\FindInstallmentPayload;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Installment\Responses\FindInstallmentResponse;
use Sanf\Core\Modules\Installment\Responses\InstallmentContractResponse;
use Sanf\Core\Modules\Installment\Responses\InstallmentEStatementReponse;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentStatusEnum;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

class FindInstallmentUseCase implements ApplicationServiceInterface
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

        $kontrak = (object) ($payload->kontrak ?? []);
        $tagihan = (object) ($payload->tagihan ?? []);
        $status = $this->resolveInstallmentStatus($dto->contractNo, $dto->dueDate, $dto->profileXid, $response->tagihan->status_pembayaran_id);

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
            'interestRate' => (float) ($kontrak->bunga_harian ?? 0),
            'plafondType' => $kontrak->jenis_pembiayaan_desc ?? '',
        ]);

        return new FindInstallmentResponse([
            'totalAmount' => (float) ($tagihan->total_tagihan ?? 0),
            'dueDate' => $this->parseDate($tagihan->jatuh_tempo ?? null),
            'penaltyFee' => (float) ($tagihan->denda ?? 0),
            'principalLoan' => (float) ($tagihan->pokok_hutang ?? 0),
            'interestAmount' => (float) ($tagihan->bunga ?? 0),
            'downPayment' => (float) ($kontrak->dp_amount ?? 0),
            'paidDownPayment' => (float) ($kontrak->dp_amount ?? 0),
            'paidAmount' => (float) ($kontrak->ar_paid ?? 0),
            'status' => $status,
            'contract' => $contractDto,
            'eStatementFile' => $this->buildEStatementDto($payload->e_statement ?? null),
        ]);
    }

    private function parseDate(?string $date): ?int
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::parse($date)->timestamp;
        } catch (\Throwable $exception) {
            try {
                return Carbon::createFromFormat('d-m-Y', $date)->timestamp;
            } catch (\Throwable $exception) {
                return strtotime($date) ?: null;
            }
        }
    }

    private function resolveInstallmentStatus(?string $contractNo, $dueDate, $userProfileXid, $coreStatus): string
    {
        $dueDateString = $this->normalizeDueDate($dueDate);
        if (!$contractNo || !$dueDateString) {
            return InstallmentStatusEnum::ACTIVE;
        }

        $records = $this->installmentRepository->findByContractsAndDueDates([
            [
                'contract_no' => $contractNo,
                'due_date' => $dueDateString,
            ],
        ], $userProfileXid);

        if (!$records instanceof Collection) {
            $records = Collection::make($records ?? []);
        }

        $record = $records->first();
        if (!$record) {
            return InstallmentStatusEnum::ACTIVE;
        }

        return $this->mapStatus($coreStatus, $record->status);
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

    private function normalizeDueDate($dueDate): ?string
    {
        if (!$dueDate) {
            return null;
        }

        if ($dueDate instanceof CarbonInterface) {
            return $dueDate->toDateString();
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
}
