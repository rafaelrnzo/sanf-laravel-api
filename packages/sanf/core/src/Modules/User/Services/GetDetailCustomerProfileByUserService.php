<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\ForbiddenException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetDetailCustomerProfileByUserService implements ApplicationServiceInterface
{
    protected $repository;
    protected $internalApiClient;

    public function __construct(AuthModel $repository, SanfCoreApiClient $internalApiClient) //TODO REPOSITORY
    {
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param object $dto userId, customerId
     * @return mixed
     * @throws ForbiddenException
     * @throws UserNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $user = $this->repository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $response = $this->internalApiClient->findCustomerById($dto->customerId);
        $profile = collect($response['data'])
            ->map(function ($item) {
                return (object) [
                    'xid' => $item['CUST_ID_SANF'],
                    'typeId' => $item['ID_IDENTITY'],
                    'typeName' => $item['DESC_IDENTITY'],
                    'title' => $item['COMPANY_TYPE'],
                    'fullName' => $item['IDENTITY_NAME'],
                    'picName' => $item['PIC_NAME'],
                    'identityNumber' => $item['KTP'],
                    'npwp' => $item['NPWP'],
                    'email' => $item['EMAIL_ADDR'],
                    'landlineNumber' => $item['NO_TELP'],
                    'phoneNumber' => $item['NO_HP'],
                    'gender' => $item['GENDER'],
                    'birthdate' => Carbon::make($item['TGL_LAHIR']),
                    'countryId' => $item['ID_NEGARA'],
                    'countryName' => $item['NEGARA'],
                    'provinceId' => $item['ID_PROVINSI'],
                    'provinceName' => $item['PROVINSI'],
                    'cityId' => $item['ID_KOTA'],
                    'cityName' => $item['KOTA'],
                    'districtName' => $item['KECAMATAN'],
                    'subdistrictName' => $item['KELURAHAN'],
                    'postcode' => $item['KODEPOS'],
                    'address' => $item['ALAMAT'],
                    'businessSince' => $item['LAMA_USAHA'],
                    'isPic' => (bool) $item['PIC'],
                ];
            })->first();

        return $profile;
    }
}
