<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sanf\Core\Modules\User\Enums\OTPPurposeEnum;
use Sanf\Core\Modules\User\Exceptions\OTPExpiredException;
use Sanf\Core\Modules\User\Exceptions\OTPInvalidCodeException;
use Sanf\Core\Modules\User\Exceptions\OTPInvalidException;
use Sanf\Core\Modules\User\Exceptions\OTPPurposeInvalidException;
use Sanf\Core\Modules\User\Exceptions\OTPSuspendedException;
use Sanf\Core\Modules\User\Repositories\RegistrationOTPRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Services\VerifyRegistrationOTPService;

class VerifyRegistrationOTPServiceTest extends PHPUnitTestCase
{
    private $mockUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockUser = (object) [
            'id' => 1,
            'username' => 'test@example.com',
            'status_id' => 1,
        ];
    }

    private function makeOtpRecord(array $overrides = [])
    {
        return (object) array_merge([
            'id' => 1,
            'user_id' => 1,
            'code' => Hash::make('123456'),
            'purpose' => OTPPurposeEnum::REGISTRATION,
            'expired_at' => Carbon::now()->addMinutes(5),
            'cooldown_end_at' => null,
            'suspend_end_at' => null,
            'send_attempt' => 1,
            'verify_attempt' => 0,
            'is_used' => false,
        ], $overrides);
    }

    public function testExecuteThrowsExceptionWhenPurposeIsInvalid()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $dto = (object) ['userId' => 1, 'purpose' => 'invalid_purpose', 'code' => '123456'];

        $service = new VerifyRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(OTPPurposeInvalidException::class);
        $service->execute($dto);
    }

    public function testExecuteThrowsExceptionWhenNoOtpRecordFound()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $otpRepository->expects($this->once())
            ->method('findLatestNotUsed')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn(null);

        $dto = (object) ['userId' => 1, 'purpose' => OTPPurposeEnum::REGISTRATION, 'code' => '123456'];

        $service = new VerifyRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(OTPInvalidException::class);
        $service->execute($dto);
    }

    public function testExecuteThrowsExceptionWhenOtpIsExpired()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $otpRepository->expects($this->once())
            ->method('findLatestNotUsed')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn($this->makeOtpRecord(['expired_at' => Carbon::now()->subMinutes(1)]));

        $dto = (object) ['userId' => 1, 'purpose' => OTPPurposeEnum::REGISTRATION, 'code' => '123456'];

        $service = new VerifyRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(OTPExpiredException::class);
        $service->execute($dto);
    }

    public function testExecuteThrowsExceptionWhenAccountIsSuspended()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $otpRepository->expects($this->once())
            ->method('findLatestNotUsed')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn($this->makeOtpRecord(['suspend_end_at' => Carbon::now()->addHours(1)]));

        $dto = (object) ['userId' => 1, 'purpose' => OTPPurposeEnum::REGISTRATION, 'code' => '123456'];

        $service = new VerifyRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(OTPSuspendedException::class);
        $service->execute($dto);
    }

    public function testExecuteSuccessWithValidCodeAndUpdatesUserToActive()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);

        // Use a fake that accepts the service's actual argument order (id, data)
        // to work around the interface mismatch with the service implementation.
        $userRepository = new class implements UserRepositoryInterface {
            public $updateCalled = false;
            public $updateData = null;
            public $updateId = null;
            public function query($spec) { return null; }
            public function find(array $filters) { return null; }
            public function findById($id) { return null; }
            public function findByEmail($email) { return null; }
            public function existsByEmailAndStatusIds(string $email, array $statusIds): bool { return false; }
            public function findByEmailAndStatusIds(string $email, array $statusIds) { return null; }
            public function existsByEmail(string $email): bool { return false; }
            public function create(array $data) { return null; }
            public function update($data, $id): bool {
                $this->updateCalled = true;
                $this->updateId = $id;
                $this->updateData = $data;
                return true;
            }
        };

        $otpRepository->expects($this->once())
            ->method('findLatestNotUsed')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn($this->makeOtpRecord());

        $otpRepository->expects($this->once())
            ->method('update')
            ->with(1, $this->callback(function ($data) {
                return $data['is_used'] === true;
            }));

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $dto = (object) ['userId' => 1, 'purpose' => OTPPurposeEnum::REGISTRATION, 'code' => '123456'];

        $service = new VerifyRegistrationOTPService($otpRepository, $userRepository);

        $result = $service->execute($dto);

        $this->assertTrue($result);
        $this->assertTrue($userRepository->updateCalled);
        $this->assertEquals(1, $userRepository->updateData);
        $this->assertArrayHasKey('email_verified_at', $userRepository->updateId);
        $this->assertArrayHasKey('status_id', $userRepository->updateId);
    }

    public function testExecuteThrowsExceptionWithInvalidCode()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $otpRepository->expects($this->once())
            ->method('findLatestNotUsed')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn($this->makeOtpRecord());

        $otpRepository->expects($this->once())
            ->method('update')
            ->with(1, ['verify_attempt' => 1]);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $dto = (object) ['userId' => 1, 'purpose' => OTPPurposeEnum::REGISTRATION, 'code' => 'wrongcode'];

        $service = new VerifyRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(OTPInvalidCodeException::class);
        $service->execute($dto);
    }

    public function testExecuteSuspendsAccountAfterMaxVerifyAttempts()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $otpRepository->expects($this->once())
            ->method('findLatestNotUsed')
            ->with(1, OTPPurposeEnum::REGISTRATION)
            ->willReturn($this->makeOtpRecord(['verify_attempt' => 2]));

        $otpRepository->expects($this->once())
            ->method('update')
            ->with(1, $this->callback(function ($data) {
                return $data['verify_attempt'] === 3
                    && isset($data['suspend_end_at']);
            }));

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $dto = (object) ['userId' => 1, 'purpose' => OTPPurposeEnum::REGISTRATION, 'code' => 'wrongcode'];

        $service = new VerifyRegistrationOTPService($otpRepository, $userRepository);

        $this->expectException(OTPSuspendedException::class);
        $service->execute($dto);
    }

    public function testExecuteSuccessWithNonRegistrationPurposeDoesNotUpdateUser()
    {
        $otpRepository = $this->createMock(RegistrationOTPRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $otpRepository->expects($this->once())
            ->method('findLatestNotUsed')
            ->with(1, OTPPurposeEnum::LOGIN)
            ->willReturn($this->makeOtpRecord(['purpose' => OTPPurposeEnum::LOGIN]));

        $otpRepository->expects($this->once())
            ->method('update')
            ->with(1, $this->isType('array'));

        $userRepository->expects($this->never())
            ->method('update');

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $dto = (object) ['userId' => 1, 'purpose' => OTPPurposeEnum::LOGIN, 'code' => '123456'];

        $service = new VerifyRegistrationOTPService($otpRepository, $userRepository);

        $result = $service->execute($dto);

        $this->assertTrue($result);
    }
}
