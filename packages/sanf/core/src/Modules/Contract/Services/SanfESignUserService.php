<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\ResponseESignUserDto;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationCompleteEnum;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentEncryptedRepository;
use Sanf\Core\Modules\User\Repositories\RestProfileRepository;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class SanfESignUserService implements ApplicationServiceInterface
{
    public const MALE = 'M';
    protected EloquentESignDocumentEncryptedRepository $eSignDocumentRepository;
    protected RestProfileRepository $sanfProfileRepository;
    protected SanfCoreApiClient $sanfCoreClient;
    protected AdInsESignRegisterCheckService $adInsRegisterCheckService;

    public function __construct(
        RestProfileRepository $sanfProfileRepository,
        SanfCoreApiClient $sanfCoreClient,
        EloquentESignDocumentEncryptedRepository $eSignDocumentRepository,
        AdInsESignRegisterCheckService $adInsRegisterCheckService
    ) {
        $this->sanfProfileRepository = $sanfProfileRepository;
        $this->sanfCoreClient = $sanfCoreClient;
        $this->eSignDocumentRepository = $eSignDocumentRepository;
        $this->adInsRegisterCheckService = $adInsRegisterCheckService;
    }

    public function execute($dto = null)
    {
        $userSanfResponse = $this->sanfProfileRepository->findById($dto->profileXid);

        if (is_null($userSanfResponse)) {
            throw new UserNotFoundException();
        }

        $eSignSanfUserResponse = $this->sanfCoreClient->getAvailableESignUser($userSanfResponse->getEmail());

        $registrationComplete = data_get($eSignSanfUserResponse, 'data.0.F_REGISTRATION_COMPLETE');

        $eSignSanfUserMapping = array_map(function ($item) use ($userSanfResponse, $registrationComplete) {
            return [
                'email' => isset($item['EMAIL']) ? $item['EMAIL'] : $userSanfResponse->getEmail(),
                'msisdn' => isset($item['MOBILE']) ? $item['MOBILE'] : $userSanfResponse->getPhoneNumber(),
                'nik' => isset($item['NIK']) ? $item['NIK'] : $userSanfResponse->getIdentityNumber(),
                'fullName' => isset($item['NAME']) ? $item['NAME'] : $userSanfResponse->getFullName(),
                'dob' => isset($item['DOB']) ? Carbon::createFromFormat('Y/m/d', $item['DOB'])->format('Y-m-d') : $userSanfResponse->getBirthdate(),
                'pob' => isset($item['POB']) ? $item['POB'] : null,
                'gender' => isset($item['GENDER']) ? (int) ($item['GENDER'] == 'F') : $userSanfResponse->getGender(),
                'address' => isset($item['ADDRESS']) ? $item['ADDRESS'] : $userSanfResponse->getAddress(),
                'postcode' => isset($item['ZIP_CODE']) ? (string) $item['ZIP_CODE'] : $userSanfResponse->getPostcode(),
                'countryId' => isset($item['NEGARA']) ? $item['NEGARA'] : $userSanfResponse->getCountryId(),
                'countryName' => isset($item['ID_NEGARA']) ? $item['ID_NEGARA'] : $userSanfResponse->getCountryName(),
                'provinceId' => isset($item['ID_PROVINSI']) ? $item['ID_PROVINSI'] : $userSanfResponse->getProvinceId(),
                'provinceName' => isset($item['PROVINSI']) ? $item['PROVINSI'] : $userSanfResponse->getProvinceName(),
                'cityId' => isset($item['ID_KOTA']) ? $item['ID_KOTA'] : $userSanfResponse->getCityId(),
                'cityName' => isset($item['KOTA']) ? $item['KOTA'] : $userSanfResponse->getCityName(),
                'districtName' => isset($item['KECAMATAN']) ? $item['KECAMATAN'] : $userSanfResponse->getDistrictName(),
                'subdistrictName' => isset($item['KELURAHAN']) ? $item['KELURAHAN'] : $userSanfResponse->getSubdistrictName(),
                'statusId' => ESignRegistrationStatusEnum::AVAILABLE,
                'isAccountExpired' => $registrationComplete === ESignRegistrationCompleteEnum::NO,
            ];
        }, $eSignSanfUserResponse['data'])[0];

        $adInsUser = $this->eSignDocumentRepository->findUserBySanfId($dto->profileXid);

        if (is_null($adInsUser) === false) {
            $eSignSanfUserMapping['xid'] = $adInsUser->xid;
            $eSignSanfUserMapping['msisdn'] = $adInsUser->msisdn;
            $eSignSanfUserMapping['nik'] = $adInsUser->identity_no;
            $eSignSanfUserMapping['fullName'] = $adInsUser->full_name;
            $eSignSanfUserMapping['dob'] = $adInsUser->date_of_birth;
            $eSignSanfUserMapping['pob'] = $adInsUser->place_of_birth;
            $eSignSanfUserMapping['gender'] = (int) ($adInsUser->gender === self::MALE);
            $eSignSanfUserMapping['address'] = $adInsUser->address;
            $eSignSanfUserMapping['postcode'] = (string) $adInsUser->postal_code;
            if ($adInsUser->province !== $eSignSanfUserMapping['provinceName']) {
                $eSignSanfUserMapping['provinceId'] = null;
                $eSignSanfUserMapping['provinceName'] = $adInsUser->province;
            }
            if ($adInsUser->city !== $eSignSanfUserMapping['cityName']) {
                $eSignSanfUserMapping['cityId'] = null;
                $eSignSanfUserMapping['cityName'] = $adInsUser->city;
            }
            $eSignSanfUserMapping['districtName'] = $adInsUser->district;
            $eSignSanfUserMapping['subdistrictName'] = $adInsUser->sub_district;
            $eSignSanfUserMapping['selfieFile'] = $adInsUser->selfie_file;
            $eSignSanfUserMapping['identityFile'] = $adInsUser->identity_file;
            $eSignSanfUserMapping['statusId'] = $adInsUser->status_id;

            if ($adInsUser->certificate_expired_at) {
                $eSignSanfUserMapping['isAccountExpired'] = $eSignSanfUserMapping['isAccountExpired'] || Carbon::parse($adInsUser->certificate_expired_at)->lessThanOrEqualTo(Carbon::now());
            }

            $dto = (object) [
                'email' => $adInsUser->email,
                'msisdn' => $adInsUser->msisdn,
                'identityNo' => $adInsUser->identity_no,
            ];

            $eSignUserUpdateData = [];

            $registerStatus = $this->adInsRegisterCheckService->execute($dto);

            $vendor = 'Vida';
            foreach ($registerStatus->status as $status) {
                if ($status->vendor != $vendor) {
                    continue;
                }

                if ($status->registrationStatus == $this->adInsRegisterCheckService::ACTIVE) {
                    $eSignSanfUserMapping['statusId'] = ESignRegistrationStatusEnum::COMPLETE;
                }

                $certificateExpiredAt = $status->expiredDate ? Carbon::parse($status->expiredDate, 'Asia/Jakarta')->startOfDay()->utc() : null;

                if (
                    $certificateExpiredAt
                    && !$certificateExpiredAt->equalTo(Carbon::make($adInsUser->certificate_expired_at))
                ) {
                    $eSignUserUpdateData['certificate_expired_at'] = $certificateExpiredAt;
                    $eSignSanfUserMapping['isAccountExpired'] = $registrationComplete === ESignRegistrationCompleteEnum::NO || $certificateExpiredAt->lessThanOrEqualTo(Carbon::now());
                }
            }

            if ($adInsUser->status_id !== ESignRegistrationStatusEnum::COMPLETE && $eSignSanfUserMapping['statusId'] === ESignRegistrationStatusEnum::COMPLETE) {
                $eSignUserUpdateData['status_id'] = ESignRegistrationStatusEnum::COMPLETE;
            }

            if (!empty($eSignUserUpdateData)) {
                $eSignUserUpdateData['updated_at'] = CarbonImmutable::now();
                $this->eSignDocumentRepository->updateUser($adInsUser->id, $eSignUserUpdateData);
            }
        }

        return new ResponseESignUserDto($eSignSanfUserMapping);
    }
}
