<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\ContractPostDatedChequeDto;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\InternalApiClient;

class ListContractPostDatedChequeService extends UserService implements ApplicationServiceInterface
{

    protected InternalApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, InternalApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param ContractPostDatedChequeDto $dto
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

        $response = $this->internalApiClient->getPdc(
            $user->personal_xid,
            $dto->limit,
            $dto->skip,
            $dto->sort_by,
            $dto->contract_no
        );
        $data = collect($response->data)->map(function ($item) {
            return (object)[
                'contract_no' => $item->NO_KONTRAK ?? null,
                'currency_type' => $item->CURR_ID ?? null,
                'created_at' => $item->TGL_PDC ?? null
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