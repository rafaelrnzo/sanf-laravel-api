<?php

namespace Sanf\Core\Modules\Plafond\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\PlafondRepositoryInterface;

class ListPlafondTypeService implements ApplicationServiceInterface
{

    private PlafondRepositoryInterface $repository;

    public function __construct(PlafondRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $data = $this->repository->types($dto);

        return (object)[
            'data' => $data['lists'],
            'paginate' => (object)[
                'total' => (int)$data['total'],
                'count' => (int)$data['count'],
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];
    }
}