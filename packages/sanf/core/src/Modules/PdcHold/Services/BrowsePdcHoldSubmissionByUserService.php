<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\PdcHold\Dtos\BrowsePdcHoldSubmissionByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\BrowsePdcHoldSubmissionByUserResponseDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldRepositoryInterface;
use Sanf\Core\Modules\PdcHold\Specifications\PdcHoldSubmissionSpecificationFactoryInterface;

/**
 * @since CR2025
 */
final class BrowsePdcHoldSubmissionByUserService implements ApplicationServiceInterface
{
    protected PdcHoldRepositoryInterface $pdcHoldRepository;
    protected PdcHoldSubmissionSpecificationFactoryInterface $pdcHoldSubmissionSpecificationFactory;

    public function __construct(
        PdcHoldRepositoryInterface $pdcHoldRepository,
        PdcHoldSubmissionSpecificationFactoryInterface $pdcHoldSubmissionSpecificationFactory
    ) {
        $this->pdcHoldRepository = $pdcHoldRepository;
        $this->pdcHoldSubmissionSpecificationFactory = $pdcHoldSubmissionSpecificationFactory;
    }

    /**
     * @param BrowsePdcHoldSubmissionByUserRequestDto $dto
     * @return BrowsePdcHoldSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $result = $this->pdcHoldRepository->query(
            $this->pdcHoldSubmissionSpecificationFactory->paginateByUserAndProfile($dto->userId, $dto->profileXid, $dto->statusId, $dto->type, $dto->sortBy, $dto->skip, $dto->limit)
        );
        $total = $this->pdcHoldRepository->size(
            $this->pdcHoldSubmissionSpecificationFactory->paginateByUserAndProfile($dto->userId, $dto->profileXid, $dto->statusId, $dto->type)
        );

        $data = array_map(function ($item) {
            $pdcType = new PdcHoldTypeEnum($item->type);

            return (object) [
                'id' => $item->id,
                'xid' => $item->xid,
                'status' => (object) [
                    'id' => $item->status_id,
                    'name' => $item->status,
                ],
                'type' => $pdcType,
                'dateStart' => $item->date_start,
                'dateEnd' => $item->date_end,
                'giroCount' => $item->giros_count,
                'createdAt' => $item->created_at,
                'updatedAt' => $item->updated_at,
            ];
        }, $result);

        return new BrowsePdcHoldSubmissionByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int) $total,
                'count' => count($data),
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sortBy' => $dto->sortBy,
            ],
        ]);
    }
}
