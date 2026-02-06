<?php

namespace Sanf\Integration\Responses;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreV2MetaResponse extends FlexibleDataTransferObject
{
    use CastsNumericDtoProperties;

    public ?int $current_page;
    public ?int $per_page;
    public ?int $total;
    public ?int $last_page;
}
