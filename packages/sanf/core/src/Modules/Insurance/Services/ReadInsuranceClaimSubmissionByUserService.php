<?php

namespace Sanf\Core\Modules\Insurance\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Insurance\Dtos\ReadInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Dtos\ReadInsuranceClaimSubmissionByUserResponseDto;
use Sanf\Core\Modules\Insurance\Exceptions\InsuranceClaimSubmissionNotFoundException;

final class ReadInsuranceClaimSubmissionByUserService extends InsuranceClaimSubmissionByUserService implements ApplicationServiceInterface
{
    /**
     * @param ReadInsuranceClaimSubmissionByUserRequestDto $dto
     * @return ReadInsuranceClaimSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $entity = $this->repository->findByXid($dto->xid);
        if (is_null($entity)) {
            throw new InsuranceClaimSubmissionNotFoundException();
        }

        return new ReadInsuranceClaimSubmissionByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
            'userId' => $entity->user_id,
            'status' => $entity->status,
            'serialNo' => $entity->serial_no,
            'polisNo' => $entity->polis_no,
            'year' => $entity->year,
            'brandTypeModel' => $entity->brand_type_model,
            'locationMetadata' => $entity->location_metadata,
            'incidentDate' => CarbonImmutable::make($entity->incident_date),
            'description' => $entity->description,
            'imageFiles' => $entity->image_files,
            'completenessDocuments' => $entity->completeness_documents,
            'completenessNote' => $entity->completeness_note,
            'createdAt' => CarbonImmutable::make($entity->created_at),
            'updatedAt' => CarbonImmutable::make($entity->updated_at),
        ]);
    }
}
