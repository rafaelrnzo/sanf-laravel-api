<?php
#parse("PHP File Header.php")

namespace ${VENDOR}\Core\Modules\\${MODULE}\Specifications;

class Eloquent${NAME}SpecificationFactory implements ${NAME}SpecificationFactoryInterface
{
    public function paginate(?int \$skip, ?int \$limit, ?string \$sortBy, ?string \$keyword)
    {
        return new EloquentPaginate${NAME}Specification(\$skip, \$limit, \$sortBy, \$keyword);
    }
}
