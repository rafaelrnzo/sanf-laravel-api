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

class GetDetailSurveyByUserService extends UserService implements ApplicationServiceInterface
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
                $dto->contractNo
            );

            $surveyData = $response->data;

            foreach ($surveyData->ITEMS ?? [] as $item) {
                foreach ($item->IMAGES ?? [] as $file) {
                    $imagesFiles[] = (object)[
                        'file_name' => $file->FILE_NAME ?? null,
                        'origin_name' => $file->ORIGIN_NAME ?? null,
                        'url' => $file->URL ?? null,
                    ];
                }

                $items[] = (object)[
                    'code' => $item->DOC_ID_SURVEY ?? null,
                    'title' => $item->DESCRIPTION ?? null,
                    'description' => $item->NOTE ?? null,
                    'image_files' => $imagesFiles ?? null
                ];
            }

            $data = (object)[
                'branch_id' => $surveyData->BR_ID ?? null,
                'profile_xid' => $surveyData->CUST_ID ?? null,
                'contract_no' => $surveyData->REG_NO ?? null,
                'project_name' => $surveyData->PROJ_NAME ?? null,
                'segment' => $surveyData->SEGMENT ?? null,
                'company_name' => $surveyData->COMPANY_NAME ?? null,
                'customer_name' => $surveyData->CUST_NAME ?? null,
                'project_location' => $surveyData->LOCATION ?? null,
                'items' => $items ?? null,
            ];
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
