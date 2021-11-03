<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end

class ${NAME}Service
{
    protected ${NAME}RepositoryInterface \$repository;
    
    public function __construct(${NAME}RepositoryInterface \$repository)
    {
        \$this->repository = \$repository;
    }
}