<?php

namespace Sanf\Core\Modules\User\Dto;

use NbsPhp\Core\Dto\AbstractDto;

class OTPRequestDto extends AbstractDto
{
    /** @var int */
    public $userId;

    /** @var string */
    public $purpose;

    /** @var string|null */
    public $code;
}
