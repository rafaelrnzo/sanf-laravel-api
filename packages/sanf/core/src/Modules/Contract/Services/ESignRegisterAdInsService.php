<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\ESignRegisterFormDto;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignUserUniqueException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;

class ESignRegisterAdInsService implements ApplicationServiceInterface
{
    public const MALE = 1;

    protected AdInsESignRegisterService $adInsRegisterService;
    protected EloquentESignDocumentRepository $eSignRepository;

    public function __construct(AdInsESignRegisterService $adInsRegisterService, EloquentESignDocumentRepository $eSignRepository)
    {
        $this->adInsRegisterService = $adInsRegisterService;
        $this->eSignRepository = $eSignRepository;
    }

    /**
     * @param ESignRegisterFormDto $dto
     */
    public function execute($dto = null)
    {
        $userAdInsRecord = $this->eSignRepository->findUserBySanfIdAndIdentityNo($dto->sanfId, $dto->identityNo);
        if ($userAdInsRecord) {
            throw new ESignUserUniqueException();
        }

        $msisdn = $this->parseMsisdnWithZeroFormat($dto->msisdn);
        $currentTimestamp = CarbonImmutable::now();
        $encryptedPassword = Crypt::encryptString($dto->password);
        $userAdInsRecord = $this->eSignRepository->createUser([
            'xid' => nano_id(),
            'user_id' => $dto->userId,
            'sanf_id' => $dto->sanfId,
            'email' => $dto->email,
            'msisdn' => $msisdn,
            'identity_no' => $dto->identityNo,
            'full_name' => $dto->fullName,
            'date_of_birth' => $dto->dob,
            'place_of_birth' => $dto->pob,
            'gender' => ($dto->gender === self::MALE) ? 'M' : 'F',
            'address' => $dto->address,
            'postal_code' => $dto->postalCode,
            'province' => $dto->province,
            'city' => $dto->city,
            'district' => $dto->district,
            'sub_district' => $dto->subDistrict,
            'selfie_file' => $this->moveFile($dto->selfieFile, config('image-path.temp'), config('image-path.selfie_adins')),
            'identity_file' => $this->moveFile($dto->identityFile, config('image-path.temp'), config('image-path.identity_adins')),
            'password' => $encryptedPassword,
            'status_id' => ESignRegistrationStatusEnum::SUBMIT,
            'created_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ]);

        $dto->msisdn = $msisdn;
        $dto->selfieFile = $this->getBase64Image($userAdInsRecord->selfie_file->path, $userAdInsRecord->selfie_file->mime_type);
        $dto->identityFile = $this->getBase64Image($userAdInsRecord->identity_file->path, $userAdInsRecord->identity_file->mime_type);
        $dto->password = Crypt::decryptString($encryptedPassword);

        $this->adInsRegisterService->execute($dto);

        return $userAdInsRecord;
    }

    private function parseMsisdnWithZeroFormat(string $msisdn): string
    {
        $trimValue = trim($msisdn);

        if (strpos($trimValue, '+62') === 0) {
            $msisdn = '0' . substr($trimValue, 3);
        } elseif (strpos($trimValue, '62') === 0) {
            $msisdn = '0' . substr($trimValue, 2);
        }

        return $msisdn;
    }

    /**
     * @param string $filename
     * @param string $temporaryPath
     * @param string $path
     * @return array
     */
    private function moveFile(string $filename, string $temporaryPath, string $path)
    {
        $fileExistInTempPath = Storage::disk('minio_post')->exists("{$temporaryPath}{$filename}");
        if ($fileExistInTempPath) {
            $fileExistInNewPath = Storage::disk('minio_post')->exists("{$path}{$filename}");
            if ($fileExistInNewPath === false) {
                Storage::disk('minio_post')->move("{$temporaryPath}{$filename}", "{$path}{$filename}");
            }
        }

        $metadata = Storage::disk('minio_post')->getMetaData("{$path}{$filename}");

        return [
            'file_name' => $filename,
            'directory' => $path,
            'path' => "{$path}{$filename}",
            'mime_type' => $metadata['mimetype'],
            'size' => $metadata['size'],
        ];
    }

    private function getBase64Image(string $path, string $mimeType): string
    {
        $image = Storage::disk('minio_post')->get($path);

        $base64 = base64_encode($image);

        return 'data:' . $mimeType . ';base64,' . $base64;
    }
}
