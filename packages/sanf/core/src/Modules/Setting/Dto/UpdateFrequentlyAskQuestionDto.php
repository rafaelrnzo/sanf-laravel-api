<?php

namespace Sanf\Core\Modules\Setting\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class UpdateFrequentlyAskQuestionDto extends CamelCaseDataTransferObject
{
    public string $xid;
    public int $categoryId;
    public string $title;
    public string $description;
    public bool $isPopular;
    public float $order;
}
