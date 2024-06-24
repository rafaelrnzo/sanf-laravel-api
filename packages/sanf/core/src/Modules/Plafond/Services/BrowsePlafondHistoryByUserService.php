<?php

namespace Sanf\Core\Modules\Plafond\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondHistoryByUserRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondHistoryByUserResponseDto;
use Sanf\Core\Modules\Plafond\Entities\GuzzlePlafondHistoryEntity;
use Sanf\Core\Modules\Plafond\Enums\PlafondSubmissionTypeEnum;

final class BrowsePlafondHistoryByUserService extends PlafondByUserService implements ApplicationServiceInterface
{
    /**
     * @param BrowsePlafondHistoryByUserRequestDto $dto
     * @return BrowsePlafondHistoryByUserResponseDto
     */
    public function execute($dto = null)
    {
        $coreResponse = $this->repository->getHistoryByProfile($dto->profileXid);
        $coreResponseMapping = collect($coreResponse)->map(function (GuzzlePlafondHistoryEntity $item) {
            $type = $item->getType();
            $submissionType = (string) $item->getSubmissionType();

            return (object) [
                'xid' => $item->getPlafondId(),
                'type' => (object) [
                    'id' => $type->getId(),
                    'title' => $type->getTitle(),
                    'name' => $type->getName(),
                ],
                'status' => $item->getStatus(),
                'updatedAt' => $item->getUpdatedAt(),
                'currentBalance' => $item->getUsedBalance(),
                'addedBalance' => ($submissionType == PlafondSubmissionTypeEnum::INCREASE) ? $item->getAddedBalance() : '0',
                'submittedBalance' => $item->getAddedBalance(),
                'notes' => explode(',', $item->getNotes()),
            ];
        });

        if (is_null($dto->sortBy) === false && $dto->sortBy === 'oldest') {
            $data = $coreResponseMapping->sortBy('updatedAt');
        } else {
            $data = $coreResponseMapping->sortbyDesc('updatedAt');
        }

        return new BrowsePlafondHistoryByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => count($data),
                'count' => count($data),
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sortBy' => $dto->sortBy,
            ],
        ]);
    }
}
