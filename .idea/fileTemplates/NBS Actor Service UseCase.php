<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end

use NbsPhp\Core\Services\ApplicationServiceInterface;

class ${USECASE}${NAME}By${ACTOR}Service extends ${NAME}By${ACTOR}Service implements ApplicationServiceInterface
{
    public function execute(\$dto = null)
    {
        //TODO IMPLEMENTATION
    }
}