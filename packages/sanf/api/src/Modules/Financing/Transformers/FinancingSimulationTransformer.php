<?php


namespace Sanf\Api\Modules\Financing\Transformers;


use League\Fractal\TransformerAbstract;

class FinancingSimulationTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            "financing_method_id" => (int)$item->financing_method_id,
            "financing_method_name" => (string)$item->financing_method_name,
            "financing_amount" => (float)$item->financing_amount,
            "down_payment_percentage" => (float)$item->down_payment_percentage,
            "down_payment_amount" => (float)$item->down_payment_amount,
            "tenor_in_month" => (float)$item->tenor_in_month,
            "installment_per_month" => (float)$item->installment_per_month,
            "interest_rate_percentage" => (float)$item->interest_rate_percentage,
        ];
    }
}
