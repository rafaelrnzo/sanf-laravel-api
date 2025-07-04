<?php

namespace Sanf\Core\Modules\Plafond\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondFactoringRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondFactoringV2ByUserResponseDto;
use Sanf\Core\Modules\Plafond\Entities\GuzzlePlafondFactoringV2Entity;
use Sanf\Core\Modules\Plafond\Entities\GuzzleVirtualAccountEntity;

final class BrowsePlafondFactoringV2Service extends PlafondByUserService implements ApplicationServiceInterface
{
    /**
     * @param BrowsePlafondFactoringRequestDto $dto
     * @return BrowsePlafondFactoringV2ByUserResponseDto
     */
    public function execute($dto = null)
    {
        $plafonds = $this->repository->getPlafondFactoringV2ByProfile($dto->profileXid);

        $data = collect($plafonds)->map(function (GuzzlePlafondFactoringV2Entity $item) {
            return (object) [
                'plafondNo' => $item->getPlafondNo(),
                'custId' => $item->getCustId(),
                'plafondCode' => $item->getPlafondCode(),
                'totalAmount' => $item->getTotalAmount(),
                'usedAmount' => $item->getUsedAmount(),
                'remainingAmount' => $item->getRemainingAmount(),
                'expiredDate' => $item->getExpiredDate(),
                'pksNo' => $item->getPksNo(),
                'pksDate' => $item->getPksDate(),
                'customerReview' => $item->getCustomerReview() === 'Y',
                'customer' => (object) [
                    'id' => $item->getCustomer()->getId(),
                    'name' => $item->getCustomer()->getName(),
                    'email' => $item->getCustomer()->getEmail(),
                    'code' => $item->getCustomer()->getCode(),
                ],
                'virtualAccounts' => array_map(function (GuzzleVirtualAccountEntity $va) {
                    return (object) [
                        'custId' => $va->getCustId(),
                        'currencyId' => $va->getCurrencyId(),
                        'description' => $va->getDescription(),
                        'vaId' => $va->getVaId(),
                        'accountName' => $va->getAccountName(),
                    ];
                }, $item->getVirtualAccounts()),
            ];
        });

        return new BrowsePlafondFactoringV2ByUserResponseDto([
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
