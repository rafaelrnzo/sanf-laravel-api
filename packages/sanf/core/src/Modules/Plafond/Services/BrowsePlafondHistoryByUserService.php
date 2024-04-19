<?php

namespace Sanf\Core\Modules\Plafond\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondHistoryByUserRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondHistoryByUserResponseDto;
use Sanf\Core\Modules\Plafond\Entities\GuzzlePlafondHistoryEntity;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;

final class BrowsePlafondHistoryByUserService extends PlafondByUserService implements ApplicationServiceInterface
{
    /**
     * @param BrowsePlafondHistoryByUserRequestDto $dto
     * @return BrowsePlafondHistoryByUserResponseDto
     */
    public function execute($dto = null)
    {
        $plafonds = $this->repository->getHistoryByProfile($dto->profileXid);
        $data = collect($plafonds)->map(function (GuzzlePlafondHistoryEntity $item) {
            $type = $item->getType();
            $notes = $item->getNotes();
            if ($type->getId() === PlafondTypeEnum::FACTORING) {
                $notes = ['PT. Emas Perkasa Gemilang', 'PT. Dominika Permata Digital'];
            }

            return (object) [
                'type' => (object) [
                    'id' => $type->getId(),
                    'title' => $type->getTitle(),
                    'name' => $type->getName(),
                ],
                'status' => $item->getStatus(),
                'updatedAt' => $item->getUpdatedAt(),
                'currentBalance' => $item->getCurrentBalance(),
                'addedBalance' => $item->getAddedBalance(),
                'submittedBalance' => $item->getSubmittedBalance(),
                'notes' => $notes,
            ];
        });

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
