<?php


namespace Sanf\Core\Modules\Staff;


use NbsPhp\Core\Exceptions\ApiException;

class GeneralStaffException extends ApiException
{
    protected $code = 'E_STAFF_1';

    protected $message = 'General Staff Error';
}
