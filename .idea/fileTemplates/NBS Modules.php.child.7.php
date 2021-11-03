<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end

use MyCLabs\Enum\Enum;

class ${NAME}StatusEnum extends Enum
{
    const ENABLED = 10; 
    const DISABLED = 20; 
}
