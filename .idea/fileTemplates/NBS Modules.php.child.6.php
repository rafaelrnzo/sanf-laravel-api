<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end

use NbsPhp\Core\Models\AbstractModel;

class ${NAME}Model extends AbstractModel
{
    protected \$table = '${NAME}';
}