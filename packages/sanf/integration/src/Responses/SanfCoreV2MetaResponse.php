<?php

namespace Sanf\Integration\Responses;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreV2MetaResponse extends FlexibleDataTransferObject
{
    public int $current_page;
    public int $per_page;
    public int $total;
    public int $last_page;
}