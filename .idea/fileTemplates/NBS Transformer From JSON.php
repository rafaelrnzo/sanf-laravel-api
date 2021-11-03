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
            #set($json = {
              "xid": "string",
              "application_code": "string",
              "status": {
                "id": "string",
                "name": "string"
              },
            })
            #foreach($key in $json.keySet())
                '$key' => \$item->$key,
            #end
        ];
    }
}