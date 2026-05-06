<?php

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Services\GetUserMetadataAccountReceivableService;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetUserMetadataAccountReceivableServiceTest extends PHPUnitTestCase
{
    public function testExecuteThrowsExceptionWhenUserNotFound()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $apiClient = $this->createMock(SanfCoreApiClient::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn(null);

        $service = new GetUserMetadataAccountReceivableService($userRepository, $apiClient);

        $this->expectException(\NbsPhp\Core\Exceptions\UserNotFoundException::class);
        $service->execute((object) ['user_id' => 1, 'profile_xid' => 'CUST001', 'currency_type' => 'IDR']);
    }

    public function testExecuteThrowsForbiddenExceptionWhenEmailMismatch()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $apiClient = $this->createMock(SanfCoreApiClient::class);

        $user = (object) ['id' => 1, 'username' => 'user@example.com'];
        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($user);

        $apiClient->expects($this->once())
            ->method('getMetadataContract')
            ->with('CUST001')
            ->willReturn((object) ['data' => []]);

        $apiClient->expects($this->once())
            ->method('findCustomerById')
            ->with('CUST001')
            ->willReturn([
                'data' => [
                    ['EMAIL_ADDR' => 'different@example.com'],
                ],
            ]);

        $service = new GetUserMetadataAccountReceivableService($userRepository, $apiClient);

        $this->expectException(\NbsPhp\Core\Exceptions\ForbiddenException::class);
        $service->execute((object) ['user_id' => 1, 'profile_xid' => 'CUST001', 'currency_type' => 'IDR']);
    }

    public function testExecuteReturnsCorrectSummedAmounts()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $apiClient = $this->createMock(SanfCoreApiClient::class);

        $user = (object) ['id' => 1, 'username' => 'user@example.com'];
        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($user);

        $apiClient->expects($this->once())
            ->method('getMetadataContract')
            ->with('CUST001')
            ->willReturn((object) [
                'data' => [
                    (object) ['CURR_ID_AKTIF' => 'IDR', 'AMT_AKTIF' => 1000000, 'CURR_ID_SELESAI' => 'IDR', 'AMT_SELESAI' => 500000],
                    (object) ['CURR_ID_AKTIF' => 'IDR', 'AMT_AKTIF' => 2000000, 'CURR_ID_SELESAI' => 'IDR', 'AMT_SELESAI' => 300000],
                    (object) ['CURR_ID_AKTIF' => 'USD', 'AMT_AKTIF' => 5000, 'CURR_ID_SELESAI' => 'USD', 'AMT_SELESAI' => 2000],
                    (object) ['CURR_ID_AKTIF' => 'IDR', 'AMT_AKTIF' => 500000, 'CURR_ID_SELESAI' => 'IDR', 'AMT_SELESAI' => 100000],
                ],
            ]);

        $apiClient->expects($this->once())
            ->method('findCustomerById')
            ->with('CUST001')
            ->willReturn([
                'data' => [
                    ['EMAIL_ADDR' => 'user@example.com'],
                ],
            ]);

        $service = new GetUserMetadataAccountReceivableService($userRepository, $apiClient);

        $result = $service->execute((object) ['user_id' => 1, 'profile_xid' => 'CUST001', 'currency_type' => 'IDR']);

        $this->assertEquals(3500000, $result->total_outstanding_amount);
        $this->assertEquals(900000, $result->total_paid_amount);
        $this->assertEquals('IDR', $result->currency_type);
    }

    public function testExecuteFiltersByCorrectCurrencyType()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $apiClient = $this->createMock(SanfCoreApiClient::class);

        $user = (object) ['id' => 1, 'username' => 'user@example.com'];
        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($user);

        $apiClient->expects($this->once())
            ->method('getMetadataContract')
            ->with('CUST001')
            ->willReturn((object) [
                'data' => [
                    (object) ['CURR_ID_AKTIF' => 'IDR', 'AMT_AKTIF' => 1000000, 'CURR_ID_SELESAI' => 'IDR', 'AMT_SELESAI' => 500000],
                    (object) ['CURR_ID_AKTIF' => 'USD', 'AMT_AKTIF' => 5000, 'CURR_ID_SELESAI' => 'USD', 'AMT_SELESAI' => 2000],
                ],
            ]);

        $apiClient->expects($this->once())
            ->method('findCustomerById')
            ->with('CUST001')
            ->willReturn([
                'data' => [
                    ['EMAIL_ADDR' => 'user@example.com'],
                ],
            ]);

        $service = new GetUserMetadataAccountReceivableService($userRepository, $apiClient);

        $result = $service->execute((object) ['user_id' => 1, 'profile_xid' => 'CUST001', 'currency_type' => 'USD']);

        $this->assertEquals(5000, $result->total_outstanding_amount);
        $this->assertEquals(2000, $result->total_paid_amount);
        $this->assertEquals('USD', $result->currency_type);
    }

    public function testExecuteReturnsZeroWhenNoRecordsForCurrency()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $apiClient = $this->createMock(SanfCoreApiClient::class);

        $user = (object) ['id' => 1, 'username' => 'user@example.com'];
        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($user);

        $apiClient->expects($this->once())
            ->method('getMetadataContract')
            ->with('CUST001')
            ->willReturn((object) [
                'data' => [
                    (object) ['CURR_ID_AKTIF' => 'IDR', 'AMT_AKTIF' => 1000000, 'CURR_ID_SELESAI' => 'IDR', 'AMT_SELESAI' => 500000],
                ],
            ]);

        $apiClient->expects($this->once())
            ->method('findCustomerById')
            ->with('CUST001')
            ->willReturn([
                'data' => [
                    ['EMAIL_ADDR' => 'user@example.com'],
                ],
            ]);

        $service = new GetUserMetadataAccountReceivableService($userRepository, $apiClient);

        $result = $service->execute((object) ['user_id' => 1, 'profile_xid' => 'CUST001', 'currency_type' => 'EUR']);

        $this->assertEquals(0, $result->total_outstanding_amount);
        $this->assertEquals(0, $result->total_paid_amount);
        $this->assertEquals('EUR', $result->currency_type);
    }
}
