<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end

use NbsPhp\Core\Models\AbstractModel;

class ${NAME}Model extends AbstractModel
{
    #set( $regex = "([a-z])([A-Z]+)")
    #set( $replacement = "$1_$2")
    #set( $toSnakeCase = $NAME.replaceAll($regex, $replacement).toLowerCase())
    protected \$table = '$toSnakeCase';
}