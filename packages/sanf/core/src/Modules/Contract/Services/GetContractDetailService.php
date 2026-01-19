<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentStatusEnum;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

class GetContractDetailService extends UserService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $internalApiClient;
    protected SanfCoreApiClientV2 $internalApiClientV2;
    protected PaymentRepositoryInterface $paymentRepository;
    protected InstallmentRepositoryInterface $installmentRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        SanfCoreApiClient $internalApiClient,
        SanfCoreApiClientV2 $internalApiClientV2,
        PaymentRepositoryInterface $paymentRepository,
        InstallmentRepositoryInterface $installmentRepository
    )
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
        $this->internalApiClientV2 = $internalApiClientV2;
        $this->paymentRepository = $paymentRepository;
        $this->installmentRepository = $installmentRepository;
    }

    /**
     * @param $dto
     * @return object
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->findById($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $data = $this->internalApiClientV2->getContractDetail($dto->contract_no);

        if (!$data) {
            throw new ResourceNotFoundException('Contract not found');
        }

        $payment = null;
        if (optional($data)->NO_KONTRAK && optional($data)->DT_DUE) {
            $payment = $this->findPayment(
                $data->NO_KONTRAK,
                $data->DT_DUE,
                $dto->user_id,
                $dto->profile_xid
            );
        }

        $installment = $this->installmentRepository->findByContract($data->NO_KONTRAK, $data->DT_DUE, $dto->profile_xid);

        return (object) [
            'contract_at' => $data->TGL_KONTRAK ?? null,
            'contract_no' => $data->NO_KONTRAK ?? null,
            'currency_type' => $data->CURR_ID ?? null,
            'status' => (object) [
                'id' => $data->STATUS_ID ?? null,
                'name' => $data->STATUS ?? null,
            ],
            'total_amount' => $data->TOTAL_PEMBIAYAAN ?? 0,
            'total_invoice_amount' => ($data->INSTALL_AMT ?? 0) + ($data->TOTAL_DENDA ?? 0),
            'total_installment_amount' => $data->INSTALL_AMT ?? 0,
            'total_installment' => $data->TENOR ?? 0,
            'total_penalty_amount' => $data->TOTAL_DENDA ?? 0,
            'total_paid_amount' => $data->TERBAYAR ?? 0,
            'total_outstanding_amount' => $data->TAGIHAN_SISA ?? 0,
            'principal_amount' => $data->POKOK_HUTANG ?? 0,
            'interest_amount' => $data->BUNGA ?? 0,
            'down_payment_amount' => $data->DP ?? 0,
            'total_bill_amount' => $data->TOTAL_TAGIHAN ?? 0,
            'due_at' => $data->DT_DUE ?? null,
            'installment_count' => $data->ANGSURAN_KE ?? 0,
            'financing' => (object) [
                'due_at' => $data->DT_DUE ?? null,
                'finished_at' => $data->TGL_SELESAI ?? null,
                'interest_percentage' => $data->RATE_EFF ?? 0,
                'facility' => (object) [
                    'id' => $data->ID_JENIS_PEMBIAYAAN ?? null,
                    'name' => $data->JENIS_PEMBIAYAAN ?? null,
                ],
                'method' => (object) [
                    'id' => $data->ID_CARA_PEMBIAYAAN ?? null,
                    'name' => $data->CARA_PEMBIAYAAN ?? null,
                ],
                'total_tenor' => $data->TENOR ?? 0,
                'type' => (object) [
                    'id' => $data->TIPE_PEMBAYARAN_ID ?? null,
                    'name' => $data->TIPE_PEMBAYARAN_DESC ?? null,
                ],
                'plafond_type' => $this->mapPalfondType($data->CONTRACT_TYPE_CODE ?? null),
                'status' => $data->STATUS_PEMBAYARAN_ID ? $this->mapInstallmentStatus($data->STATUS_PEMBAYARAN_ID, optional($installment)->status) : null,
            ],
            'total_financing_unit' => $data->TOT_UNIT ?? 0,
            'payment_xid' => optional($payment)->xid,
            'supplier_id' => $data->ID_SUPPLIER ?? null,
            'supplier_name' => $data->NAMA_SUPPLIER ?? null,
        ];
    }

    private function findPayment(string $contractNo, string $dueDate, int $userAuthId, string $userProfileXid): ?PaymentModel
    {
        return $this->paymentRepository->findByInstallmentDetail(
            $contractNo,
            $dueDate,
            [
                'user_auth_id' => $userAuthId,
                'user_profile_xid' => $userProfileXid,
            ]
        );
    }

    private function mapPalfondType(?string $plafondType)
    {
        $status = [
            'SPAREPART' => 'SPARE_PART_FINANCING',
            'FACTORING' => 'FACTORING_FINANCING',
            'GENERAL' => 'GENERAL_FINANCING',
        ];

        return $status[$plafondType] ?? $status['GENERAL'];
    }

    private function mapInstallmentStatus(string $coreStatus, ?string $dbStatus = null): ?string
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
}
