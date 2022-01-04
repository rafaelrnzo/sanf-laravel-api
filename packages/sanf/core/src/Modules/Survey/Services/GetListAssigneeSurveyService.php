<?php

namespace Sanf\Core\Modules\Survey\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Survey\Dtos\PaginateAssigneeSurveyDto;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

class GetListAssigneeSurveyService extends UserService implements ApplicationServiceInterface
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
     * @param PaginateAssigneeSurveyDto $dto
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
            $response = $this->internalApiClient->getAssigneeSurvey('pt.mitrajayakencanaindah@yahoo.com');
            $data = collect($response->data)->map(function ($property) {
                foreach ($property->ITEMS ?? [] as $item) {
                    $items[] = (object)[
                        'code' => $item->DOC_ID_SURVEY ?? null,
                        'title' => $item->DESCRIPTION ?? null,
                    ];
                }

                return (object)[
                    'branch_id' => $property->BR_ID ?? null,
                    'profile_xid' => $property->CUST_ID ?? null,
                    'contract_no' => $property->REG_NO ?? null,
                    'project_name' => $property->PROJ_NAME ?? null,
                    'segment' => $property->SEGMENT ?? null,
                    'company_name' => $property->COMPANY_NAME ?? null,
                    'customer_name' => $property->CUST_NAME ?? null,
                    'project_location' => $property->LOCATION ?? null,
                    'items' => $items ?? null,
                ];
            });
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return (object)[
                'data' => [],
                'paginate' => (object)[
                    'total' => 0,
                    'count' => 0,
                    'skip' => 0,
                    'limit' => 0,
                    'sortBy' => '',
                ]
            ];
        }

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => $response->total ?? $response->count,
                'count' => $response->count ?? 0,
                'skip' => 0,
                'limit' => 0,
                'sort_by' => '',
            ],
        ];
    }
}
