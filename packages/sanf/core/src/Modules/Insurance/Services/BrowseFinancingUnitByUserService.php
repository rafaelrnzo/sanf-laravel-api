<?php

namespace Sanf\Core\Modules\Insurance\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Insurance\Dtos\BrowseFinancingUnitByUserResponseDto;
use Sanf\Core\Modules\Invoice\Dtos\BrowseFinancingUnitByUserRequestDto;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

final class BrowseFinancingUnitByUserService implements ApplicationServiceInterface
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
     * @param BrowseFinancingUnitByUserRequestDto $dto
     * @return BrowseFinancingUnitByUserResponseDto
     */
    public function execute($dto = null)
    {
        try {
            $result = $this->apiClient->getFinancingUnitOfInsurance(
                $dto->profileXid,
                $dto->skip,
                $dto->limit,
                $dto->sortBy,
                $dto->timestamp,
                $dto->keyword
            );
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return new BrowseFinancingUnitByUserResponseDto([
                'data' => [],
                'paginate' => [
                    'total' => 0,
                    'count' => 0,
                    'skip' => (int)$dto->skip,
                    'limit' => (int)$dto->limit,
                    'sortBy' => $dto->sortBy,
                ]
            ]);
        }


        $data = array_map(function ($item) {
            return (object)[
                'contractNo' => $item->AGREE_NO,
                'polisNo' => $item->POLIS_NO,
                'serialNo' => $item->SERIAL_NO,
                'brandTypeModel' => $item->BTM,
                'year' => $item->YEAR ?? '',
            ];
        }, $result->data);

        return new BrowseFinancingUnitByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int)$result->count,
                'count' => count($data),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sortBy' => $dto->sortBy,
            ]
        ]);
    }
}
