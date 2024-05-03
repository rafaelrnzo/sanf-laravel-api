<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

final class DisbursementBowheerFormRequest extends CamelCaseDataTransferObject
{
    public string $id;
    public string $name;
    public string $email;
    public string $code;
}
