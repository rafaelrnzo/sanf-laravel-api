<?php

namespace ${VENDOR}\Core\Modules\\${MODULE}\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ${NAME}InvalidException Extends ApiException
{
    protected \$code = 'E_${NAME.toUpperCase()}_1';

    protected \$message = '${NAME.toUpperCase()} Invalid';
}
