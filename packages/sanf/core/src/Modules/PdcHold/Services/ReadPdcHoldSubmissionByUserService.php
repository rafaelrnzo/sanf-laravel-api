<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\PdcHold\Dtos\ReadPdcHoldSubmissionByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\ReadPdcHoldSubmissionByUserResponseDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Exceptions\PdcHoldNotFoundException;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldRepositoryInterface;

/**
 * @since CR2025
 */
final class ReadPdcHoldSubmissionByUserService implements ApplicationServiceInterface
{
    protected PdcHoldRepositoryInterface $pdcHoldRepository;

    public function __construct(PdcHoldRepositoryInterface $pdcHoldRepository)
    {
        $this->pdcHoldRepository = $pdcHoldRepository;
    }

    /**
     * @param ReadPdcHoldSubmissionByUserRequestDto $dto
     * @return ReadPdcHoldSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $entity = $this->pdcHoldRepository->findByXid($dto->xid);
        if (is_null($entity)) {
            throw new PdcHoldNotFoundException();
        }

        $pdcType = new PdcHoldTypeEnum($entity->type);
        $giros = $entity->type === PdcHoldTypeEnum::RESUME ? $entity->resume_giros : $entity->giros;

        return new ReadPdcHoldSubmissionByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
            'status'  => (object) [
                'id' => $entity->status_id,
                'name' => $entity->status,
            ],
            'type' => $pdcType,
            'dateStart' => CarbonImmutable::make($entity->date_start),
            'dateEnd' => CarbonImmutable::make($entity->date_end),
            'giros' => $giros,
            'createdAt' => CarbonImmutable::make($entity->created_at),
            'updatedAt' => CarbonImmutable::make($entity->updated_at),
        ]);
    }
}
