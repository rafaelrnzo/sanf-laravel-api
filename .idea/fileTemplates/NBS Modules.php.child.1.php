<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end

use League\Fractal\TransformerAbstract;

class ${NAME}Transformer extends TransformerAbstract
{
    public function transform(\$item)
    {    
        return [
            "id" => \$item->id,
        ];
    }
}