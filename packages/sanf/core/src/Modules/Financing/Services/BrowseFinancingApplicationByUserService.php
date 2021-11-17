<?php


namespace Sanf\Core\Modules\Financing\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\BrowseFinancingApplicationDto;

class BrowseFinancingApplicationByUserService extends FinancingByUserService implements ApplicationServiceInterface
{
    /**
     * @param BrowseFinancingApplicationDto $dto
     * @return object
     * @throws \NbsPhp\Core\Exceptions\UserNotFoundException
     */
    public function execute($dto = null)
    {
        $this->findUserOrFail($dto->userId);
        $data = $this->financingApplicationRepository->query(
            $this->financingSpecificationFactory->paginateByUser($dto->userId, $dto->skip, $dto->limit, $dto->sortBy, $dto->keyword)
        );
        $total = $this->financingApplicationRepository->size(
            $this->financingSpecificationFactory->paginateByUser($dto->userId, null, null, null, $dto->keyword)
        );

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => (int)$total,
                'count' => collect($data)->count(),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
