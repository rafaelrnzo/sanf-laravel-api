<?php


namespace Sanf\Core\Modules\Financing\Dto;


use Spatie\DataTransferObject\DataTransferObject;

class SendEmailFinancingSimulationDto extends DataTransferObject
{
    public int $user_id;

    public int $financing_method_id;

    public string $financing_method_name;

    public float $financing_amount;

    public int $down_payment_percentage;

    public float $down_payment_amount;

    public int $tenor_in_month;

    public float $installment_per_month;

    public int $interest_rate_percentage;
}
