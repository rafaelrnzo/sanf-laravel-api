<?php


namespace Sanf\Core\Modules\Staff;


use NbsPhp\Core\Exceptions\ApiException;

class StaffException extends ApiException
{
    protected $code = 'E_STAFF_1';

    protected $message = 'Staff Error';

}
