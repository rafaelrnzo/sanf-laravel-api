<?php

namespace Sanf\Core\Modules\Insurance\Services;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Insurance\Dtos\AddInsuranceClaimSubmissionByUserResponseDto;
use Sanf\Core\Modules\Insurance\Dtos\AddInsuranceClaimSubmissionByUserV2RequestDto;
use Sanf\Core\Modules\Insurance\Enums\InsuranceClaimSubmissionStatusEnum;
use Sanf\Core\Modules\Insurance\Events\InsuranceClaimSubmissionAddedEvent;

/**
 * @since CR2025 Copied from packages/sanf/core/src/Modules/Insurance/Services/AddInsuranceClaimSubmissionByUserService.php.
 */
final class AddInsuranceClaimSubmissionByUserV2Service extends InsuranceClaimSubmissionByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddInsuranceClaimSubmissionByUserV2RequestDto $dto
     * @return AddInsuranceClaimSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);

        //TODO REFACTOR
        $imageFiles = [];
        if ($dto->imageFiles) {
            $newPath = config('image-path.insurance');
            $tempPath = config('image-path.temp');

            foreach ($dto->imageFiles as $imageFile) {
                $exist = Storage::disk('minio_post')->exists("{$newPath}{$imageFile}");
                try {
                    if (!$exist) {
                        Storage::disk('minio_post')->move("{$tempPath}{$imageFile}", "{$newPath}{$imageFile}");
                    }

                    $metadata = Storage::disk('minio_post')->getMetadata("{$newPath}{$imageFile}");

                    $imageFiles[] = [
                        'file_name' => $imageFile,
                        'directory' => $metadata['dirname'] ?? $newPath,
                        'path' => $metadata['path'],
                        'mime_type' => $metadata['mimetype'] ?? Storage::disk('minio_post')->getMimeType("{$newPath}{$imageFile}"),
                        'timestamp' => $metadata['timestamp'],
                        'size' => $metadata['size'],
                    ];
                } catch (FileNotFoundException $exception) {
                    report($exception);
                }
            }
        }
        $imagePath = implode('|', array_column($imageFiles, 'file_name'));
        $entity = $this->repository->add([
            'xid' => nano_id(),
            'status_id' => InsuranceClaimSubmissionStatusEnum::PROCESSED,
            'user_id' => $user->id,
            'profile_xid' => $dto->profileXid,
            'contract_no' => $dto->contractNo,
            'serial_no' => $dto->financingUnit->serialNo,
            'polis_no' => $dto->financingUnit->polisNo,
            'brand_type_model' => $dto->financingUnit->brandTypeModel,
            'year' => $dto->financingUnit->year,
            'location_metadata' => $dto->locationMetadata,
            'city_id' => $dto->locationMetadata['city_id'],
            'incident_date' => $dto->incidentDate,
            'image_files' => $imageFiles,
            'image_path' => $imagePath,
            'description' => $dto->description,
            'pic_name' => $dto->picName,
            'pic_phone_number' => $dto->picPhoneNumber,
        ]);

        $entity->user = (object) $user->toArray();
        $entity->profile = $this->findProfileOrFail($dto->profileXid);
        $entity->financingUnit = $dto->financingUnit;
        event(new InsuranceClaimSubmissionAddedEvent($entity));

        return new AddInsuranceClaimSubmissionByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
        ]);
    }
}
