<?php


namespace Sanf\Core\Modules\Project;


use NbsPhp\Core\Exceptions\ApiException;

class GeneralProjectException extends ApiException
{
    protected $code = 'E_PROJ_1';

    protected $message = 'General Project Error';
}
