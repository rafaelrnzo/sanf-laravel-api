<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

final class PaymentAccDocumentFormRequest extends CamelCaseDataTransferObject
{
    public ?string $name;
    public ?string $origin;
    public ?string $no;
    public ?string $date;
}
