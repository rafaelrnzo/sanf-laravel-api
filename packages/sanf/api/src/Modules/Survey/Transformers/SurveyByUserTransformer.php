<?php

namespace Sanf\Api\Modules\Survey\Transformers;

use League\Fractal\TransformerAbstract;

class SurveyByUserTransformer extends TransformerAbstract
{
    public function transform($data)
    {
        return [
            'branch_id' => $data->branch_id,
            'profile_xid' => $data->profile_xid,
            'contract_no' => $data->contract_no,
            'project_name' => $data->project_name,
            'segment' => $data->segment,
            'company_name' => $data->company_name,
            'customer_name' => $data->customer_name,
            'project_location' => $data->project_location,
            'items' => fractal($data->items, SurveyItemTransformer::class),
        ];
    }
}
