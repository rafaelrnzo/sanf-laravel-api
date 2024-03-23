<?php

namespace Sanf\Core\Modules\Plafond\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Repositories\PlafondTypeRepositoryInterface;

final class ListPlafondTypeService implements ApplicationServiceInterface
{
    private PlafondTypeRepositoryInterface $repository;

    public function __construct(PlafondTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $data = $this->repository->types($dto);

        return (object) [
            'data' => $data['lists'],
            'paginate' => (object) [
                'total' => (int) $data['total'],
                'count' => (int) $data['count'],
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];
    }
}
