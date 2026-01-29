<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreESignDocumentCategoryEntity extends FlexibleDataTransferObject
{
    public ?string $DOC_ID;
    public ?string $DOC_DESC;
}
