<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Repositories\UserAuthLogRepositoryInterface;
use Sanf\Core\Modules\User\Specifications\UserAuthLogSpecificationFactoryInterface;

class BrowseUserDeletionAccountService implements ApplicationServiceInterface
{
    protected UserAuthLogRepositoryInterface $repository;
    protected UserAuthLogSpecificationFactoryInterface $specification;

    public function __construct(
        UserAuthLogRepositoryInterface $repository,
        UserAuthLogSpecificationFactoryInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
    }

    public function execute($dto = null)
    {
        $query = $this->repository->query(
            $this->specification->paginateByExternal(
                $dto->statusId,
                $dto->keyword,
                $dto->limit,
                $dto->skip,
                $dto->sortBy
            )
        );

        $total = $this->repository->size(
            $this->specification->paginateByExternal($dto->keyword, $dto->statusId)
        );

        $mappingData = array_map(function ($item) {
            return (object)[
                'id' => $item->id,
                'xid' => $item->xid,
                'userId' => $item->user_id,
                'restoreExpiredAt' => $item->restore_expired_at,
                'username' => $item->username,
                'fullName' => $item->full_name,
                'landlineNumber' => $item->landline_number,
                'phoneNumber' => $item->phone_number,
                'personalXid' => $item->personal_xid,
                'companyName' => $item->company_name,
                'createdAt' => $item->created_at,
            ];
        }, $query);

        return (object)[
            'data' => $mappingData,
            'paginate' => (object)[
                'total' => $total,
                'count' => count($mappingData),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}