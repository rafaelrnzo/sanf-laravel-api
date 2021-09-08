<?php

namespace Sanf\Core\Modules\News;



class GetListNewsService
{

    protected $repository;

    public function __construct(NewsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto)
    {
        $data = $this->repository->list($dto);

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