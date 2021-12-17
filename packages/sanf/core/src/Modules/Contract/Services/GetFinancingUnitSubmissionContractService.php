<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\FinancingUnitSubmissionDto;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\InternalApiClient;

class GetFinancingUnitSubmissionContractService extends UserService implements ApplicationServiceInterface
{

    protected InternalApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, InternalApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param FinancingUnitSubmissionDto $dto
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

        $response = $this->internalApiClient->getFinancingUnitSubmissionItem($dto);
        $data = collect($response['data'])->map(function ($item) {
            return (object)[
                'serial_no' => $item['SERIAL_NO'] ?? null,
                'brand_type_model' => $item['BTM'] ?? null,
                'provider_name' => null,
                'year' => $item['YEAR'],
                'location_metadata' => (object)[
                    'city_id' => $item['CITY_ID'],
                    'city_name' => $item['CITY']
                ],
                'status' => (object)[
                    'id' => null,
                    'name' => null
                ],
                'submitted_location_metadata' => (object)[
                    'city_id' => null,
                    'city_name' => null
                ],
            ];
        });

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => $response['total'] ?? $response['count'],
                'count' => $response['count'] ?? 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];
    }
}