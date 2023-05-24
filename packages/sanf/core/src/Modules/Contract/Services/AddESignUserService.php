<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\AddESignUserDto;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Enums\TekenAjaApiResponseErrorCodeEnum;
use Sanf\Integration\Exceptions\TekenAjaExternalApiException;
use Sanf\Integration\Exceptions\TekenAjaInvalidParameterRegistrationException;
use Sanf\Integration\Exceptions\TekenAjaSubmitRegistrationHasLimitException;
use Sanf\Integration\Modules\TekenAja\TekenAjaApiClient;

final class AddESignUserService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected ESignRepositoryInterface $eSignRepository;
    protected TekenAjaApiClient $client;

    public function __construct(
        ESignRepositoryInterface $eSignRepository,
        AuthModel $userRepository,
        TekenAjaApiClient $client
    ) {
        $this->eSignRepository = $eSignRepository;
        $this->userRepository = $userRepository;
        $this->client = $client;
    }

    /**
     * @param null $dto
     * @return bool
     * @throws UserNotFoundException
     */
    public function execute($dto = null): bool
    {
        /** @var AddESignUserDto $dto */

        // get user
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        // checking user status at tekenAja

        // compose request data
        $request = $this->compose($dto);

        // get existing user
        $userRegistration = $this->eSignRepository->findUserByEmail($dto->email);
        if ($userRegistration) {
            if ($userRegistration->total_submit_registration >= config('tekenaja-api.max_total_submit')) {
                throw new TekenAjaSubmitRegistrationHasLimitException();
            }

            // update data
            $request['updated_at'] = Carbon::now();
            $userRegistration = $this->eSignRepository->updateUser($userRegistration->id, $request);
        } else {
            // insert new
            $request['xid'] = nano_id();
            $request['user_id'] = $dto->userId;
            $request['status_id'] = ESignRegistrationStatusEnum::SUBMIT;
            $request['total_submit_registration'] = 0;
            $request['created_at'] = Carbon::now();

            $userRegistration = $this->eSignRepository->createUser($request);
        }

        // hit endpoint registration by tekenAja
        // TODO use self service like registration tekenAja service
        $result = $this->client->addRegisterUser([
            ['name' => 'email', 'contents' => $userRegistration->email,],
            ['name' => 'name', 'contents' => $userRegistration->full_name,],
            ['name' => 'gender', 'contents' => (string)$userRegistration->gender,],
            ['name' => 'dob', 'contents' => $userRegistration->dob,],
            ['name' => 'pob', 'contents' => $userRegistration->pob,],
            ['name' => 'nik', 'contents' => $userRegistration->nik,],
            ['name' => 'mobile', 'contents' => $userRegistration->msisdn,],
            ['name' => 'province', 'contents' => $userRegistration->province_id,],
            ['name' => 'district', 'contents' => $userRegistration->district_id,],
            ['name' => 'sub_district', 'contents' => $userRegistration->sub_district_id,],
            ['name' => 'address', 'contents' => $userRegistration->address,],
            ['name' => 'zip_code', 'contents' => $userRegistration->postal_code,],
            [
                'name' => 'ktp_photo',
                'contents' => file_get_contents(file_get_temp_url($userRegistration->identity_file->path)),
                'filename' => $userRegistration->identity_file->file_name,
            ],
            [
                'name' => 'selfie_photo',
                'contents' => file_get_contents(file_get_temp_url($userRegistration->selfie_file->path)),
                'filename' => $userRegistration->selfie_file->file_name,
            ],
        ]);

        $request['total_submit_registration'] = $userRegistration->total_submit_registration + 1;
        $request['updated_at'] = Carbon::now();
        $this->eSignRepository->updateUser($userRegistration->id, $request);

        if ($result['code']) {
            $this->errorHandle($result['code'], $result['message']);
        }

        return true;
    }

    private function compose(AddESignUserDto $dto): array
    {
        $request = [
            'email' => $dto->email,
            'msisdn' => $dto->msisdn,
            'nik' => $dto->nik,
            'full_name' => $dto->fullName,
            'dob' => $dto->dob,
            'pob' => $dto->pob,
            'gender' => $dto->gender,
            'address' => $dto->address,
            'postal_code' => $dto->postalCode,
            'province_id' => $dto->provinceId,
            'district_id' => $dto->districtId,
            'sub_district_id' => $dto->subDistrictId,
        ];

        if ($dto->selfieFile) {
            $request['selfie_file'] = $this->moveFile($dto->selfieFile, config('image-path.selfie_tekenaja'));
        }

        if ($dto->identityFile) {
            $request['identity_file'] = $this->moveFile($dto->identityFile, config('image-path.identity_tekenaja'));
        }

        return $request;
    }

    private function moveFile(string $file, string $path): array
    {
        $tempPath = config('image-path.temp');

        $exist = Storage::exists("{$path}{$file}");
        if (!$exist) {
            Storage::move("{$tempPath}{$file}", "{$path}{$file}");
        }

        return [
            'file_name' => $file,
            'directory' => $path,
            'path' => "{$path}{$file}",
            'mime_type' => Storage::getMimeType("{$path}{$file}")
        ];
    }

    private function errorHandle(string $code, $messages)
    {
        switch ($code) {
            case TekenAjaApiResponseErrorCodeEnum::INVALID_PARAMETER:
                $response = array_map(function ($item) {
                    return $item[0];
                }, $messages);
                throw new TekenAjaInvalidParameterRegistrationException(implode('|', $response));
                break;
            default:
            case TekenAjaApiResponseErrorCodeEnum::SYSTEM_FAILURE:
                throw new TekenAjaExternalApiException($messages);
        }
    }
}
