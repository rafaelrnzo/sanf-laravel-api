<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\FinancingUnitContractDto;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

class GetFinancingUnitContractService extends UserService implements ApplicationServiceInterface
{

    protected InternalApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, InternalApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param FinancingUnitContractDto $dto
     * @return object
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }
        try {
            $response = $this->internalApiClient->getFinancingUnitItem(
                $user->personal_xid,
                $dto->contract_no,
                $dto->limit,
                $dto->skip,
                $dto->sort_by
            );
            $data = collect($response->data)->map(function ($item) {
                return (object)[
                    'serial_no' => $item->SERIAL_NO ?? null,
                    'brand_type_model' => $item->BTM ?? null,
                    'year' => $item->YEAR ?? null,
                    'provider_name' => $item->SUPPLIER ?? null,
                ];
            });
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return (object)[
                'data' => [],
                'paginate' => (object)[
                    'total' => 0,
                    'count' => 0,
                    'skip' => (int)$dto->skip,
                    'limit' => (int)$dto->limit,
                    'sortBy' => $dto->sort_by,
                ]
            ];
        }

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => $response->total ?? $response->count,
                'count' => $response->count ?? 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];
    }

}
