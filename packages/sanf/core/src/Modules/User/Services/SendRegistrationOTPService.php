<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Sanf\Core\Modules\User\Exceptions\OTPRateLimitedException;
use Sanf\Core\Modules\User\Exceptions\OTPSuspendedException;
use Sanf\Core\Modules\User\Notifications\SendRegistrationOTPNotification;
use Sanf\Core\Modules\User\Repositories\RegistrationOTPRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use NbsPhp\Core\Services\ApplicationServiceInterface;

class SendRegistrationOTPService implements ApplicationServiceInterface
{
    protected const MAX_SEND_ATTEMPTS = 3;
    protected const COOLDOWN_MINUTES = 5;
    protected const OTP_EXPIRY_MINUTES = 5;

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
        $purpose = $dto->purpose ?? 'registration';
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $now = Carbon::now();
        $otpRecord = $this->otpRepository->findLatestActive($userId, $purpose);

        if ($otpRecord) {
            // Check Suspension
            if ($otpRecord->suspend_end_at && $otpRecord->suspend_end_at > $now) {
                throw new OTPSuspendedException();
            }

            // Check Cooldown
            if ($otpRecord->cooldown_end_at && $otpRecord->cooldown_end_at > $now) {
                $diff = $now->diffInMinutes($otpRecord->cooldown_end_at);
                throw new OTPRateLimitedException($diff + 1);
            }

            // If reached max send attempts, set cooldown
            if ($otpRecord->send_attempt >= self::MAX_SEND_ATTEMPTS) {
                $cooldownEnd = $now->copy()->addMinutes(self::COOLDOWN_MINUTES);
                $this->otpRepository->update($otpRecord->id, [
                    'cooldown_end_at' => $cooldownEnd,
                    'send_attempt' => 0,
                ]);
                throw new OTPRateLimitedException(self::COOLDOWN_MINUTES);
            }
        }

        return DB::transaction(function () use ($userId, $purpose, $user, $now, $otpRecord) {
            // Generate 6-digit OTP
            $otpCode = (string) random_int(100000, 999999);
            $hashedCode = Hash::make($otpCode);
            $expiredAt = $now->copy()->addMinutes(self::OTP_EXPIRY_MINUTES);

            // Invalidate all existing OTPs for this user/purpose
            $this->otpRepository->deleteOthers($userId, $purpose, 0);

            $newOtp = $this->otpRepository->create([
                'user_id' => $userId,
                'email' => $user->username,
                'code' => $hashedCode,
                'purpose' => $purpose,
                'expired_at' => $expiredAt,
                'send_attempt' => ($otpRecord ? $otpRecord->send_attempt + 1 : 1),
                'is_used' => false,
            ]);

            // Send Email
            Notification::send($user, new SendRegistrationOTPNotification($otpCode));

            return $newOtp;
        });
    }
}
