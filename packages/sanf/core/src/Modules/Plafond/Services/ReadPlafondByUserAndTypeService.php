<?php

namespace Sanf\Core\Modules\Plafond\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\ReadPlafondByProfileAndTypeRequestDto;
use Sanf\Core\Modules\Plafond\Entities\PlafondHistoryEntity;

final class ReadPlafondByUserAndTypeService extends PlafondByUserService implements ApplicationServiceInterface
{
    /**
     * @param ReadPlafondByProfileAndTypeRequestDto $dto
     * @return object
     */
    public function execute($dto = null)
    {
        $plafond = $this->repository->getByProfileAndType($dto->profileXid, $dto->typeId);

        $type = $plafond->getType();
        $data = (object)[
            'id' => $plafond->getId(),
            'remainingBalance' => $plafond->getRemainingBalance(),
            'usedBalance' => $plafond->getUsedBalance(),
            'updatedAt' => $plafond->getUpdatedAt(),
            'type' => (object)[
                'id' => $type->getId(),
                'title' => $type->getTitle(),
                'name' => $type->getName(),
            ],
            'histories' => collect($plafond->getHistories())->map(function (PlafondHistoryEntity $item) {
                return (object)[
                    'status' => $item->getStatus(),
                    'updatedAt' => $item->getUpdatedAt(),
                    'currentBalance' => $item->getCurrentBalance(),
                    'addedBalance' => $item->getAddedBalance(),
                    'submittedBalance' => $item->getSubmittedBalance(),
                ];
            }),
        ];

        return $data;
    }
}
