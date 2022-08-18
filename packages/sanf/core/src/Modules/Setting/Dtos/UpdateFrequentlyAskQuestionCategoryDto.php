<?php

namespace Sanf\Core\Modules\Setting\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class UpdateFrequentlyAskQuestionCategoryDto extends CamelCaseDataTransferObject
{
    public string $xid;
    public ?string $name;
}
