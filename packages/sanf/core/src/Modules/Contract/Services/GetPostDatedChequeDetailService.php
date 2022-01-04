<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\PostDatedChequeDto;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\InternalApiClient;

class GetPostDatedChequeDetailService extends UserService implements ApplicationServiceInterface
{

    protected InternalApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, InternalApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param PostDatedChequeDto $dto
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

        $response = $this->internalApiClient->getPdcDetail(
            $dto->profile_xid,
            $dto->contract_no,
            $dto->limit,
            $dto->skip,
            $dto->sort_by
        );
        $data = collect($response->data)->map(function ($item) {
            return (object)[
                'pdc_no' => $item->PDC_NO ?? null,
                'amount' => $item->PDC_AMT ?? 0,
                'currency_type' => $item->CURR_ID ?? null,
                'submitted_date' => $item->PDC_DUE_DT ?? null,
                'pdc_type' => $item->PDC_TYPE ?? null,
                'status' => (object)[
                    'id' => $item->STATUS_ID ?? null,
                    'name' => $item->STATUS ?? null,
                ]
            ];
        });

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
