<?php

namespace Sanf\Core\Modules\Contract\Services;

use DateTime;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\RequestESignDocumentOTPDto;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentOTPThrottleException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;

class ESignDocumentOTPService implements ApplicationServiceInterface
{
    public const INIT_VERSION = 1;

    private AdInsESignDocumentOTPService $adInsOtpService;
    private EloquentESignDocumentRepository $eSignRepository;

    public function __construct(AdInsESignDocumentOTPService $adInsOtpService, EloquentESignDocumentRepository $eSignRepository)
    {
        $this->adInsOtpService = $adInsOtpService;
        $this->eSignRepository = $eSignRepository;
    }

    /**
     * @param RequestESignDocumentOTPDto $dto
     */
    public function execute($dto = null)
    {
        $otpRecord = $this->eSignRepository->findOTPRequestBySanfIdAndRefNoWhereNullCode($dto->profileXid, $dto->referenceNo);
        if ($otpRecord && $otpRecord->expired_at > (new DateTime())->format('Y-m-d H:i:s')) {
            throw new ESignDocumentOTPThrottleException();
        }

        $otpResult = $this->adInsOtpService->execute($dto);

        $currentTimestamp = new DateTime();
        if (is_null($otpRecord) === false) {
            $otpRecord = $this->eSignRepository->updateOTPRequest($otpRecord->id, [
                'expired_at' => $otpResult->expiredAt,
                'transaction_no' => $otpResult->transactionNo,
                'attempt' => $otpRecord->attempt + 1,
                'updated_at' => $currentTimestamp,
            ]);
        } else {
            $otpRecord = $this->eSignRepository->createOTPRequest([
                'xid' => nano_id(),
                'user_id' => $dto->userId,
                'sanf_id' => $dto->profileXid,
                'msisdn' => $otpResult->msisdn,
                'email' => $otpResult->email,
                'expired_at' => $otpResult->expiredAt,
                'reference_no' => $otpResult->referenceNo,
                'transaction_no' => $otpResult->transactionNo,
                'attempt' => self::INIT_VERSION,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]);
        }

        return $otpRecord;
    }
}
