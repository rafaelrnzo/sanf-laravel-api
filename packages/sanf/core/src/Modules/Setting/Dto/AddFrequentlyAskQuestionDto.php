<?php

namespace Sanf\Core\Modules\Setting\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddFrequentlyAskQuestionDto extends CamelCaseDataTransferObject
{
    public int $categoryId;
    public string $title;
    public string $description;
    public bool $isPopular;
    public float $order;
}
