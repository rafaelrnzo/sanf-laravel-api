<?php
#parse("PHP File Header.php")

namespace ${VENDOR}\Core\Modules\\${MODULE}\Transformers;

use League\Fractal\TransformerAbstract;

class ${NAME}Transformer extends TransformerAbstract
{
    public function transform(\$item)
    {
        return [
            'id' => \$item->id,
        ];
    }
}