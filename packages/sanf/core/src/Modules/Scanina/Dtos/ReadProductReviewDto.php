<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ReadProductReviewDto extends FlexibleDataTransferObject
{

    public ?int $total;
    public ?int $withImages;
    public ?array $customerSatisfied;
    public ?array $customerUnstatisfied;
    public ?object $ratingProgress;
}
