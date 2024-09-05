<?php

namespace Sanf\Core\Modules\Survey\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Survey\Dtos\PaginateAssigneeSurveyDto;
use Sanf\Core\Modules\Survey\Repositories\SurveyRepositoryInterface;
use Sanf\Core\Modules\Survey\Specifications\SurveySpecificationFactoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetListAssigneeSurveyService extends UserService implements ApplicationServiceInterface
{
    /**
     * @var SanfCoreApiClient
     */
    protected SanfCoreApiClient $internalApiClient;
    protected SurveyRepositoryInterface $surveyRepository;
    protected SurveySpecificationFactoryInterface $specificationFactory;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param SanfCoreApiClient $internalApiClient
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        SanfCoreApiClient $internalApiClient,
        SurveyRepositoryInterface $surveyRepository,
        SurveySpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
        $this->surveyRepository = $surveyRepository;
        $this->specificationFactory = $specificationFactory;
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
        $user = $this->userRepository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        try {
            $response = $this->internalApiClient->getAssigneeSurvey($user->username);

            $surveys = $this->surveyRepository->query($this->specificationFactory->getAll());
            $surveyCollection = collect($surveys);

            $data = collect($response->data)->map(function ($property) use ($surveyCollection) {
                foreach ($property->ITEMS ?? [] as $item) {
                    $items[] = (object) [
                        'code' => $item->DOC_ID_SURVEY ?? null,
                        'title' => $item->DESCRIPTION ?? null,
                    ];
                }

                $isSubmitted = $surveyCollection->where('branch_id', '=', $property->HEADER->BR_ID)
                    ->where('contract_no', '=', $property->HEADER->REG_NO)
                    ->first();

                return (object) [
                    'branch_id' => $property->HEADER->BR_ID ?? null,
                    'profile_xid' => $property->HEADER->CUST_ID ?? null,
                    'contract_no' => $property->HEADER->REG_NO ?? null,
                    'project_name' => $property->HEADER->PROJ_NAME ?? null,
                    'segment' => $property->HEADER->SEGMENT ?? null,
                    'pic_name' => $property->HEADER->PIC_NAME ?? null,
                    'customer_name' => $property->HEADER->CUST_NAME ?? null,
                    'project_location' => $property->HEADER->LOCATION ?? null,
                    'status_id' => $property->HEADER->STATUS_ID ?? null,
                    'status' => $property->HEADER->STATUS ?? null,
                    'items' => $items ?? null,
                    'is_submitted' => isset($isSubmitted),
                ];
            });
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return (object) [
                'data' => [],
                'paginate' => (object) [
                    'total' => 0,
                    'count' => 0,
                    'skip' => 0,
                    'limit' => 0,
                    'sortBy' => '',
                ],
            ];
        }

        return (object) [
            'data' => $data,
            'paginate' => (object) [
                'total' => $response->total ?? $response->count,
                'count' => $response->count ?? 0,
                'skip' => 0,
                'limit' => 0,
                'sort_by' => '',
            ],
        ];
    }
}
