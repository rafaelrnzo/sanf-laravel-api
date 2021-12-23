<?php

namespace Sanf\Core\Modules\Prepayment\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Prepayment\Dtos\BrowseContractByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Dtos\BrowseContractByUserResponseDto;
use Sanf\Core\Modules\Prepayment\Repositories\PrepaymentSubmissionRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\InternalApiClient;

final class BrowseContractForPrepaymentSubmissionByUserService extends PrepaymentSubmissionByUserService implements ApplicationServiceInterface
{
    protected InternalApiClient $apiClient;

    /**
     * BrowseContractForPrepaymentSubmissionByUserService constructor.
     * @param InternalApiClient $apiClient
     */
    public function __construct(
        PrepaymentSubmissionRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        InternalApiClient $apiClient
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
        $result = $this->apiClient->getContractOfPrepayment(
            $dto->profileXid,
            $dto->skip,
            $dto->limit,
            $dto->sortBy,
            $dto->timestamp,
            $dto->keyword
        );
        $data = array_map(function ($item) {
            return (object)[
                'contractNo' => $item->AGREE_NO,
                'isSubmitted' => !empty($this->repository->whereContractNo($item->AGREE_NO)),
                'remainingBalance' => $item->PAY_AMT,
                'currencyType' => $item->CURR_ID,
            ];
        }, $result->data);

        return new BrowseContractByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int)$result->count,
                'count' => count($data),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sortBy' => $dto->sortBy,
            ]
        ]);
    }
}
