<?php

namespace Sanf\Core\Modules\Prepayment\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Prepayment\Dtos\BrowseContractByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Dtos\BrowseContractByUserResponseDto;
use Sanf\Integration\InternalApiClient;

final class BrowseContractByUserService implements ApplicationServiceInterface
{
    protected InternalApiClient $apiClient;

    /**
     * BrowseContractByUserService constructor.
     * @param InternalApiClient $apiClient
     */
    public function __construct(InternalApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }


    /**
     * @param BrowseContractByUserRequestDto $dto
     * @return BrowseContractByUserResponseDto
     */
    public function execute($dto = null)
    {
        $result = $this->apiClient->getContractOfPrepayment(
            $dto->profileXid,
            $dto->skip,
            $dto->limit,
            $dto->sortBy,
            $dto->timestamp,
            $dto->keyword
        );

        dd($result);


        $data = array_map(function ($item) {
            return (object)[
                'xid' => $item->xid,
                'createdAt' => $item->created_at,
                'updatedAt' => $item->updated_at,
            ];
        }, $result['data']);

        return new BrowseContractByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int)$total,
                'count' => count($data),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sortBy' => $dto->sortBy,
            ]
        ]);
    }
}
