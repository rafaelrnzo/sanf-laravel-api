<?php

namespace Sanf\Core\Modules\Notification\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;

final class BrowseNotificationByUserService extends NotificationByUserService implements ApplicationServiceInterface
{
    /*For Case Browse
    protected NotificationSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        NotificationRepositoryInterface $repository,
        NotificationSpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct($repository);
        $this->specificationFactory = $specificationFactory;
    }
    /*

    /**
     * @param BrowseNotificationByUserRequestDto $dto
     * @return BrowseNotificationByUserResponseDto
     */
    public function execute($dto = null)
    {
        /* For Case Browse
        $result = $this->repository->query(
            $this->specificationFactory->paginate($dto->keyword, $dto->sortBy, $dto->skip, $dto->limit)
        );
        $total = $this->repository->size(
            $this->specificationFactory->paginate($dto->keyword)
        );

        $data = array_map(function ($item) {
            return (object)[
                'xid' => $item->xid,
                'createdAt' => $item->created_at,
                'updatedAt' => $item->updated_at,
            ];
        }, $result);

        return new BrowseNotificationByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int)$total,
                'count' => count($data),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sortBy' => $dto->sortBy,
            ]
        ]);
        */

        /* For Case Read/Add/Update
        $entity = $this->repository->findByXid($dto->xid);
        if (is_null($entity)) {
            throw new NotificationNotFoundException();
        }
        return new BrowseNotificationByUserResponseDto([
            'id' => $entity->getId(),
        ]);
        */
    }
}
