<?php

namespace Sanf\Core\Modules\Setting\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class UpdateFrequentlyAskQuestionCategoryDto extends CamelCaseDataTransferObject
{
    public string $xid;
    public ?string $name;
}
