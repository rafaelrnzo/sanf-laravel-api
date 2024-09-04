<?php

namespace Sanf\Core\Modules\Location;

use Sanf\Core\Encryptions\SodiumEncryption;

class GetListLocationService
{
    protected $repository;

    public function __construct(LocationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto)
    {
        $data = SodiumEncryption::query()->transaction(function () use ($dto) {
            return $this->repository->list($dto);
        });

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
