<?php


namespace Sanf\Api\Modules\Branch;


use League\Fractal\TransformerAbstract;

class ListBranchTransformer extends TransformerAbstract
{

    public function transform($dto)
    {
        return [
            'branches' => fractal($dto->list, new DetailBranchTransformer()),
        ];
    }
}
