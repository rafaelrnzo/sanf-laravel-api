<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end
use Spatie\DataTransferObject\DataTransferObject;

class ${NAME}Dto extends DataTransferObject
{
}