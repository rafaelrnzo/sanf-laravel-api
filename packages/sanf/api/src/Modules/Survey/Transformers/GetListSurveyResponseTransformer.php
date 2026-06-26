<?php

namespace Sanf\Api\Modules\Survey\Transformers;

use League\Fractal\TransformerAbstract;

class GetListSurveyResponseTransformer extends TransformerAbstract
{
    public function transform($data)
    {
        return [
            'branch_id' => $data->branch_id,
            'profile_xid' => $data->profile_xid,
            'contract_no' => $data->contract_no,
            'project_name' => $data->project_name,
            'project_id' => $data->project_id,
            'segment' => $data->segment,
            'pic_name' => $data->pic_name,
            'customer_name' => $data->customer_name,
            'status_id' => $data->status_id,
            'status' => $data->status,
            'project_location' => $data->project_location,
            'is_submitted' => $data->is_submitted,
        ];
    }
}
