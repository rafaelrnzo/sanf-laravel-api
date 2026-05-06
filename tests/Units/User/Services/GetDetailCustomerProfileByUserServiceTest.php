<?php

use Carbon\Carbon;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Services\GetDetailCustomerProfileByUserService;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetDetailCustomerProfileByUserServiceTest extends PHPUnitTestCase
{
    public function testExecuteThrowsExceptionWhenUserNotFound()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $apiClient = $this->createMock(SanfCoreApiClient::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn(null);

        $service = new GetDetailCustomerProfileByUserService($userRepository, $apiClient);

        $this->expectException(UserNotFoundException::class);
        $service->execute((object) ['userId' => 1, 'customerId' => 'CUST001']);
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
            ->method('findCustomerById')
            ->with('CUST001')
            ->willReturn([
                'data' => [
                    [
                        'CUST_ID_SANF' => 'CUST001',
                        'ID_IDENTITY' => 1,
                        'DESC_IDENTITY' => 'Personal',
                        'COMPANY_TYPE' => 'Mr',
                        'IDENTITY_NAME' => 'John Doe',
                        'PIC_NAME' => 'John Doe',
                        'KTP' => '1234567890123456',
                        'NPWP' => '12.345.678.9-012.000',
                        'EMAIL_ADDR' => 'different@example.com',
                        'NO_TELP' => '021123456',
                        'NO_HP' => '081212345678',
                        'GENDER' => 'L',
                        'TGL_LAHIR' => '1990-01-01',
                        'ID_NEGARA' => 'ID',
                        'NEGARA' => 'Indonesia',
                        'ID_PROVINSI' => '31',
                        'PROVINSI' => 'DKI Jakarta',
                        'ID_KOTA' => '3171',
                        'KOTA' => 'Jakarta Selatan',
                        'KECAMATAN' => 'Kebayoran Baru',
                        'KELURAHAN' => 'Senayan',
                        'KODEPOS' => '12110',
                        'ALAMAT' => 'Jl. Sudirman No. 1',
                        'LAMA_USAHA' => 5,
                        'PIC' => 1,
                    ],
                ],
            ]);

        $service = new GetDetailCustomerProfileByUserService($userRepository, $apiClient);

        $this->expectException(\NbsPhp\Core\Exceptions\ForbiddenException::class);
        $service->execute((object) ['userId' => 1, 'customerId' => 'CUST001']);
    }

    public function testExecuteReturnsProfileWhenOwnershipMatches()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $apiClient = $this->createMock(SanfCoreApiClient::class);

        $user = (object) ['id' => 1, 'username' => 'user@example.com'];
        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($user);

        $apiClient->expects($this->once())
            ->method('findCustomerById')
            ->with('CUST001')
            ->willReturn([
                'data' => [
                    [
                        'CUST_ID_SANF' => 'CUST001',
                        'ID_IDENTITY' => 1,
                        'DESC_IDENTITY' => 'Personal',
                        'COMPANY_TYPE' => 'Mr',
                        'IDENTITY_NAME' => 'John Doe',
                        'PIC_NAME' => 'John Doe',
                        'KTP' => '1234567890123456',
                        'NPWP' => '12.345.678.9-012.000',
                        'EMAIL_ADDR' => 'user@example.com',
                        'NO_TELP' => '021123456',
                        'NO_HP' => '081212345678',
                        'GENDER' => 'L',
                        'TGL_LAHIR' => '1990-01-01',
                        'ID_NEGARA' => 'ID',
                        'NEGARA' => 'Indonesia',
                        'ID_PROVINSI' => '31',
                        'PROVINSI' => 'DKI Jakarta',
                        'ID_KOTA' => '3171',
                        'KOTA' => 'Jakarta Selatan',
                        'KECAMATAN' => 'Kebayoran Baru',
                        'KELURAHAN' => 'Senayan',
                        'KODEPOS' => '12110',
                        'ALAMAT' => 'Jl. Sudirman No. 1',
                        'LAMA_USAHA' => 5,
                        'PIC' => 1,
                    ],
                ],
            ]);

        $service = new GetDetailCustomerProfileByUserService($userRepository, $apiClient);

        $result = $service->execute((object) ['userId' => 1, 'customerId' => 'CUST001']);

        $this->assertEquals('CUST001', $result->xid);
        $this->assertEquals(1, $result->typeId);
        $this->assertEquals('Personal', $result->typeName);
        $this->assertEquals('Mr', $result->title);
        $this->assertEquals('John Doe', $result->fullName);
        $this->assertEquals('John Doe', $result->picName);
        $this->assertEquals('1234567890123456', $result->identityNumber);
        $this->assertEquals('user@example.com', $result->email);
        $this->assertEquals('021123456', $result->landlineNumber);
        $this->assertEquals('081212345678', $result->phoneNumber);
        $this->assertEquals('L', $result->gender);
        $this->assertEquals('ID', $result->countryId);
        $this->assertEquals('Indonesia', $result->countryName);
        $this->assertEquals('31', $result->provinceId);
        $this->assertEquals('DKI Jakarta', $result->provinceName);
        $this->assertEquals('3171', $result->cityId);
        $this->assertEquals('Jakarta Selatan', $result->cityName);
        $this->assertEquals('Kebayoran Baru', $result->districtName);
        $this->assertEquals('Senayan', $result->subdistrictName);
        $this->assertEquals('12110', $result->postcode);
        $this->assertEquals('Jl. Sudirman No. 1', $result->address);
        $this->assertEquals(5, $result->businessSince);
        $this->assertTrue($result->isPic);
    }
}
