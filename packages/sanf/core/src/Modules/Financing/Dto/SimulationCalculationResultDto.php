<?php


namespace Sanf\Core\Modules\Financing\Dto;


use Spatie\DataTransferObject\DataTransferObject;

class SimulationCalculationResultDto extends DataTransferObject
{
    public int $financing_method_id;

    public string $financing_method_name;

    public float $financing_amount;

    public float $down_payment_percentage;

    public float $down_payment_amount;

    public int $tenor_in_month;

    public float $installment_per_month;

    public float $interest_rate_percentage;

}
