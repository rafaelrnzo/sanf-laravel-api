<?php

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Services\GetUserMetadataContractService;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetUserMetadataContractServiceTest extends PHPUnitTestCase
{
    public function testExecuteThrowsExceptionWhenUserNotFound()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $apiClient = $this->createMock(SanfCoreApiClient::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn(null);

        $service = new GetUserMetadataContractService($userRepository, $apiClient);

        $this->expectException(\NbsPhp\Core\Exceptions\UserNotFoundException::class);
        $service->execute((object) ['user_id' => 1, 'profile_xid' => 'CUST001']);
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

        $service = new GetUserMetadataContractService($userRepository, $apiClient);

        $this->expectException(\NbsPhp\Core\Exceptions\ForbiddenException::class);
        $service->execute((object) ['user_id' => 1, 'profile_xid' => 'CUST001']);
    }

    public function testExecuteReturnsCorrectContractMetadata()
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
                    (object) ['TOTAL_AKTIF' => 5, 'TOTAL_SELESAI' => 3],
                    (object) ['TOTAL_AKTIF' => 2, 'TOTAL_SELESAI' => 1],
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

        $service = new GetUserMetadataContractService($userRepository, $apiClient);

        $result = $service->execute((object) ['user_id' => 1, 'profile_xid' => 'CUST001']);

        $this->assertEquals(7, $result->total_active_contract);
        $this->assertEquals(4, $result->total_finished_contract);
    }

    public function testExecuteReturnsZeroWhenNoContracts()
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
                    ['EMAIL_ADDR' => 'user@example.com'],
                ],
            ]);

        $service = new GetUserMetadataContractService($userRepository, $apiClient);

        $result = $service->execute((object) ['user_id' => 1, 'profile_xid' => 'CUST001']);

        $this->assertEquals(0, $result->total_active_contract);
        $this->assertEquals(0, $result->total_finished_contract);
    }
}
