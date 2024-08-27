<?php

namespace NbsPhp\Core\Exceptions;

class UndefinedSwitchCaseException extends ApiException
{
    protected $code = 'E_CASE';

    protected $message = 'Undefined switch case value';
}
