<?php


namespace Sanf\Integration\Exceptions;


use NbsPhp\Core\Exceptions\ApiException;

class SanfInternalApiException extends ApiException
{
    protected $code = 'E_SANF_1';
}
