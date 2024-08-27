<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\RequestESignDocumentOTPDto;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;

class ESignDocumentOTPService implements ApplicationServiceInterface
{
    protected const INIT_VERSION = 1;
    protected const COOLDOWN_TIME = 3;
    protected const SUSPEND_TIME = 2;

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
        $msisdn = $this->parseMsisdnWithZeroFormat($dto->msisdn);

        $otpRecord = $this->eSignRepository->findOTPRequestBySanfIdAndRefNoWhereCodeIsNull($dto->profileXid, $dto->referenceNo);
        $now = (CarbonImmutable::now()->format('Y-m-d H:i:s'));

        if ($otpRecord && $otpRecord->expired_at > $now) {
            return $otpRecord;
        }

        if ($otpRecord && $otpRecord->cooldown_end_at > $now) {
            $otpRecord->expired_at = $otpRecord->cooldown_end_at;

            return $otpRecord;
        }

        if ($otpRecord && $otpRecord->suspend_end_at > $now) {
            $otpRecord->expired_at = $otpRecord->suspend_end_at;

            return $otpRecord;
        }

        $dto->msisdn = $msisdn;
        $otpResult = $this->adInsOtpService->execute($dto);

        $currentTimestamp = CarbonImmutable::now();
        if (is_null($otpRecord) === false) {
            $attempt = $otpRecord->attempt + 1;

            $cooldownEndAt = null;
            $suspendEndAt = null;

            if (is_null($otpRecord->cooldown_end_at) === false) {
                $cooldownEndAt = CarbonImmutable::parse($otpRecord->cooldown_end_at);
                if (is_null($otpRecord->suspend_end_at) === false) {
                    $suspendEndAt = CarbonImmutable::parse($otpRecord->suspend_end_at);
                    if ($suspendEndAt < $now) {
                        $cooldownEndAt = null;
                        $suspendEndAt = null;
                    }
                } else {
                    if ($attempt >= self::SUSPEND_TIME) {
                        $attempt = 0;
                        $suspendEndAt = $otpResult->expiredAt->addMinutes(1440); // 1440
                    }
                }
            } else {
                if ($attempt >= self::COOLDOWN_TIME) {
                    $attempt = 0;
                    $cooldownEndAt = $otpResult->expiredAt->addMinutes(5); // 5
                }
            }

            $otpRecord = $this->eSignRepository->updateOTPRequest($otpRecord->id, [
                'expired_at' => $otpResult->expiredAt,
                'transaction_no' => $otpResult->transactionNo,
                'attempt' => $attempt,
                'cooldown_end_at' => $cooldownEndAt,
                'suspend_end_at' => $suspendEndAt,
                'updated_at' => $currentTimestamp,
            ]);
        } else {
            $otpRecord = $this->eSignRepository->createOTPRequest([
                'xid' => nano_id(),
                'user_id' => $dto->userId,
                'sanf_id' => $dto->profileXid,
                'msisdn' => $msisdn,
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

    private function parseMsisdnWithZeroFormat(string $msisdn): string
    {
        $trimValue = trim($msisdn);

        if (strpos($trimValue, '+62') === 0) {
            $msisdn = '0' . substr($trimValue, 3);
        } elseif (strpos($trimValue, '62') === 0) {
            $msisdn = '0' . substr($trimValue, 2);
        }

        return $msisdn;
    }
}
