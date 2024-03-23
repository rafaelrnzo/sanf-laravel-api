<?php

namespace Sanf\Core\Modules\Prepayment\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Prepayment\Dtos\BrowseContractByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Dtos\BrowseContractByUserResponseDto;
use Sanf\Core\Modules\Prepayment\Repositories\PrepaymentSubmissionRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class BrowseContractForPrepaymentSubmissionByUserService extends PrepaymentSubmissionByUserService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $apiClient;

    /**
     * BrowseContractForPrepaymentSubmissionByUserService constructor.
     * @param SanfCoreApiClient $apiClient
     */
    public function __construct(
        PrepaymentSubmissionRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        SanfCoreApiClient $apiClient
    ) {
        parent::__construct($repository, $userRepository);
        $this->apiClient = $apiClient;
    }

    /**
     * @param BrowseContractByUserRequestDto $dto
     * @return BrowseContractByUserResponseDto
     */
    public function execute($dto = null)
    {
        try {
            $result = $this->apiClient->getContractOfPrepayment(
                $dto->profileXid,
                $dto->skip,
                $dto->limit,
                $dto->sortBy,
                $dto->timestamp,
                $dto->keyword
            );
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return new BrowseContractByUserResponseDto([
                'data' => [],
                'paginate' => [
                    'total' => 0,
                    'count' => 0,
                    'skip' => (int) $dto->skip,
                    'limit' => (int) $dto->limit,
                    'sortBy' => $dto->sortBy,
                ],
            ]);
        }

        $data = array_map(function ($item) {
            return (object) [
                'contractNo' => $item->AGREE_NO,
                'isSubmitted' => !empty($this->repository->whereContractNo($item->AGREE_NO)),
                'remainingBalance' => $item->PAY_AMT,
                'currencyType' => $item->CURR_ID,
            ];
        }, $result->data);

        return new BrowseContractByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int) ($result->total ?? $result->count),
                'count' => (int) $result->count,
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sortBy' => $dto->sortBy,
            ],
        ]);
    }
}
