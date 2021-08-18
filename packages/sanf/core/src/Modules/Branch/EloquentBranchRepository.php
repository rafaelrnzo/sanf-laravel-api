<?php

namespace Sanf\Core\Modules\Branch;


class EloquentBranchRepository implements BranchRepositoryInterface
{
    /** @var \Sanf\Core\Modules\Branch\BranchModel $model */
    protected BranchModel $model;

    public function __construct(BranchModel $model)
    {
        $this->model = $model;
    }

    public function list($limit, $offset)
    {
        return $this->model
            ->newQuery()
            ->select([
                'id',
                'name',
                'address',
                'msisdn',
                'msisdn_alternative',
                'email',
                'latitude',
                'longitude',
            ])
            ->limit($limit)
            ->offset($offset)
            ->get();
    }
}
