<?php

namespace Sanf\Core\Modules\Plafond\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondFactoringByUserResponseDto;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondFactoringRequestDto;
use Sanf\Core\Modules\Plafond\Entities\GuzzleCustomerPlafondFactoringEntity;
use Sanf\Core\Modules\Plafond\Entities\GuzzlePlafondFactoringEntity;

final class BrowsePlafondFactoringService extends PlafondByUserService implements ApplicationServiceInterface
{
    /**
     * @param BrowsePlafondFactoringRequestDto $dto
     * @return BrowsePlafondFactoringByUserResponseDto
     */
    public function execute($dto = null)
    {
        $plafonds = $this->repository->getPlafondFactoringByProfile($dto->profileXid);

        $data = collect($plafonds)->map(function (GuzzlePlafondFactoringEntity $item) {
            return (object) [
                'xid' => $item->getPlafondId(),
                'submitAmount' => $item->getCurrentBalance(),
                'remainingAmount' => $item->getRemainingBalance(),
                'usedAmount' => $item->getUsedBalance(),
                'customers' => array_map(function (GuzzleCustomerPlafondFactoringEntity $customer) {
                    return (object) [
                        'id' => $customer->getId(),
                        'name' => $customer->getName(),
                        'code' => $customer->getCode(),
                        'email' => $customer->getEmail(),
                    ];
                }, $item->getCustomers()),
                'customerReview' => $item->getCustomerReview() == 'Y',
                'expiredAt' => $item->getExpiredAt(),
            ];
        });

        return new BrowsePlafondFactoringByUserResponseDto([
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
