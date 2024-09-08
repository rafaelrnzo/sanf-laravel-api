<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\RequestESignDocumentOTPDto;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentEncryptedRepository;

class ESignDocumentOTPService implements ApplicationServiceInterface
{
    protected const INIT_VERSION = 1;
    protected const COOLDOWN_TIME = 3;
    protected const SUSPEND_TIME = 2;

    private AdInsESignDocumentOTPService $adInsOtpService;
    private EloquentESignDocumentEncryptedRepository $eSignRepository;

    public function __construct(AdInsESignDocumentOTPService $adInsOtpService, EloquentESignDocumentEncryptedRepository $eSignRepository)
    {
        $this->adInsOtpService = $adInsOtpService;
        $this->eSignRepository = $eSignRepository;
    }

    /**
     * @param RequestESignDocumentOTPDto $dto
     */
    public function execute($dto = null)
    {
        $currentTimestamp = CarbonImmutable::now();
        $msisdn = $this->parseMsisdnWithZeroFormat($dto->msisdn);

        $otpRecord = $this->eSignRepository->findOTPRequestBySanfIdAndRefNoWhereCodeIsNull($dto->profileXid, $dto->referenceNo);

        if (is_null($otpRecord) === false) {
            if ($otpRecord->expired_at > $currentTimestamp->format('Y-m-d H:i:s')) {
                return $otpRecord;
            }

            if ($otpRecord->cooldown_end_at > $currentTimestamp->format('Y-m-d H:i:s')) {
                $otpRecord->expired_at = $otpRecord->cooldown_end_at;

                return $otpRecord;
            }

            if ($otpRecord->suspend_end_at > $currentTimestamp->format('Y-m-d H:i:s')) {
                $otpRecord->expired_at = $otpRecord->suspend_end_at;

                return $otpRecord;
            }

            $attempt = $otpRecord->attempt;

            $cooldownEndAt = null;
            $suspendEndAt = null;
            $data = [];

            if (is_null($otpRecord->cooldown_end_at) === false) {
                $cooldownEndAt = CarbonImmutable::parse($otpRecord->cooldown_end_at);
                if (is_null($otpRecord->suspend_end_at) === false) {
                    $suspendEndAt = CarbonImmutable::parse($otpRecord->suspend_end_at);
                    if ($suspendEndAt < $currentTimestamp->format('Y-m-d H:i:s')) {
                        $cooldownEndAt = null;
                        $suspendEndAt = null;
                    }
                } else {
                    if ($attempt >= self::SUSPEND_TIME) {
                        $suspendEndAt = $currentTimestamp->addMinutes(1440); // 1440
                        $data['expired_at'] = $suspendEndAt;
                    }
                }
            } else {
                if ($attempt >= self::COOLDOWN_TIME) {
                    $cooldownEndAt = $currentTimestamp->addMinutes(5); // 5
                    $data['expired_at'] = $cooldownEndAt;
                }
            }

            if ($cooldownEndAt && $attempt == self::COOLDOWN_TIME || $suspendEndAt && $attempt == self::SUSPEND_TIME) {
                $attempt = 0;
            } else {
                $dto->msisdn = $msisdn;
                $otpResult = $this->adInsOtpService->execute($dto);

                $data['expired_at'] = $otpResult->expiredAt;
                $data['transaction_no'] = $otpResult->transactionNo;
                $attempt++;
            }

            $payload = array_merge($data, [
                'attempt' => $attempt,
                'cooldown_end_at' => $cooldownEndAt,
                'suspend_end_at' => $suspendEndAt,
                'updated_at' => $currentTimestamp,
            ]);

            $otpRecord = $this->eSignRepository->updateOTPRequest($otpRecord->id, $payload);
        } else {
            $dto->msisdn = $msisdn;
            $otpResult = $this->adInsOtpService->execute($dto);

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
