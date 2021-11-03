<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end
use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class Browse${NAME}Dto extends CamelCaseDataTransferObject
{
    public ?int \$userId;
    public ?int \$skip = 10;
    public ?int \$limit = 0;
    public ?string \$sortBy;
    public ?string \$keyword;
}