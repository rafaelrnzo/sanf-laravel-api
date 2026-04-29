<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use NbsPhp\Core\Models\UserStatusModel;
use Sanf\Core\Modules\User\Enums\OTPPurposeEnum;
use Sanf\Core\Modules\User\Exceptions\OTPPurposeInvalidException;
use Sanf\Core\Modules\User\Exceptions\OTPInvalidException;
use Sanf\Core\Modules\User\Exceptions\OTPSuspendedException;
use Sanf\Core\Modules\User\Repositories\RegistrationOTPRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use NbsPhp\Core\Services\ApplicationServiceInterface;

class VerifyRegistrationOTPService implements ApplicationServiceInterface
{
    protected const MAX_VERIFY_ATTEMPTS = 3;
    protected const SUSPEND_HOURS = 24;

    protected $otpRepository;
    protected $userRepository;

    public function __construct(
        RegistrationOTPRepositoryInterface $otpRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->otpRepository = $otpRepository;
        $this->userRepository = $userRepository;
    }

    public function execute($dto = null)
    {
        $userId = $dto->userId;
        $purpose = $dto->purpose ?? OTPPurposeEnum::REGISTRATION;
        $code = $dto->code;

        if (!OTPPurposeEnum::isValid($purpose)) {
            throw new OTPPurposeInvalidException();
        }

        $now = Carbon::now();
        $otpRecord = $this->otpRepository->findLatestActive($userId, $purpose);

        if (!$otpRecord) {
            throw new OTPInvalidException();
        }

        if ($otpRecord->suspend_end_at && $otpRecord->suspend_end_at > $now) {
            throw new OTPSuspendedException();
        }

        return DB::transaction(function () use ($userId, $otpRecord, $code, $now, $purpose) {
            if (Hash::check($code, $otpRecord->code)) {
                $this->otpRepository->update($otpRecord->id, [
                    'is_used' => true,
                    'updated_at' => $now,
                ]);

                if ($purpose === OTPPurposeEnum::REGISTRATION) {
                    $this->userRepository->update($userId, [
                        'email_verified_at' => $now,
                        'status_id' => UserStatusModel::STATUS_ACTIVE,
                    ]);
                }

                return true;
            }

            $newAttempts = $otpRecord->verify_attempt + 1;
            $updateData = ['verify_attempt' => $newAttempts];

            if ($newAttempts >= self::MAX_VERIFY_ATTEMPTS) {
                $updateData['suspend_end_at'] = $now->copy()->addHours(self::SUSPEND_HOURS);
                $this->otpRepository->update($otpRecord->id, $updateData);
                throw new OTPSuspendedException();
            }

            $this->otpRepository->update($otpRecord->id, $updateData);
            throw new OTPInvalidException();
        });
    }
}
