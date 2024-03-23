<?php

namespace Sanf\Core\Modules\Plafond\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondByProfileRequestDto;
use Sanf\Core\Modules\Plafond\Entities\GuzzlePlafondEntity;

final class BrowsePlafondByUserService extends PlafondByUserService implements ApplicationServiceInterface
{
    /**
     * @param BrowsePlafondByProfileRequestDto $dto
     * @return object
     */
    public function execute($dto = null)
    {
        $plafonds = $this->repository->getByProfile($dto->profileXid);
        $data = collect($plafonds)->map(function (GuzzlePlafondEntity $item) {
            $type = $item->getType();

            return (object) [
                'id' => $item->getId(),
                'remainingBalance' => $item->getRemainingBalance(),
                'usedBalance' => $item->getUsedBalance(),
                'type' => (object) [
                    'id' => $type->getId(),
                    'title' => $type->getTitle(),
                    'name' => $type->getName(),
                ],
                'updatedAt' => $item->getUpdatedAt(),
            ];
        });

        return (object) [
            'data' => $data, //TODO DTO
            'paginate' => (object) [
                'total' => $data->count(),
                'count' => $data->count(),
                'skip' => (int) ($dto->skip ?? null),
                'limit' => (int) ($dto->limit ?? null),
                'sort_by' => $dto->sort_by ?? null,
            ],
        ];
    }
}
