<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\ResponseESignUserDto;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;
use Sanf\Core\Modules\User\Repositories\RestProfileRepository;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class SanfESignUserService implements ApplicationServiceInterface
{
    public const MALE = 'M';
    protected EloquentESignDocumentRepository $eSignDocumentRepository;
    protected RestProfileRepository $sanfProfileRepository;
    protected SanfCoreApiClient $sanfCoreClient;
    protected AdInsESignRegisterCheckService $adInsRegisterCheckService;

    public function __construct(
        RestProfileRepository $sanfProfileRepository,
        SanfCoreApiClient $sanfCoreClient,
        EloquentESignDocumentRepository $eSignDocumentRepository,
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

        $eSignSanfUserMapping = array_map(function ($item) use ($userSanfResponse) {
            return [
                'email' => isset($item['EMAIL']) ? $item['EMAIL'] : $userSanfResponse->getEmail(),
                'statusId' => ESignRegistrationStatusEnum::AVAILABLE,
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
            $eSignSanfUserMapping['postalCode'] = (string) $adInsUser->postal_code;
            $eSignSanfUserMapping['province'] = $adInsUser->province;
            $eSignSanfUserMapping['city'] = $adInsUser->city;
            $eSignSanfUserMapping['district'] = $adInsUser->district;
            $eSignSanfUserMapping['subDistrict'] = $adInsUser->sub_district;
            $eSignSanfUserMapping['selfieFile'] = $adInsUser->selfie_file;
            $eSignSanfUserMapping['identityFile'] = $adInsUser->identity_file;
            $eSignSanfUserMapping['statusId'] = $adInsUser->status_id;

            $dto = (object) [
                'email' => $adInsUser->email,
                'msisdn' => $adInsUser->msisdn,
                'identityNo' => $adInsUser->identity_no,
            ];

            if ($adInsUser->status_id !== ESignRegistrationStatusEnum::COMPLETE) {
                $registerStatus = $this->adInsRegisterCheckService->execute($dto);

                $vendor = 'Vida';
                foreach ($registerStatus->status as $status) {
                    if ($status->vendor == $vendor && $status->registrationStatus == $this->adInsRegisterCheckService::ACTIVE) {
                        $eSignSanfUserMapping['statusId'] = ESignRegistrationStatusEnum::COMPLETE;
                    }
                }
            }

            if ($adInsUser->status_id !== ESignRegistrationStatusEnum::COMPLETE && $eSignSanfUserMapping['statusId'] === ESignRegistrationStatusEnum::COMPLETE) {
                $this->eSignDocumentRepository->updateUser($adInsUser->id, [
                    'status_id' => ESignRegistrationStatusEnum::COMPLETE,
                    'updated_at' => CarbonImmutable::now(),
                ]);
            }
        }

        return new ResponseESignUserDto($eSignSanfUserMapping);
    }
}
