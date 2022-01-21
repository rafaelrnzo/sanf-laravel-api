<?php

namespace Sanf\Core\Modules\Survey\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

class GetListSurveyService extends UserService implements ApplicationServiceInterface
{
    /**
     * @var InternalApiClient
     */
    protected InternalApiClient $internalApiClient;


    /**
     * @param AuthModel $userRepository
     * @param InternalApiClient $internalApiClient
     */
    public function __construct(AuthModel $userRepository, InternalApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }


    /**
     * @param null $dto
     * @return object
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        try {
            $response = $this->internalApiClient->getSurveys(
                $user->username,
                $dto->limit,
                $dto->skip,
                Str::title($dto->sortBy),
                $dto->statusId
            );

            $data = collect($response->data)->map(function ($property) {
                return (object)[
                    'branch_id' => $property->BR_ID ?? null,
                    'profile_xid' => $property->CUST_ID ?? null,
                    'contract_no' => $property->REG_NO ?? null,
                    'segment' => $property->SEGMENT ?? null,
                    'pic_name' => $property->PIC_NAME ?? null,
                    'customer_name' => $property->CUST_NAME ?? null,
                    'project_location' => $property->LOCATION ?? null,
                    'status_id' => $property->STATUS_ID ?? null,
                    'status' => $property->STATUS ?? null,
                ];
            });
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return (object)[
                'data' => [],
                'paginate' => (object)[
                    'total' => 0,
                    'count' => 0,
                    'skip' => $dto->skip,
                    'limit' => $dto->limit,
                    'sortBy' => $dto->sortBy,
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
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
