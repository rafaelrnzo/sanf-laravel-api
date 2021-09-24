<?php


namespace Sanf\Core\Modules\Commodity\Exceptions;


use NbsPhp\Core\Exceptions\ApiException;

class InvalidStateCommodityException extends ApiException
{
    protected $code = 'E_COMD_1';

    protected $message = 'Invalid Commodity State';
}
