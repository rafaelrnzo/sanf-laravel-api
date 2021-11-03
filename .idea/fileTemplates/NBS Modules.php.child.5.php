<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end
use NbsPhp\Core\Repositories\AbstractEloquentRepository;

class Eloquent${NAME}Repository extends AbstractEloquentRepository implements ${NAME}RepositoryInterface
{
    protected \$model;
    
    public function __construct(${NAME}Model \$model)
    {
        \$this->model = \$model;
    }
}