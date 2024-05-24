<?php

namespace Sanf\Core\Modules\Plafond\Queries;

use Sanf\Core\Modules\Plafond\Dtos\BrowsePlafondDisbursementRequestDto;
use Sanf\Core\Modules\Plafond\Dtos\ReadPlafondDisbursementRequestDto;

class PlafondDisbursementEloquentBuilder
{
    public function browse(BrowsePlafondDisbursementRequestDto $dto)
    {
        return new BrowsePlafondDisbursementEloquentBuilder($dto);
    }

    public function read(ReadPlafondDisbursementRequestDto $dto)
    {
        return new ReadPlafondDisbursementEloquentBuilder($dto);
    }
}
