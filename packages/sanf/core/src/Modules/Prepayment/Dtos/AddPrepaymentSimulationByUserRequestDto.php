<?php

namespace Sanf\Core\Modules\Prepayment\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddPrepaymentSimulationByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public \DateTimeImmutable $prepaymentDate;
    public string $contractNo;
    public ?bool $isDownloadPdf;
}
