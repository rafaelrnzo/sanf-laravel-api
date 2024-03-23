<?php

namespace NbsPhp\Core\Dto;

class ReadByOwnerRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $xid;
}
