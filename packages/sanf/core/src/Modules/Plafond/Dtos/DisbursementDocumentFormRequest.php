<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

final class DisbursementDocumentFormRequest extends CamelCaseDataTransferObject
{
    public ?string $name;
    public ?string $origin;
}
