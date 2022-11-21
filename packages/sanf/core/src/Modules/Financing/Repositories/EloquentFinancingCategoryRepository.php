<?php

namespace Sanf\Core\Modules\Financing\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Financing\Models\FinancingCategoryModel;

class EloquentFinancingCategoryRepository extends AbstractEloquentRepository implements FinancingCategoryRepositoryInterface
{
    private FinancingCategoryModel $financingCategoryModel;

    public function __construct(FinancingCategoryModel $financingCategoryModel)
    {
        $this->financingCategoryModel = $financingCategoryModel;
    }

    public function query($specification): array
    {
        $records = $specification->buildQuery($this->financingCategoryModel)->get();

        return $this->stripEloquentModel($records);
    }

    public function size($specification): int
    {
        return $specification->buildQuery($this->financingCategoryModel)->count();
    }
}
