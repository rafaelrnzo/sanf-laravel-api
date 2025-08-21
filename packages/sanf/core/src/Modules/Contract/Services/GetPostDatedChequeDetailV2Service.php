<?php

namespace Sanf\Core\Modules\Contract\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\PostDatedChequeV2Dto;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Services\UserService;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

/**
 * From SANF Core.
 *
 * @since CR2025
 */
class GetPostDatedChequeDetailV2Service extends UserService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $internalApiClient;

    public function __construct(UserRepositoryInterface $userRepository, SanfCoreApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param PostDatedChequeV2Dto $dto
     * @return object
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->findById($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        try {
            $response = $this->internalApiClient->getPdcGiroByContractV2(
                $dto->profile_xid,
                $dto->contract_no,
                optional($dto->date_start)->format('Y-m-d'),
                optional($dto->date_end)->format('Y-m-d'),
                $dto->status_id,
                $dto->limit,
                $dto->skip,
                $dto->sort_by
            );

            $data = collect($response->data)->map(function ($item) {
                return (object) [
                    'pdc_no' => $item->PDC_NO ?? null,
                    'contract_no' => $item->AGREE_NO ?? null,
                    'amount' => $item->PDC_AMT ?? 0,
                    'currency_type' => $item->CURR_ID ?? null,
                    'submitted_date' => $item->PDC_DUE_DT ?? null,
                    'pdc_type' => $item->PDC_TYPE ?? null,
                    'status' => (object) [
                        'id' => $item->STATUS_ID ?? null,
                        'name' => $item->STATUS ?? null,
                    ],
                ];
            });
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return (object) [
                'data' => [],
                'paginate' => (object) [
                    'total' => 0,
                    'count' => 0,
                    'skip' => (int) $dto->skip,
                    'limit' => (int) $dto->limit,
                    'sortBy' => $dto->sort_by,
                ],
            ];
        }

        return (object) [
            'data' => $data,
            'paginate' => (object) [
                'total' => $response->total ?? $response->count,
                'count' => $response->count ?? 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];
    }
}
