<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sanf\Core\Modules\User\Notifications\SendRegistrationOTPNotification;
use Sanf\Core\Modules\User\Enums\OTPPurposeEnum;
use Sanf\Core\Modules\User\Exceptions\OTPPurposeInvalidException;
use Sanf\Core\Modules\User\Exceptions\OTPRateLimitedException;
use Sanf\Core\Modules\User\Exceptions\OTPSuspendedException;
use Sanf\Core\Modules\User\Exceptions\ProfileNotFoundException;
use Sanf\Core\Modules\User\Repositories\RegistrationOTPRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Services\SendRegistrationOTPService;

class SendRegistrationOTPServiceTest extends PHPUnitTestCase
{
    private $dto;
    private $mockUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->dto = (object) [
            'userId' => 1,
            'purpose' => OTPPurposeEnum::REGISTRATION,
        ];

        $this->mockUser = (object) [
            'id' => 1,
            'username' => 'test@example.com',
        ];
    }

    public function testExecuteThrowsExceptionWhenPurposeIsInvalid()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $dto = (object) ['userId' => 1, 'purpose' => 'invalid_purpose'];

        $service = new SendRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(OTPPurposeInvalidException::class);
        $service->execute($dto);
    }

    public function testExecuteThrowsExceptionWhenUserNotFound()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn(null);

        $service = new SendRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(ProfileNotFoundException::class);
        $service->execute($this->dto);
    }

    public function testExecuteThrowsExceptionWhenAccountIsSuspended()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($this->mockUser);

        $suspendedOtpRecord = (object) [
            'id' => 1,
            'user_id' => 1,
            'code' => Hash::make('123456'),
            'purpose' => OTPPurposeEnum::REGISTRATION,
            'expired_at' => Carbon::now()->addMinutes(5),
            'cooldown_end_at' => null,
            'suspend_end_at' => Carbon::now()->addHours(1),
            'send_attempt' => 0,
            'is_used' => false,
        ];

        $otpRepository->expects($this->once())
            ->method('findLatestActive')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn($suspendedOtpRecord);

        $service = new SendRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(OTPSuspendedException::class);
        $service->execute($this->dto);
    }

    public function testExecuteThrowsExceptionWhenRateLimitedByCooldown()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($this->mockUser);

        $cooldownOtpRecord = (object) [
            'id' => 1,
            'user_id' => 1,
            'code' => Hash::make('123456'),
            'purpose' => OTPPurposeEnum::REGISTRATION,
            'expired_at' => Carbon::now()->addMinutes(5),
            'cooldown_end_at' => Carbon::now()->addMinutes(3),
            'suspend_end_at' => null,
            'send_attempt' => 0,
            'is_used' => false,
        ];

        $otpRepository->expects($this->once())
            ->method('findLatestActive')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn($cooldownOtpRecord);

        $service = new SendRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(OTPRateLimitedException::class);
        $service->execute($this->dto);
    }

    public function testExecuteThrowsExceptionWhenMaxSendAttemptsReached()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($this->mockUser);

        $maxAttemptOtpRecord = (object) [
            'id' => 1,
            'user_id' => 1,
            'code' => Hash::make('123456'),
            'purpose' => OTPPurposeEnum::REGISTRATION,
            'expired_at' => Carbon::now()->addMinutes(5),
            'cooldown_end_at' => null,
            'suspend_end_at' => null,
            'send_attempt' => 3,
            'is_used' => false,
        ];

        $otpRepository->expects($this->once())
            ->method('findLatestActive')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn($maxAttemptOtpRecord);

        $otpRepository->expects($this->once())
            ->method('update')
            ->with(1, $this->callback(function ($data) {
                return isset($data['cooldown_end_at'])
                    && $data['send_attempt'] === 0;
            }));

        $service = new SendRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(OTPRateLimitedException::class);
        $service->execute($this->dto);
    }

    public function testExecuteSuccessWithNoExistingOtpRecord()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($this->mockUser);

        $otpRepository->expects($this->once())
            ->method('findLatestActive')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn(null);

        $otpRepository->expects($this->once())
            ->method('deleteOthers')
            ->with(1, OTPPurposeEnum::REGISTRATION, 0);

        $createdOtp = (object) ['id' => 2, 'user_id' => 1, 'purpose' => OTPPurposeEnum::REGISTRATION];
        $otpRepository->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['user_id'] === 1
                    && $data['email'] === 'test@example.com'
                    && $data['purpose'] === OTPPurposeEnum::REGISTRATION
                    && $data['send_attempt'] === 1
                    && $data['is_used'] === false
                    && is_string($data['code'])
                    && strlen($data['code']) > 0;
            }))
            ->willReturn($createdOtp);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Notification::shouldReceive('send')
            ->once()
            ->with($this->mockUser, SendRegistrationOTPNotification::class)
            ->andReturnNull();

        $service = new SendRegistrationOTPService($otpRepository, $userRepository);

        $result = $service->execute($this->dto);

        $this->assertEquals(2, $result->id);
    }

    public function testExecuteSuccessWithExistingOtpRecordIncrementsSendAttempt()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($this->mockUser);

        $existingOtpRecord = (object) [
            'id' => 1,
            'user_id' => 1,
            'code' => Hash::make('123456'),
            'purpose' => OTPPurposeEnum::REGISTRATION,
            'expired_at' => Carbon::now()->addMinutes(5),
            'cooldown_end_at' => null,
            'suspend_end_at' => null,
            'send_attempt' => 1,
            'is_used' => false,
        ];

        $otpRepository->expects($this->once())
            ->method('findLatestActive')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn($existingOtpRecord);

        $otpRepository->expects($this->once())
            ->method('deleteOthers')
            ->with(1, OTPPurposeEnum::REGISTRATION, 0);

        $createdOtp = (object) ['id' => 2, 'user_id' => 1, 'purpose' => OTPPurposeEnum::REGISTRATION];
        $otpRepository->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['send_attempt'] === 2;
            }))
            ->willReturn($createdOtp);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Notification::shouldReceive('send')
            ->once()
            ->with($this->mockUser, SendRegistrationOTPNotification::class)
            ->andReturnNull();

        $service = new SendRegistrationOTPService($otpRepository, $userRepository);

        $result = $service->execute($this->dto);

        $this->assertEquals(2, $result->id);
    }

    public function testExecuteUsesDefaultPurposeWhenNotProvided()
    {
        $dto = (object) ['userId' => 1, 'purpose' => null];

        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($this->mockUser);

        $otpRepository->expects($this->once())
            ->method('findLatestActive')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn(null);

        $otpRepository->expects($this->once())
            ->method('deleteOthers')
            ->with(1, OTPPurposeEnum::REGISTRATION, 0);

        $createdOtp = (object) ['id' => 1, 'user_id' => 1, 'purpose' => OTPPurposeEnum::REGISTRATION];
        $otpRepository->expects($this->once())
            ->method('create')
            ->willReturn($createdOtp);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Notification::shouldReceive('send')
            ->once()
            ->with($this->mockUser, SendRegistrationOTPNotification::class)
            ->andReturnNull();

        $service = new SendRegistrationOTPService($otpRepository, $userRepository);

        $result = $service->execute($dto);

        $this->assertEquals(1, $result->id);
    }
}
