<?php

use Carbon\Carbon;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sanf\Core\Modules\User\Enums\ProfileType;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Services\UpdatePersonalProfileService;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class UpdatePersonalProfileServiceTest extends PHPUnitTestCase
{
    public function testExecuteThrowsExceptionWhenUserNotFound()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $apiClient = $this->createMock(SanfCoreApiClient::class);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn(null);

        $service = new UpdatePersonalProfileService($userRepository, $apiClient);

        $this->expectException(UserNotFoundException::class);
        $service->execute((object) [
            'userId' => 1,
            'customerId' => 'CUST001',
            'identityNumber' => '1234567890123456',
            'landlineNumber' => '021123456',
            'phoneNumber' => '081212345678',
            'gender' => 'L',
            'birthdate' => '1990-01-01',
            'provinceId' => '31',
            'provinceName' => 'DKI Jakarta',
            'cityId' => '3171',
            'cityName' => 'Jakarta Selatan',
            'districtName' => 'Kebayoran Baru',
            'subdistrictName' => 'Senayan',
            'postcode' => '12110',
            'address' => 'Jl. Sudirman No. 1',
            'businessSince' => 5,
        ]);
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
                    ['EMAIL_ADDR' => 'different@example.com', 'ID_IDENTITY' => ProfileType::PERSONAL, 'PIC_NAME' => 'Pic'],
                ],
            ]);

        $service = new UpdatePersonalProfileService($userRepository, $apiClient);

        $this->expectException(\NbsPhp\Core\Exceptions\ForbiddenException::class);
        $service->execute((object) [
            'userId' => 1,
            'customerId' => 'CUST001',
            'identityNumber' => '1234567890123456',
            'landlineNumber' => '021123456',
            'phoneNumber' => '081212345678',
            'gender' => 'L',
            'birthdate' => '1990-01-01',
            'provinceId' => '31',
            'provinceName' => 'DKI Jakarta',
            'cityId' => '3171',
            'cityName' => 'Jakarta Selatan',
            'districtName' => 'Kebayoran Baru',
            'subdistrictName' => 'Senayan',
            'postcode' => '12110',
            'address' => 'Jl. Sudirman No. 1',
            'businessSince' => 5,
        ]);
    }

    public function testExecuteThrowsExceptionWhenProfileTypeMismatch()
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
                    ['EMAIL_ADDR' => 'user@example.com', 'ID_IDENTITY' => ProfileType::COMPANY, 'PIC_NAME' => 'Pic'],
                ],
            ]);

        $service = new UpdatePersonalProfileService($userRepository, $apiClient);

        $this->expectException(UserNotFoundException::class);
        $service->execute((object) [
            'userId' => 1,
            'customerId' => 'CUST001',
            'identityNumber' => '1234567890123456',
            'landlineNumber' => '021123456',
            'phoneNumber' => '081212345678',
            'gender' => 'L',
            'birthdate' => '1990-01-01',
            'provinceId' => '31',
            'provinceName' => 'DKI Jakarta',
            'cityId' => '3171',
            'cityName' => 'Jakarta Selatan',
            'districtName' => 'Kebayoran Baru',
            'subdistrictName' => 'Senayan',
            'postcode' => '12110',
            'address' => 'Jl. Sudirman No. 1',
            'businessSince' => 5,
        ]);
    }

    public function testExecuteCallsUpdateCustomerWithCorrectParameters()
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
                    ['EMAIL_ADDR' => 'user@example.com', 'ID_IDENTITY' => ProfileType::PERSONAL, 'PIC_NAME' => 'Existing Pic'],
                ],
            ]);

        $apiClient->expects($this->once())
            ->method('updateCustomer')
            ->with($this->callback(function ($params) {
                return $params['cust_id'] === 'CUST001'
                    && $params['ktp'] === '1234567890123456'
                    && $params['cust_type'] === ProfileType::PERSONAL
                    && $params['notelp'] === '021123456'
                    && $params['nohp'] === '081212345678'
                    && $params['gender'] === 'L'
                    && $params['idprov'] === '31'
                    && $params['prov'] === 'DKI Jakarta'
                    && $params['idkota'] === '3171'
                    && $params['kota'] === 'Jakarta Selatan'
                    && $params['kecamatan'] === 'Kebayoran Baru'
                    && $params['kelurahan'] === 'Senayan'
                    && $params['kodepos'] === '12110'
                    && $params['alamat'] === 'Jl. Sudirman No. 1'
                    && $params['lama_usaha'] === 5
                    && $params['picname'] === 'Existing Pic';
            }));

        $service = new UpdatePersonalProfileService($userRepository, $apiClient);

        $service->execute((object) [
            'userId' => 1,
            'customerId' => 'CUST001',
            'identityNumber' => '1234567890123456',
            'landlineNumber' => '021123456',
            'phoneNumber' => '081212345678',
            'gender' => 'L',
            'birthdate' => '1990-01-01',
            'provinceId' => '31',
            'provinceName' => 'DKI Jakarta',
            'cityId' => '3171',
            'cityName' => 'Jakarta Selatan',
            'districtName' => 'Kebayoran Baru',
            'subdistrictName' => 'Senayan',
            'postcode' => '12110',
            'address' => 'Jl. Sudirman No. 1',
            'businessSince' => 5,
        ]);
    }

    public function testExecuteKeepsExistingPicNameWhenPicNameNotProvided()
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
                    ['EMAIL_ADDR' => 'user@example.com', 'ID_IDENTITY' => ProfileType::PERSONAL, 'PIC_NAME' => 'Existing Pic'],
                ],
            ]);

        $apiClient->expects($this->once())
            ->method('updateCustomer')
            ->with($this->callback(function ($params) {
                return $params['picname'] === 'Existing Pic';
            }));

        $service = new UpdatePersonalProfileService($userRepository, $apiClient);

        $service->execute((object) [
            'userId' => 1,
            'customerId' => 'CUST001',
            'identityNumber' => '1234567890123456',
            'landlineNumber' => '021123456',
            'phoneNumber' => '081212345678',
            'gender' => 'L',
            'birthdate' => '1990-01-01',
            'provinceId' => '31',
            'provinceName' => 'DKI Jakarta',
            'cityId' => '3171',
            'cityName' => 'Jakarta Selatan',
            'districtName' => 'Kebayoran Baru',
            'subdistrictName' => 'Senayan',
            'postcode' => '12110',
            'address' => 'Jl. Sudirman No. 1',
            'businessSince' => 5,
        ]);
    }
}
