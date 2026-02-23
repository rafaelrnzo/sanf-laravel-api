<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCorePlafondSparePartEntity;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class PlafondSparePartTransformer extends TransformerAbstract
{
    public function transform(SanfCorePlafondSparePartEntity $data)
    {
        return [
            'xid' => $data->no_plafond,
            'submit_balance' => $total = (float) $data->p_total,
            'remaining_balance' => $total - (float) $data->p_sisa,
            'supplier' => [
                'id' => $data->tipe_supplier,
                'name' => $data->nama_supplier,
            ],
            'expired_at' => Carbon::parse($data->exp_date, SanfCoreApiClientV2::DEFAULT_TIMEZONE)->endOfDay()->timestamp,
        ];
    }
}
