<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\SummaryBillContractDto;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\InternalApiClient;

class SummaryBillContractService extends UserService implements ApplicationServiceInterface
{

    protected InternalApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, InternalApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param SummaryBillContractDto $dto
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

        $response = $this->internalApiClient->getFinancingUnitInvoice(
            $user->personal_xid,
            $dto->contract_no,
            $dto->limit,
            $dto->skip,
            $dto->sort_by
        );
        $data = collect($response->data)->map(function ($item) {
            return (object)[
                'due_at' => $item->TGL_JATUHTEMPO ?? null,
                'bill_amount' => $item->TAGIHAN ?? 0,
                'penalty_amount' => $item->DENDA_PENALTY ?? 0,
                'currency_type' => $item->CURR_ID ?? null,
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