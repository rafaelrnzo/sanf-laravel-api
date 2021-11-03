<?php
#parse("PHP File Header.php")

namespace ${VENDOR}\Core\Modules\\${MODULE}\Specifications;

interface ${NAME}SpecificationFactoryInterface
{
    public function paginate(?int \$skip, ?int \$limit, ?string \$sortBy, ?string \$keyword);
}
