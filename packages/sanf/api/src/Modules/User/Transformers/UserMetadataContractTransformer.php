<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;

class UserMetadataContractTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'total_active_contract' => (int) $item->total_active_contract,
            'total_finished_contract' => (int) $item->total_finished_contract,
        ];
    }
}
