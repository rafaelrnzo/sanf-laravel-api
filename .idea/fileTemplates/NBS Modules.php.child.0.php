<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end
use NbsPhp\Core\Exceptions\ApiException;

class ${NAME}InvalidException Extends ApiException
{
    protected \$code = 'E_${NAME}_1';
    
    protected \$message = '${NAME} Invalid';
}