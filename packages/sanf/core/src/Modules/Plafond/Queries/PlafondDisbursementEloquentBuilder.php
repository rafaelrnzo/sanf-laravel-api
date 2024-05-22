<?php

namespace Sanf\Core\Modules\Plafond\Queries;

use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondDisbursementRequestDto;

class PlafondDisbursementEloquentBuilder
{
    public function browse(BrowsePlafondDisbursementRequestDto $dto)
    {
        return new BrowsePlafondDisbursementEloquentBuilder($dto);
    }
}
