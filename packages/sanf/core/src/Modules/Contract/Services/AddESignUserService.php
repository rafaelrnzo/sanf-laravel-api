<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\AddESignUserDto;
use Sanf\Core\Modules\Contract\Enums\UserRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\TekenAjaInternalApiClient;

final class AddESignUserService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected ESignRepositoryInterface $eSignRepository;
    protected TekenAjaInternalApiClient $client;

    public function __construct(
        ESignRepositoryInterface $eSignRepository,
        AuthModel $userRepository,
        TekenAjaInternalApiClient $client
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
            // update data
            $userRegistration = $this->eSignRepository->updateUser($userRegistration->id, $request);
        } else {
            // insert new
            $request['xid'] = nano_id();
            $request['status_id'] = UserRegistrationStatusEnum::AVAILABLE;
            $request['total_submit_registration'] = 1;
            $request['created_at'] = Carbon::now();

            $userRegistration = $this->eSignRepository->createUser($request);
        }

        // hit endpoint registration by tekenAja
        // TODO uncomment this section after tekenAja fix the endpoint url
        /**
        $this->client->addRegisterUser([
            'email' => $userRegistration->email,
            'name' => $userRegistration->full_name,
            'gender' => (string) $userRegistration->gender,
            'dob' => $userRegistration->dob,
            'pob' => $userRegistration->pob,
            'nik' => $userRegistration->nik,
            'mobile' => $userRegistration->msisdn,
            'province' => (string) $userRegistration->province_id,
            'district' => (string) $userRegistration->district_id,
            'sub_district' => (string) $userRegistration->sub_district_id,
            'address' => $userRegistration->address,
            'zip_code' => $userRegistration->postal_code,
            'ktp_photo' => file_get_contents(file_get_temp_url($userRegistration->identity_file->path)),
            'selfie_photo' => file_get_contents(file_get_temp_url($userRegistration->selfie_file->path)),
        ]);
        */

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
}
