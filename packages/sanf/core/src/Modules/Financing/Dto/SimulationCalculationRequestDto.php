<?php


namespace Sanf\Core\Modules\Financing\Dto;


use Spatie\DataTransferObject\DataTransferObject;

class SimulationCalculationRequestDto extends DataTransferObject
{
    public ?int $financing_method_id;
    public ?int $financing_amount;
    public ?int $down_payment_percentage;
    public ?int $down_payment_amount;
    public ?int $tenor_in_month;
    public ?bool $is_send_email;
    public ?bool $is_download_pdf;
}
