<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end

use NbsPhp\Core\Services\ApplicationServiceInterface;

class UseCase${NAME}Service extends ${NAME}Service implements ApplicationServiceInterface
{
    public function execute(\$dto = null)
    {
        //TODO IMPLEMENTATION
    }
}